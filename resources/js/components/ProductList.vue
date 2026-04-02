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

      <div v-if="filteredProducts.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="product in filteredProducts" :key="product.id" @click="showProduct(product)"
          class="bg-white border border-orange-100 rounded-xl shadow hover:shadow-lg transition cursor-pointer flex flex-col items-center p-4 relative group">

          <img :src="getImage(product.id)" alt="product image"
            class="w-full max-w-[180px] h-[140px] object-cover rounded-lg mb-3 border border-orange-100 group-hover:scale-105 transition" />

          <h3 class="text-base font-semibold text-gray-800 mb-1 text-center line-clamp-2 min-h-[48px]">{{ product.name
          }}</h3>
          <p class="text-orange-500 font-bold text-lg mb-2">RM {{ product.price }}</p>

          <div class="mb-3 flex items-center gap-2 w-full">
            <label :for="`quantity-${product.id}`" class="text-sm font-medium text-gray-600">Qty</label>
            <input :id="`quantity-${product.id}`" v-model.number="quantities[product.id]" @click.stop type="number"
              min="1" :max="product.stock"
              class="w-full rounded-lg border border-orange-200 px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          </div>

          <div class="flex w-full flex-col gap-2">
            <button :disabled="product.stock === 0" @click.stop="buyNow(product)"
              class="w-full py-2 rounded-lg font-semibold text-orange-500 transition border border-orange-300 bg-orange-50 hover:bg-orange-100 disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200 disabled:cursor-not-allowed">
              {{ product.stock === 0 ? 'Out of Stock' : 'Buy Now' }}
            </button>

            <button :disabled="product.stock === 0" @click.stop="addToCart(product)"
              class="w-full py-2 rounded-lg font-semibold text-white transition
                bg-orange-400 hover:bg-orange-500 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed">
              {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
          </div>
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
      selectedProduct: null,
      quantities: {}
    }
  },

  mounted() {
    this.fetchProducts()

    if (this.$route.query['buy-now']) {
      this.buyNow()
      this.$router.replace({ query: {} })
    }
  },

  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products')
        console.log(response.data)
        this.products = response.data
        this.quantities = response.data.reduce((acc, product) => {
          acc[product.id] = 1
          return acc
        }, {})
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

    async addToCart(product) {
      const quantity = Number(this.getSelectedQuantity(product))

      try {
        const response = await axios.post(`/api/cart/add/${product.id}/${quantity}`)
        console.log(response.data)
        window.dispatchEvent(new Event('cart-updated'))
      } catch (error) {
        console.error(error)
      }
    },

    async buyNow(product) {
      const productName = product ? product.name : this.$route.query['name']
      const productId = product ? product.id : this.$route.query['buy-now']
      const quantity = product ? Number(this.getSelectedQuantity(product)) : Number(this.$route.query['quantity'])

      if (!confirm(`Proceed to buy ${quantity} of "${productName}"?`)) {
        return
      }

      try {
        const res = await axios.post(`/api/checkout/buy-now/${productId}/${quantity}`)
        console.log(res.data)
        alert('Order placed! Order id: ' + res.data.order.id)
        this.fetchProducts()
      } catch (error) {
        console.error(error)
        if (error.response && error.response.status === 401) {
          // redirect to login then redirect back to product page with buy-now intent
          const redirectUrl = encodeURIComponent(`/?buy-now=${productId}&quantity=${quantity}&name=${productName}`)
          window.location.href = `/login?redirect=${redirectUrl}`
        } else {
          alert(error.response?.data?.message || 'An error occurred while processing your order.')
          console.error('Buy Now error details:', error.response?.data || error)
        }
      }
    },

    showProduct(product) {
      this.selectedProduct = product
    },

    getImage(id) {
      return `https://picsum.photos/seed/${id}/300/200`
    },

    getSelectedQuantity(product) {
      const rawQuantity = Number(this.quantities[product.id] ?? 1)

      if (!Number.isFinite(rawQuantity) || rawQuantity < 1) {
        this.quantities[product.id] = 1
        return 1
      }

      return Math.floor(rawQuantity)
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
