<template>
    <div>
        <h1>Cart</h1>

        <div v-if="cartItems.length === 0">
            Cart is empty
        </div>

        <div v-else>
            <div v-for="item in cartItems" :key="item.id"
                style="display:flex; gap:12px; border-bottom:1px solid #ccc; padding:10px; align-items:center;">
                <img :src="getImage(item.id)" alt="product"
                    style="width:100px; height:80px; object-fit:cover; border-radius:6px;" />

                <div style="flex:1;">
                    <h3>{{ item.name }}</h3>
                    <p>RM {{ item.price }}</p>

                    <button @click="decrease(item)">-</button>
                    <span>{{ item.quantity }}</span>
                    <button @click="increase(item)">+</button>

                    <button @click="remove(item)">Remove</button>
                </div>

                <p>
                    Subtotal: RM {{ item.price * item.quantity }}
                </p>
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
            cart: {},
            increaseDebounceTimers: {},
            decreaseDebounceTimers: {},
            pendingQuantities: {}
        }
    },

    // computed = derived state
    computed: {
        cartItems() {
            return Object.keys(this.cart).map(id => {
                let item = { id, ...this.cart[id] }
                if (this.pendingQuantities && this.pendingQuantities[id] !== undefined) {
                    return { ...item, quantity: this.pendingQuantities[id] }
                }
                return item
            })
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

        increase(item) {
            // Set the pending quantity to current or last pending
            const current = this.pendingQuantities[item.id] !== undefined ? this.pendingQuantities[item.id] : item.quantity
            this.pendingQuantities[item.id] = current + 1

            if (this.increaseDebounceTimers[item.id]) {
                clearTimeout(this.increaseDebounceTimers[item.id])
            }
            this.increaseDebounceTimers[item.id] = setTimeout(async () => {
                const qty = this.pendingQuantities[item.id]
                await axios.post(`/api/cart/update/${item.id}`, {
                    quantity: qty
                })
                this.fetchCart()
                this.increaseDebounceTimers[item.id] = null
                setTimeout(() => {
                    delete this.pendingQuantities[item.id];
                }, 500); 
                window.dispatchEvent(new Event('cart-updated'))
            }, 400)
        },

        decrease(item) {
            if (item.quantity <= 1 && (!this.pendingQuantities || this.pendingQuantities[item.id] <= 1)) return

            // Set the pending quantity to current or last pending
            const current = this.pendingQuantities[item.id] !== undefined ? this.pendingQuantities[item.id] : item.quantity
            const next = current - 1
            if (next < 1) return
            this.pendingQuantities[item.id] = next

            if (this.decreaseDebounceTimers[item.id]) {
                clearTimeout(this.decreaseDebounceTimers[item.id])
            }
            this.decreaseDebounceTimers[item.id] = setTimeout(async () => {
                const qty = this.pendingQuantities[item.id]
                await axios.post(`/api/cart/update/${item.id}`, {
                    quantity: qty
                })
                this.fetchCart()
                this.decreaseDebounceTimers[item.id] = null
                setTimeout(() => {
                    delete this.pendingQuantities[item.id];
                }, 500); 
                window.dispatchEvent(new Event('cart-updated'))
            }, 400)
        },

        async remove(item) {
            if (!confirm('Are you sure you want to remove ' + item.name + ' from cart?')) {
                return
            }
            await axios.post(`/api/cart/remove/${item.id}`)
            this.fetchCart()
        },

        async checkout() {
            if (!confirm('Proceed to checkout?')) {
                return
            }

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
                    alert('Checkout failed: ' + (err.response?.data?.message || err.message))
                }
            }
        },

        getImage(id) {
            return `https://picsum.photos/seed/${id}/200/150`
        }
    }
}
</script>