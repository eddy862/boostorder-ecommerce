<template>
  <div class="space-y-6">
    <section
      class="rounded-[2rem] bg-gradient-to-br from-orange-400 via-orange-300 to-amber-200 px-6 py-8 text-white shadow-xl shadow-orange-200 sm:px-8">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-100">Order History</p>
          <h1 class="mt-2 text-3xl font-black sm:text-4xl">Track every order from one timeline.</h1>
          <p class="mt-3 max-w-2xl text-sm leading-7 text-orange-50">
            Review pending, completed, and cancelled orders and keep tabs on what you've already purchased.
          </p>
        </div>
        <div class="rounded-3xl bg-white/20 px-5 py-4 backdrop-blur">
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-100">Orders</p>
          <p class="mt-2 text-3xl font-black">{{ orders.length }}</p>
          <p class="text-sm text-orange-50">in your history</p>
        </div>
      </div>
    </section>

    <section class="rounded-[1.75rem] border border-orange-100 bg-white p-6 shadow-sm">
      <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">My Orders</p>
          <h2 class="mt-1 text-2xl font-black text-gray-800">Status overview</h2>
        </div>

        <div class="flex flex-wrap gap-2">
          <button @click="selectedStatus = 'all'"
            :class="selectedStatus === 'all' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
            class="rounded-xl border border-orange-200 px-4 py-2 font-semibold transition">All</button>
          <button @click="selectedStatus = 'pending'"
            :class="selectedStatus === 'pending' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
            class="rounded-xl border border-orange-200 px-4 py-2 font-semibold transition">Pending</button>
          <button @click="selectedStatus = 'completed'"
            :class="selectedStatus === 'completed' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
            class="rounded-xl border border-orange-200 px-4 py-2 font-semibold transition">Completed</button>
          <button @click="selectedStatus = 'cancelled'"
            :class="selectedStatus === 'cancelled' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
            class="rounded-xl border border-orange-200 px-4 py-2 font-semibold transition">Cancelled</button>
        </div>
      </div>

      <div v-if="orders.length === 0" class="py-16 text-center text-lg font-semibold text-gray-400">
        <svg class="mx-auto mb-4 h-16 w-16 text-orange-200" fill="none" stroke="currentColor" stroke-width="1.5"
          viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        No orders yet
      </div>

      <div v-else>
        <p v-if="filteredOrders.length === 0" class="py-8 text-center text-base font-medium text-gray-400">
          No orders found for status "{{ selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1) }}"
        </p>

        <div v-else>
          <div v-for="order in filteredOrders" :key="order.id"
            class="mb-6 flex flex-col rounded-[1.5rem] border border-orange-100 bg-orange-50/40 p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-400">Order</p>
                <h3 class="text-xl font-black text-gray-800">#{{ order.id }}</h3>
              </div>
              <span class="rounded-full px-3 py-1 text-xs font-bold" :class="{
                'bg-orange-100 text-orange-500': order.status === 'pending',
                'bg-green-100 text-green-600': order.status === 'completed',
                'bg-red-100 text-red-500': order.status === 'cancelled'
              }">
                {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
              </span>
            </div>

            <div>
              <h4 class="mb-3 font-semibold uppercase tracking-[0.18em] text-orange-400">Items</h4>
              <div>
                <div v-for="item in order.items" :key="item.id"
                  class="mb-3 flex items-center gap-4 rounded-2xl bg-white px-4 py-3 last:mb-0">
                  <img v-if="item.product.image_url" :src="item.product.image_url" :alt="item.product.name"
                    class="h-[60px] w-[80px] rounded-lg border border-orange-100 object-cover" />
                  <div v-else
                    class="flex h-[60px] w-[80px] items-center justify-center rounded-lg border border-orange-100 bg-orange-50 text-xs text-orange-400">
                    No image
                  </div>
                  <div>
                    <p class="mb-0.5 font-semibold text-gray-800">{{ item.product.name }}</p>
                    <p class="mb-0.5 text-sm text-gray-500">Qty: x{{ item.quantity }}</p>
                    <p class="mb-0.5 text-sm font-bold text-orange-500">RM {{ formatPrice(item.price) }}</p>
                    <p class="text-xs text-gray-500">Subtotal: RM {{ formatPrice(item.price * item.quantity) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-5 flex items-center rounded-2xl border border-orange-100 bg-white px-4 py-4"
              :class='{ "justify-between": order.status === "pending", "justify-end": order.status !== "pending" }'>
              <div class="text-base font-medium text-gray-500">
                Total:&nbsp;
                <span class="text-lg font-black text-orange-500">RM {{ formatPrice(order.total_price) }}</span>
              </div>
              <button v-if="order.status === 'pending'" @click="cancelOrder(order)"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-500 transition hover:bg-red-100">
                Cancel Order
              </button>
            </div>
          </div>
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
      orders: [],
      selectedStatus: 'all'
    }
  },

  computed: {
    filteredOrders() {
      if (this.selectedStatus === 'all') {
        return this.orders
      }
      return this.orders.filter(o => o.status === this.selectedStatus)
    }
  },

  mounted() {
    this.fetchOrders()

    if (window.Echo?.channel) {
      window.Echo.channel('orders')
        .listen('.order.updated', (e) => {
          console.log('Received order update event:', e)
          this.fetchOrders()
        })
    }
  },

  methods: {
    async fetchOrders() {
      const res = await axios.get('/api/orders')
      this.orders = res.data
    },

    async cancelOrder(order) {
      if (!confirm(`Cancel Order #${order.id}?`)) {
        return
      }

      const res = await axios.post(`/api/orders/${order.id}/status`, {
        status: 'cancelled'
      })

      order.status = res.data.order.status
      alert('Order cancelled')
    },

    formatPrice(price) {
      return Number(price).toFixed(2)
    }
  }
}
</script>
