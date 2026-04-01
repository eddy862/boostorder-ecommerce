<template>
  <div>
    <h1>Product List</h1>

    <div v-if="products.length === 0">
      Loading...
    </div>

    <div v-else>
      <div v-for="product in products" :key="product.id">
        <h3>{{ product.name }}</h3>
        <p>RM {{ product.price }}</p>
        <p>Stock: {{ product.stock }}</p>
        <button @click="addToCart(product.id)">Add to Cart</button>
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
      products: []
    }
  },

  mounted() {
    this.fetchProducts()
  },

  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products')
        console.log(response.data)
        this.products = response.data
      } catch (error) {
        console.error(error)
      }
    },

    async addToCart(productId) {
      try {
        const response = await axios.post(`/api/cart/add/${productId}`)
        console.log(response.data)
      } catch (error) {
        console.error(error)
      }
    }
  }
}
</script>