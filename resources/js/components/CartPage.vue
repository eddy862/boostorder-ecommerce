<template>
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6 mt-6">
        <h1 class="text-2xl font-bold text-orange-500 mb-6">Shopping Cart</h1>

        <div v-if="cartItems.length === 0" class="text-center text-gray-400 py-16 text-lg font-semibold">
            <svg class="mx-auto mb-4 w-16 h-16 text-orange-200" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            Your cart is empty
        </div>

        <div v-else>
            <div v-for="item in cartItems" :key="item.id"
                class="flex gap-4 sm:gap-6 border-b border-orange-100 py-5 items-center group">
                <img :src="getImage(item.id)" alt="product"
                    class="w-[100px] h-[80px] object-cover rounded-lg border border-orange-100 shadow-sm" />

                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 text-lg mb-1 truncate">{{ item.name }}</h3>
                    <p class="text-orange-500 font-bold mb-2">RM {{ item.price }}</p>

                    <div class="flex items-center gap-2 mb-2">
                        <button @click="decrease(item)"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-orange-300 bg-white text-orange-500 hover:bg-orange-50 disabled:opacity-50"
                            :disabled="item.quantity <= 1">-</button>
                        <span class="px-3 font-semibold">{{ item.quantity }}</span>
                        <button @click="increase(item)"
                            class="w-8 h-8 flex items-center justify-center rounded-full border border-orange-300 bg-white text-orange-500 hover:bg-orange-50">+</button>
                    </div>

                    <button @click="remove(item)"
                        class="text-xs text-red-500 hover:underline font-medium mt-1">Remove</button>
                </div>

                <div class="text-right min-w-[120px]">
                    <span class="block text-gray-500 text-sm">Subtotal</span>
                    <span class="text-orange-500 font-bold">RM {{ item.price * item.quantity }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-8 gap-4">
                <h2 class="text-xl font-bold text-gray-700">Total: <span class="text-orange-500">RM {{ total }}</span>
                </h2>
                <button @click="checkout"
                    class="px-8 py-3 rounded-lg font-semibold text-white bg-orange-400 hover:bg-orange-500 transition text-lg shadow disabled:bg-gray-300 disabled:text-gray-500">Place
                    Order</button>
            </div>
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

            // Cancel any pending decrease debounce for this item
            if (this.decreaseDebounceTimers[item.id]) {
                clearTimeout(this.decreaseDebounceTimers[item.id])
                this.decreaseDebounceTimers[item.id] = null
            }

            if (this.increaseDebounceTimers[item.id]) {
                clearTimeout(this.increaseDebounceTimers[item.id])
            }
            this.increaseDebounceTimers[item.id] = setTimeout(async () => {
                // If a decrease was clicked after this increase, skip this update to prevent race conditions
                if (this.decreaseDebounceTimers[item.id]) return;
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

            // Cancel any pending increase debounce for this item
            if (this.increaseDebounceTimers[item.id]) {
                clearTimeout(this.increaseDebounceTimers[item.id])
                this.increaseDebounceTimers[item.id] = null
            }

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
            window.dispatchEvent(new Event('cart-updated'))
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
                window.dispatchEvent(new Event('cart-updated'))
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