<template>
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-6 mt-6">
        <h1 class="text-2xl font-bold text-orange-500 mb-6">My Orders</h1>

        <div v-if="orders.length === 0" class="text-center text-gray-400 py-16 text-lg font-semibold">
            <svg class="mx-auto mb-4 w-16 h-16 text-orange-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m13-9l2 9m-5-9V6a2 2 0 10-4 0v4"/></svg>
            No orders yet
        </div>

        <div v-else>
            <div class="mb-6 flex flex-wrap gap-2">
                <button @click="selectedStatus = 'all'"
                    :class="selectedStatus === 'all' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
                    class="px-4 py-2 rounded-lg font-semibold border border-orange-200 transition">All</button>
                <button @click="selectedStatus = 'pending'"
                    :class="selectedStatus === 'pending' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
                    class="px-4 py-2 rounded-lg font-semibold border border-orange-200 transition">Pending</button>
                <button @click="selectedStatus = 'completed'"
                    :class="selectedStatus === 'completed' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
                    class="px-4 py-2 rounded-lg font-semibold border border-orange-200 transition">Completed</button>
                <button @click="selectedStatus = 'cancelled'"
                    :class="selectedStatus === 'cancelled' ? 'bg-orange-400 text-white' : 'bg-orange-50 text-orange-500'"
                    class="px-4 py-2 rounded-lg font-semibold border border-orange-200 transition">Cancelled</button>
            </div>

            <p v-if="filteredOrders.length === 0" class="text-center text-gray-400 py-8 text-base font-medium">
                No orders found for status "{{ selectedStatus.charAt(0).toUpperCase() + selectedStatus.slice(1) }}"
            </p>

            <div v-else>
                <div v-for="order in filteredOrders" :key="order.id" class="border border-orange-100 rounded-lg p-5 mb-6 shadow-sm flex flex-col">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-2">
                        <h3 class="font-semibold text-lg text-gray-800">Order #{{ order.id }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold"
                            :class="{
                                'bg-orange-100 text-orange-500': order.status === 'pending',
                                'bg-green-100 text-green-600': order.status === 'completed',
                                'bg-red-100 text-red-500': order.status === 'cancelled'
                            }"
                        >
                            {{ order.status.charAt(0).toUpperCase() + order.status.slice(1) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <label class="text-xs text-gray-500 font-medium mr-2">Change status:</label>
                        <select v-model="order.status" @change="updateStatus(order)"
                            class="border border-orange-200 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-orange-300">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Items:</h4>
                        <div>
                            <div v-for="item in order.items" :key="item.id" class="flex gap-4 items-center mb-3">
                                <img :src="getImage(item.product.id)" alt="product"
                                    class="w-[80px] h-[60px] object-cover rounded-lg border border-orange-100" />
                                <div>
                                    <p class="font-semibold text-gray-800 mb-0.5">{{ item.product.name }}</p>
                                    <p class="text-gray-500 text-sm mb-0.5">Qty: x{{ item.quantity }}</p>
                                    <p class="text-orange-500 font-bold text-sm mb-0.5">RM {{ item.price }}</p>
                                    <p class="text-gray-500 text-xs">Subtotal: RM {{ item.price * item.quantity }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center mt-4">
                        <span class="text-gray-500 text-base font-medium">Total:&nbsp;</span>
                        <span class="text-orange-500 font-bold text-lg">RM {{ order.total_price }}</span>
                    </div>
                </div>
            </div>
        </div>
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
    },

    methods: {
        async fetchOrders() {
            const res = await axios.get('/api/orders')
            console.log(res.data)
            this.orders = res.data
        },

        async updateStatus(order) {
            const res = await axios.post(`/api/orders/${order.id}/status`, {
                status: order.status
            })
            console.log(res.data)

            alert('Status updated')
        },

        getImage(id) {
            return `https://picsum.photos/seed/${id}/200/150`
        }
    }
}
</script>