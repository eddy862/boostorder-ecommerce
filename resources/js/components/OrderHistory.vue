<template>
    <div>
        <h1>My Orders</h1>

        <div v-if="orders.length === 0">
            No orders yet
        </div>

        <div v-else>
            <div v-for="order in orders" :key="order.id" style="border:1px solid #ccc; margin:10px; padding:10px;">

                <h3>Order #{{ order.id }}</h3>
                <p>Status: {{ order.status }}</p>
                <p>Total: RM {{ order.total_price }}</p>

                <!-- Change status -->
                <select v-model="order.status" @change="updateStatus(order)">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <h4>Items:</h4>
                <ul>
                    <li v-for="item in order.items" :key="item.id">
                        {{ item.product.name }}
                        (x{{ item.quantity }}) - RM {{ item.price }}
                    </li>
                </ul>

            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            orders: []
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
        }
    }
}
</script>