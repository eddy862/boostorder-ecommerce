<template>
  <div class="space-y-6">
    <section
      class="rounded-[2rem] bg-gradient-to-br from-orange-400 via-orange-300 to-amber-200 px-6 py-8 text-white shadow-xl shadow-orange-200 sm:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-100">Your Cart</p>
          <h1 class="mt-2 text-3xl font-black sm:text-4xl">Review everything before checkout.</h1>
          <p class="mt-3 max-w-2xl text-sm leading-7 text-orange-50">
            Adjust quantities, remove products, and place your order when the cart looks right.
          </p>
        </div>
        <div class="rounded-3xl bg-white/20 px-5 py-4 backdrop-blur">
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-100">Cart Total</p>
          <p class="mt-2 text-3xl font-black">RM {{ formatPrice(total) }}</p>
          <p class="text-sm text-orange-50">{{ cartItems.length }} item(s)</p>
        </div>
      </div>
    </section>

    <section class="rounded-[1.75rem] border border-orange-100 bg-white p-6 shadow-sm">
      <div class="mb-6">
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">Shopping Cart</p>
        <h2 class="mt-1 text-2xl font-black text-gray-800">Items ready for checkout</h2>
      </div>

      <div v-if="cartItems.length === 0" class="py-16 text-center text-lg font-semibold text-gray-400">
        <svg class="mx-auto mb-4 h-16 w-16 text-orange-200" fill="none" stroke="currentColor" stroke-width="1.5"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
        </svg>
        Your cart is empty
      </div>

      <div v-else>
        <div v-for="item in cartItems" :key="item.id"
          class="group flex items-center gap-4 border-b border-orange-100 py-5 last:border-b-0 sm:gap-6">
          <img v-if="item.image_url" :src="item.image_url" :alt="item.name"
            class="h-[88px] w-[110px] rounded-2xl border border-orange-100 object-cover shadow-sm" />
          <div v-else
            class="flex h-[88px] w-[110px] items-center justify-center rounded-2xl border border-orange-100 bg-orange-50 text-sm text-orange-400">
            No image
          </div>

          <div class="min-w-0 flex-1">
            <h3 class="mb-1 truncate text-lg font-black text-gray-800">{{ item.name }}</h3>
            <p class="mb-3 text-lg font-black text-orange-500">RM {{ formatPrice(item.price) }}</p>

            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-orange-50 px-2 py-1">
              <button @click="decrease(item)"
                class="flex h-8 w-8 items-center justify-center rounded-full border border-orange-300 bg-white text-orange-500 hover:bg-orange-50 disabled:opacity-50"
                :disabled="item.quantity <= 1">-</button>
              <span class="min-w-10 text-center font-black">{{ item.quantity }}</span>
              <button @click="increase(item)"
                class="flex h-8 w-8 items-center justify-center rounded-full border border-orange-300 bg-white text-orange-500 hover:bg-orange-50">+</button>
            </div>

            <button @click="remove(item)" class="mt-1 text-xs font-medium text-red-500 hover:underline">Remove</button>
          </div>

          <div class="min-w-[120px] text-right">
            <span class="block text-sm text-gray-500">Subtotal</span>
            <span class="text-lg font-black text-orange-500">RM {{ formatPrice(item.price * item.quantity) }}</span>
          </div>
        </div>

        <div
          class="mt-8 flex flex-col items-center justify-between gap-4 rounded-[1.5rem] border border-orange-100 bg-orange-50/60 p-5 sm:flex-row">
          <h2 class="text-xl font-black text-gray-800">Total: <span class="text-orange-500">RM {{ formatPrice(total)
              }}</span></h2>
          <button @click="checkout"
            class="rounded-xl bg-orange-400 px-8 py-3 text-lg font-semibold text-white shadow transition hover:bg-orange-500 disabled:bg-gray-300 disabled:text-gray-500">
            Place Order
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      cart: {},
      increaseDebounceTimers: {},
      decreaseDebounceTimers: {},
      pendingQuantities: {}
    }
  },

  computed: {
    cartItems() {
      return Object.keys(this.cart).map(id => {
        const item = { id, ...this.cart[id] }
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
      this.$router.replace({ query: {} })
    }
  },

  methods: {
    async fetchCart() {
      const res = await axios.get('/api/cart')
      this.cart = res.data
    },

    increase(item) {
      const current = this.pendingQuantities[item.id] !== undefined ? this.pendingQuantities[item.id] : item.quantity
      this.pendingQuantities[item.id] = current + 1

      if (this.decreaseDebounceTimers[item.id]) {
        clearTimeout(this.decreaseDebounceTimers[item.id])
        this.decreaseDebounceTimers[item.id] = null
      }

      if (this.increaseDebounceTimers[item.id]) {
        clearTimeout(this.increaseDebounceTimers[item.id])
      }

      this.increaseDebounceTimers[item.id] = setTimeout(async () => {
        if (this.decreaseDebounceTimers[item.id]) return
        const qty = this.pendingQuantities[item.id]
        await axios.post(`/api/cart/update/${item.id}`, { quantity: qty })
        this.fetchCart()
        this.increaseDebounceTimers[item.id] = null
        setTimeout(() => {
          delete this.pendingQuantities[item.id]
        }, 500)
        window.dispatchEvent(new Event('cart-updated'))
      }, 400)
    },

    decrease(item) {
      if (item.quantity <= 1 && (!this.pendingQuantities || this.pendingQuantities[item.id] <= 1)) return

      const current = this.pendingQuantities[item.id] !== undefined ? this.pendingQuantities[item.id] : item.quantity
      const next = current - 1
      if (next < 1) return
      this.pendingQuantities[item.id] = next

      if (this.increaseDebounceTimers[item.id]) {
        clearTimeout(this.increaseDebounceTimers[item.id])
        this.increaseDebounceTimers[item.id] = null
      }

      if (this.decreaseDebounceTimers[item.id]) {
        clearTimeout(this.decreaseDebounceTimers[item.id])
      }

      this.decreaseDebounceTimers[item.id] = setTimeout(async () => {
        const qty = this.pendingQuantities[item.id]
        await axios.post(`/api/cart/update/${item.id}`, { quantity: qty })
        this.fetchCart()
        this.decreaseDebounceTimers[item.id] = null
        setTimeout(() => {
          delete this.pendingQuantities[item.id]
        }, 500)
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
        alert('Order placed! Order id: ' + res.data.order.id)
        this.fetchCart()
        window.dispatchEvent(new Event('cart-updated'))
      } catch (err) {
        if (err.response && err.response.status === 401) {
          window.location.href = '/login?redirect=/cart?checkout=1'
        } else {
          console.error(err)
          alert('Checkout failed: ' + (err.response?.data?.message || err.message))
        }
      }
    },

    formatPrice(price) {
      return Number(price).toFixed(2)
    }
  }
}
</script>
