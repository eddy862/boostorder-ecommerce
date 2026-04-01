<template>
    <div>
        <h1>Cart</h1>

        <div v-if="cartItems.length === 0">
            Cart is empty
        </div>

        <div v-else>
            <div v-for="item in cartItems" :key="item.id" style="border-bottom:1px solid #ccc; padding:10px;">
                <h3>{{ item.name }}</h3>
                <p>RM {{ item.price }}</p>

                <!-- Quantity Controls -->
                <button @click="decrease(item)">-</button>
                <span>{{ item.quantity }}</span>
                <button @click="increase(item)">+</button>

                <!-- Remove -->
                <button @click="remove(item.id)">Remove</button>
            </div>

            <h2>Total: RM {{ total }}</h2>

            <button @click="checkout">Place Order</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
axios.defaults.withCredentials = true

export default {
    data() {
        return {
            cart: {}
        }
    },

    // computed = derived state
    computed: {
        cartItems() {
            return Object.keys(this.cart).map(id => ({
                id,
                ...this.cart[id]
            }))
        },

        total() {
            return this.cartItems.reduce((sum, item) => {
                return sum + item.price * item.quantity
            }, 0)
        }
    },

    mounted() {
        this.fetchCart()

        if (this.$route.query.checkout) {
            this.checkout()

            // remove the query param so that if user refreshes, it won't auto checkout again
            this.$router.replace({ query: {} })
        }
    },

    methods: {
        async fetchCart() {
            const res = await axios.get('/api/cart')
            console.log(res.data)
            this.cart = res.data
        },

        async increase(item) {
            await axios.post(`/api/cart/update/${item.id}`, {
                quantity: item.quantity + 1
            })
            this.fetchCart()
        },

        async decrease(item) {
            if (item.quantity <= 1) return

            await axios.post(`/api/cart/update/${item.id}`, {
                quantity: item.quantity - 1
            })
            this.fetchCart()
        },

        async remove(id) {
            await axios.post(`/api/cart/remove/${id}`)
            this.fetchCart()
        },

        async checkout() {
            try {
                const res = await axios.post('/api/checkout')
                console.log(res.data)

                alert('Order placed! Order id: ' + res.data.order.id)

                // refresh cart (now empty)
                this.fetchCart()
            } catch (err) {
                if (err.response && err.response.status === 401) {
                    // redirect to login
                    window.location.href = '/login?redirect=/cart?checkout=1'
                } else {
                    console.error(err)
                    alert('Error placing order')
                }
            }
        }
    }
}
</script>