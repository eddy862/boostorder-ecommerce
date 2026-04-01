<template>
  <div>
    <h1>Product List</h1>

    <input type="text" v-model="search" placeholder="Search products..." @input="onSearchInput"
      style="margin-bottom: 16px; padding: 6px; width: 100%; max-width: 400px;" />

    <div v-if="products.length === 0">
      Loading...
    </div>

    <div style="display:flex; gap:12px;"  v-else>
      <div v-for="product in filteredProducts" :key="product.id" @click="showProduct(product)"
        style="cursor:pointer; border:1px solid #eee; margin-bottom:12px; padding:10px; border-radius:6px;">
        <img :src="getImage(product.id)" alt="product image"
          style="width:100%; max-width:200px; height:150px; object-fit:cover; border-radius:6px; margin-bottom:8px;" />

        <h3>{{ product.name }}</h3>
        <p>RM {{ product.price }}</p>
        
        <button :disabled="product.stock === 0" @click.stop="addToCart(product.id)">
          {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
        </button>
      </div>

      <!-- Product Detail Modal -->
      <div v-if="selectedProduct" class="modal-overlay"
        style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1000;display:flex;align-items:center;justify-content:center;">
        <div class="modal-content"
          style="background:#fff;padding:24px 32px;border-radius:8px;min-width:300px;max-width:90vw;box-shadow:0 2px 16px rgba(0,0,0,0.15);position:relative;">
          <button @click="selectedProduct = null"
            style="position:absolute;top:8px;right:12px;font-size:20px;background:none;border:none;cursor:pointer;">&times;</button>

          <img :src="getImage(selectedProduct.id)"
            style="width:100%; height:200px; object-fit:cover; margin-bottom:12px;" />

          <h2>{{ selectedProduct.name }}</h2>
          <p><strong>Price:</strong> RM {{ selectedProduct.price }}</p>
          <p><strong>Stock:</strong> {{ selectedProduct.stock }}</p>
          <p><strong>Description:</strong></p>
          <p>{{ selectedProduct.description || 'No description.' }}</p>
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