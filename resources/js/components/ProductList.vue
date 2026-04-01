<template>
  <div class="min-h-screen py-2">
    <div class="max-w-5xl mx-auto px-4">
      <div class="mb-8 flex justify-center">
        <input type="text" v-model="search" placeholder="Search products..." @input="onSearchInput"
          class="w-full max-w-md px-4 py-2 border border-orange-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition" />
      </div>

      <div v-if="products.length === 0"
        class="flex justify-center items-center h-40 text-orange-400 text-lg font-semibold">
        Loading...
      </div>

      <div v-if="filteredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="product in filteredProducts" :key="product.id" @click="showProduct(product)"
          class="bg-white border border-orange-100 rounded-xl shadow hover:shadow-lg transition cursor-pointer flex flex-col items-center p-4 relative group">

          <img :src="getImage(product.id)" alt="product image"
            class="w-full max-w-[180px] h-[140px] object-cover rounded-lg mb-3 border border-orange-100 group-hover:scale-105 transition" />

          <h3 class="text-base font-semibold text-gray-800 mb-1 text-center line-clamp-2 min-h-[48px]">{{ product.name
            }}</h3>
          <p class="text-orange-500 font-bold text-lg mb-2">RM {{ product.price }}</p>

          <button :disabled="product.stock === 0" @click.stop="addToCart(product.id)"
            class="w-full py-2 rounded-lg font-semibold text-white transition
              bg-orange-400 hover:bg-orange-500 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed">
            {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
          </button>
        </div>
      </div>

      <div v-else-if="debouncedSearch" class="text-center text-gray-400 py-8 text-lg font-semibold">
        No products found for "{{ debouncedSearch }}"
      </div>

      <!-- Product Detail Modal -->
      <div v-if="selectedProduct"
        class="fixed top-0 left-0 w-screen h-screen bg-black bg-opacity-30 z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 min-w-[320px] max-w-[95vw] relative animate-fadeIn">

          <button @click="selectedProduct = null"
            class="absolute top-3 right-4 text-2xl text-gray-400 hover:text-orange-500 focus:outline-none">&times;</button>

          <img :src="getImage(selectedProduct.id)"
            class="w-full h-[200px] object-cover rounded-lg mb-4 border border-orange-100" />

          <h2 class="text-2xl font-bold text-orange-500 mb-2">{{ selectedProduct.name }}</h2>
          <p class="text-lg font-semibold mb-1"><span class="text-gray-500">Price:</span> <span
              class="text-orange-500">RM {{ selectedProduct.price }}</span></p>
          <p class="mb-1"><span class="text-gray-500 font-medium">Stock:</span> {{ selectedProduct.stock }}</p>
          <p class="text-gray-500">{{ selectedProduct.description || 'No description.' }}</p>
        </div>
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
      products: [],
      search: '',
      searchTimeout: null,
      debouncedSearch: '',
      selectedProduct: null
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

    onSearchInput() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.debouncedSearch = this.search
      }, 400) // 400ms debounce
    },

    async addToCart(productId) {
      try {
        const response = await axios.post(`/api/cart/add/${productId}`)
        console.log(response.data)
        window.dispatchEvent(new Event('cart-updated'))
      } catch (error) {
        console.error(error)
      }
    },

    showProduct(product) {
      this.selectedProduct = product
    },

    getImage(id) {
      return `https://picsum.photos/seed/${id}/300/200`
    }
  },

  computed: {
    filteredProducts() {
      if (!this.debouncedSearch) return this.products
      const s = this.debouncedSearch.toLowerCase()
      return this.products.filter(p =>
        p.name.toLowerCase().includes(s) ||
        (p.description && p.description.toLowerCase().includes(s))
      )
    }
  },
}
</script>