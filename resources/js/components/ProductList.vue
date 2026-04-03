<template>
  <div class="space-y-8 py-2">
    <section
      class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-400 via-orange-300 to-amber-200 px-6 py-8 text-white shadow-xl shadow-orange-200 sm:px-8">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-2xl">
          <p class="text-sm font-semibold uppercase tracking-[0.26em] text-orange-100">Daily Picks</p>
          <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">Find something fresh for your cart today.</h1>
          <p class="mt-4 text-sm leading-7 text-orange-50 sm:text-base">
            Browse the catalog, adjust quantity in one tap, and either buy instantly or save products for later.
          </p>
        </div>

        <div class="rounded-3xl bg-white/20 px-5 py-4 backdrop-blur">
          <p class="text-xs font-semibold uppercase tracking-[0.22em] text-orange-100">Catalog Size</p>
          <p class="mt-2 text-3xl font-black">{{ filteredProducts.length }}</p>
          <p class="text-sm text-orange-50">{{ debouncedSearch ? 'matching products' : 'available products' }}</p>
        </div>
      </div>
    </section>

    <section class="rounded-[1.75rem] border border-orange-100 bg-white p-5 shadow-sm sm:p-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">Search Products</p>
          <h2 class="mt-1 text-2xl font-black text-gray-800">Browse the storefront</h2>
        </div>

        <div class="w-full md:max-w-md">
          <input type="text" v-model="search" placeholder="Search products..." @input="onSearchInput"
            class="w-full rounded-2xl border border-orange-200 bg-orange-50/50 px-4 py-3 shadow-sm transition focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
        </div>
      </div>
    </section>

    <section v-if="products.length === 0"
      class="flex h-40 items-center justify-center rounded-[1.75rem] border border-orange-100 bg-white text-lg font-semibold text-orange-400 shadow-sm">
      Loading...
    </section>

    <section v-if="filteredProducts.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
      <div v-for="product in filteredProducts" :key="product.id"
        class="group relative overflow-hidden rounded-[1.75rem] border border-orange-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-100">
        <div class="absolute right-4 top-4 rounded-full px-3 py-1 text-xs font-bold"
          :class="product.stock > 0 ? 'bg-orange-100 text-orange-500' : 'bg-gray-100 text-gray-400'">
          {{ product.stock > 0 ? `${product.stock} left` : 'Out of stock' }}
        </div>

        <img v-if="product.image_url" :src="product.image_url" :alt="product.name"
          class="mb-4 h-[180px] w-full rounded-2xl border border-orange-100 object-cover transition " />
        <div v-else
          class="flex h-[180px] w-full items-center justify-center rounded-2xl border border-orange-100 bg-orange-50 text-sm text-orange-400 mb-4">
          No image available
        </div>

        <div class="mb-4">
          <h3 class="min-h-[56px] line-clamp-2 text-xl font-black leading-7 text-gray-800">{{ product.name }}</h3>
          <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500">
            {{ product.description || 'Fresh pick ready for your next checkout.' }}
          </p>
          <p class="mt-3 text-2xl font-black text-orange-500">RM {{ formatPrice(product.price) }}</p>
        </div>

        <div
          class="mb-4 flex items-center justify-between rounded-2xl border border-orange-100 bg-orange-50/60 px-4 py-3">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-400">Quantity</p>
          </div>
          <div class="flex items-center gap-2 rounded-full bg-white px-2 py-1 shadow-sm">
            <button @click.stop="decreaseQuantity(product)"
              :disabled="product.stock === 0 || getSelectedQuantity(product) <= 1"
              class="flex h-9 w-9 items-center justify-center rounded-full border border-orange-200 bg-white text-orange-500 transition hover:bg-orange-50 disabled:cursor-not-allowed disabled:border-gray-100 disabled:text-gray-300">
              -
            </button>

            <span class="min-w-10 text-center text-base font-black text-gray-700">
              {{ getSelectedQuantity(product) }}
            </span>

            <button @click.stop="increaseQuantity(product)"
              :disabled="product.stock === 0 || getSelectedQuantity(product) >= product.stock"
              class="flex h-9 w-9 items-center justify-center rounded-full border border-orange-200 bg-white text-orange-500 transition hover:bg-orange-50 disabled:cursor-not-allowed disabled:border-gray-100 disabled:text-gray-300">
              +
            </button>
          </div>
        </div>

        <div class="grid gap-2 sm:grid-cols-2">
          <button :disabled="product.stock === 0" @click.stop="buyNow(product)"
            class="w-full rounded-xl border border-orange-300 bg-orange-50 py-3 font-semibold text-orange-500 transition hover:bg-orange-100 disabled:cursor-not-allowed disabled:border-gray-200 disabled:bg-gray-100 disabled:text-gray-400">
            {{ product.stock === 0 ? 'Out of Stock' : 'Buy Now' }}
          </button>

          <button :disabled="product.stock === 0" @click.stop="addToCart(product)"
            class="w-full rounded-xl bg-orange-400 py-3 font-semibold text-white transition hover:bg-orange-500 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500">
            {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
          </button>
        </div>
      </div>
    </section>

    <section v-else-if="debouncedSearch"
      class="rounded-[1.75rem] border border-orange-100 bg-white py-12 text-center text-lg font-semibold text-gray-400 shadow-sm">
      No products found for "{{ debouncedSearch }}"
    </section>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      products: [],
      search: '',
      searchTimeout: null,
      debouncedSearch: '',
      quantities: {}
    }
  },

  mounted() {
    this.fetchProducts()

    if (this.$route.query['buy-now'] && this.$route.query['quantity'] && this.$route.query['name']) {
      this.buyNow()
      this.$router.replace({ query: {} })
    }

    if (window.Echo?.channel) {
      window.Echo.channel('products')
        .listen('.product.updated', (e) => {
          console.log('Received product update event:', e)
          this.fetchProducts()
        })
    }
  },

  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products')
        console.log('Fetched products:', response.data)
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
      }, 400)
    },

    async addToCart(product) {
      const quantity = Number(this.getSelectedQuantity(product))

      try {
        await axios.post(`/api/cart/add/${product.id}/${quantity}`)
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
        alert('Order placed! Order id: ' + res.data.order.id)
        this.fetchProducts()
      } catch (error) {
        console.error(error)
        if (error.response && error.response.status === 401) {
          const redirectUrl = encodeURIComponent(`/products?buy-now=${productId}&quantity=${quantity}&name=${productName}`)
          window.location.href = `/login?redirect=${redirectUrl}`
        } else {
          alert(error.response?.data?.message || 'An error occurred while processing your order.')
        }
      }
    },

    increaseQuantity(product) {
      const current = this.getSelectedQuantity(product)

      if (product.stock > 0 && current < product.stock) {
        this.quantities[product.id] = current + 1
      }
    },

    decreaseQuantity(product) {
      const current = this.getSelectedQuantity(product)

      if (current > 1) {
        this.quantities[product.id] = current - 1
      }
    },

    formatPrice(price) {
      return Number(price).toFixed(2)
    },

    getSelectedQuantity(product) {
      const rawQuantity = Number(this.quantities[product.id] ?? 1)

      if (!Number.isFinite(rawQuantity) || rawQuantity < 1) {
        this.quantities[product.id] = 1
        return 1
      }

      if (product.stock > 0 && rawQuantity > product.stock) {
        this.quantities[product.id] = product.stock
        return product.stock
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
