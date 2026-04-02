<template>
  <section class="space-y-6">
    <div class="flex flex-col gap-3 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700 px-6 py-8 text-white shadow-xl shadow-slate-300 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-300">Admin Dashboard</p>
        <h1 class="mt-2 text-3xl font-black sm:text-4xl">Order Review</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-200">
          Review pending and cancelled customer orders, and complete the ones that are ready to fulfill.
        </p>
      </div>
      <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-orange-200">
        {{ filteredOrders.length }} visible
      </span>
    </div>

    <div class="flex flex-wrap gap-2">
      <button
        @click="selectedStatus = 'all'"
        class="rounded-xl border px-4 py-2 text-sm font-semibold transition"
        :class="selectedStatus === 'all' ? 'border-orange-400 bg-orange-400 text-white' : 'border-orange-200 bg-white text-orange-500 hover:bg-orange-50'"
      >
        All
      </button>
      <button
        @click="selectedStatus = 'pending'"
        class="rounded-xl border px-4 py-2 text-sm font-semibold transition"
        :class="selectedStatus === 'pending' ? 'border-orange-400 bg-orange-400 text-white' : 'border-orange-200 bg-white text-orange-500 hover:bg-orange-50'"
      >
        Pending
      </button>
      <button
        @click="selectedStatus = 'cancelled'"
        class="rounded-xl border px-4 py-2 text-sm font-semibold transition"
        :class="selectedStatus === 'cancelled' ? 'border-orange-400 bg-orange-400 text-white' : 'border-orange-200 bg-white text-orange-500 hover:bg-orange-50'"
      >
        Cancelled
      </button>
      <button
        @click="selectedStatus = 'completed'"
        class="rounded-xl border px-4 py-2 text-sm font-semibold transition"
        :class="selectedStatus === 'completed' ? 'border-orange-400 bg-orange-400 text-white' : 'border-orange-200 bg-white text-orange-500 hover:bg-orange-50'"
      >
        Completed
      </button>
    </div>

    <div
      v-if="message"
      class="rounded-2xl border px-4 py-3 text-sm font-medium"
      :class="messageType === 'error' ? 'border-red-200 bg-red-50 text-red-600' : 'border-green-200 bg-green-50 text-green-700'"
    >
      {{ message }}
    </div>

    <div class="rounded-3xl border border-orange-100 bg-white p-6 shadow-sm">
      <div v-if="loading" class="py-12 text-center text-lg font-semibold text-orange-400">
        Loading orders...
      </div>

      <div v-else-if="filteredOrders.length === 0" class="py-12 text-center text-gray-400">
        No orders found for this status.
      </div>

      <div v-else class="space-y-5">
        <article
          v-for="order in filteredOrders"
          :key="order.id"
          class="rounded-2xl border border-orange-100 bg-orange-50/40 p-5"
        >
          <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
              <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-gray-800">Order #{{ order.id }}</h2>
                <span
                  class="rounded-full px-3 py-1 text-xs font-bold"
                  :class="{
                    'bg-orange-100 text-orange-500': order.status === 'pending',
                    'bg-red-100 text-red-500': order.status === 'cancelled',
                    'bg-green-100 text-green-600': order.status === 'completed'
                  }"
                >
                  {{ capitalize(order.status) }}
                </span>
              </div>
              <p class="mt-2 text-sm text-gray-500">
                {{ order.user?.name || 'Unknown User' }} - {{ order.user?.email || 'No email' }}
              </p>
            </div>

            <div class="flex items-center gap-3">
              <div class="text-right">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Total</p>
                <p class="text-lg font-black text-orange-500">RM {{ formatPrice(order.total_price) }}</p>
              </div>
              <button
                v-if="order.status === 'pending'"
                @click="completeOrder(order)"
                :disabled="processingId === order.id"
                class="rounded-xl bg-orange-400 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-500 disabled:cursor-not-allowed disabled:bg-orange-200"
              >
                {{ processingId === order.id ? 'Completing...' : 'Mark Completed' }}
              </button>
            </div>
          </div>

          <div class="mt-4 space-y-3">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center gap-4 rounded-2xl bg-white px-4 py-3"
            >
              <img
                :src="getImage(item.product.id)"
                alt="product"
                class="h-[60px] w-[80px] rounded-lg border border-orange-100 object-cover"
              />
              <div class="min-w-0 flex-1">
                <p class="truncate font-semibold text-gray-800">{{ item.product.name }}</p>
                <p class="text-sm text-gray-500">Qty: x{{ item.quantity }}</p>
              </div>
              <div class="text-right text-sm">
                <p class="font-semibold text-orange-500">RM {{ formatPrice(item.price) }}</p>
                <p class="text-gray-500">Subtotal: RM {{ formatPrice(item.price * item.quantity) }}</p>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      loading: true,
      orders: [],
      selectedStatus: 'all',
      processingId: null,
      message: '',
      messageType: 'success',
    }
  },

  computed: {
    filteredOrders() {
      if (this.selectedStatus === 'all') {
        return this.orders
      }

      return this.orders.filter(order => order.status === this.selectedStatus)
    },
  },

  mounted() {
    this.fetchOrders()
  },

  methods: {
    async fetchOrders() {
      this.loading = true

      try {
        const res = await axios.get('/api/admin/orders')
        this.orders = res.data
      } finally {
        this.loading = false
      }
    },

    async completeOrder(order) {
      if (!confirm(`Mark Order #${order.id} as completed?`)) {
        return
      }

      this.processingId = order.id
      this.message = ''

      try {
        const res = await axios.post(`/api/orders/${order.id}/status`, {
          status: 'completed',
        })

        order.status = res.data.order.status
        this.message = `Order #${order.id} marked as completed.`
        this.messageType = 'success'
      } catch (error) {
        this.message = error.response?.data?.message || 'Unable to update the order right now.'
        this.messageType = 'error'
      } finally {
        this.processingId = null
      }
    },

    formatPrice(price) {
      return Number(price).toFixed(2)
    },

    capitalize(value) {
      return value.charAt(0).toUpperCase() + value.slice(1)
    },

    getImage(id) {
      return `https://picsum.photos/seed/${id}/200/150`
    },
  },
}
</script>
