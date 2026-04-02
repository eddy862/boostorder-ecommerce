<template>
  <div class="min-h-screen bg-orange-50">
    <!-- Navbar -->
    <nav
      class="bg-white shadow-sm border-b border-orange-100 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
      <div class="flex items-center gap-2 sm:gap-4">
        <router-link to="/"
          class="px-3 py-2 rounded-lg font-black text-gray-800 flex items-center gap-2">
          <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-400 text-white shadow-sm">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
            </svg>
          </span>
          <span>{{ appName }}</span>
        </router-link>

        <router-link to="/products"
          class="px-3 py-2 rounded-lg font-semibold text-gray-700 hover:bg-orange-50 transition flex items-center"
          :class="{ 'text-orange-500 bg-orange-100': $route.path === '/products' }">
          Products
        </router-link>
        
        <router-link to="/orders"
          class="px-3 py-2 rounded-lg font-semibold text-gray-700 hover:bg-orange-50 transition flex items-center"
          :class="{ 'text-orange-500 bg-orange-100': $route.path.startsWith('/orders') }">
          Order History
        </router-link>

        <router-link to="/cart"
          class="px-3 py-2 rounded-lg font-semibold text-gray-700 hover:bg-orange-50 transition flex items-center relative"
          :class="{ 'text-orange-500 bg-orange-100': $route.path.startsWith('/cart') }">
          <svg class="size-5"  fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
            </svg>
          <span v-if="cartCount > 0"
            class="ml-2 bg-red-500 text-white rounded-full px-2 py-0.5 text-xs font-bold absolute -top-2 -right-2 shadow">
            {{ cartCount }}
          </span>
        </router-link>

      </div>
      <div class="flex items-center gap-2 sm:gap-4">
        <!-- NOT logged in -->
        <template v-if="!user">
          <a href="/login"
            class="px-3 py-2 rounded-lg font-semibold text-gray-700 hover:bg-orange-50 transition">Login</a>
          <a href="/register"
            class="px-3 py-2 rounded-lg font-semibold text-gray-700 hover:bg-orange-50 transition">Register</a>
        </template>
        
        <!-- Logged in -->
        <template v-else>
          <a
            v-if="!user.email_verified_at"
            href="/verify-email"
            class="px-3 py-2 rounded-lg font-semibold text-orange-500 bg-orange-100 hover:bg-orange-200 transition"
          >
            Verify Email
          </a>
          <span class="text-gray-600 font-medium mr-2">{{ user.email }}</span>
          <button @click="logout"
            class="px-3 py-2 rounded-lg font-semibold text-white bg-orange-400 hover:bg-orange-500 transition">Logout</button>
        </template>
      </div>
    </nav>
    <main class="max-w-5xl mx-auto px-4 py-6">
      <router-view />
    </main>
  </div>
</template>

<script>
import axios from 'axios'
axios.defaults.withCredentials = true

export default {
  data() {
    return {
      appName: import.meta.env.VITE_APP_NAME || 'E-commerce',
      user: null,
      cartCount: 0
    }
  },

  async mounted() {
    this.fetchUser(),
      this.fetchCartCount()

    window.addEventListener('cart-updated', this.fetchCartCount)
  },

  methods: {
    async fetchUser() {
      try {
        const res = await axios.get('/api/user')
        console.log('User data:', res.data) 
        this.user = res.data
      } catch (err) {
        this.user = null
      }
    },

    async fetchCartCount() {
      const res = await axios.get('/api/cart')

      const cart = res.data

      let total = 0

      Object.values(cart).forEach(item => {
        total += Number(item.quantity) || 0
      })

      this.cartCount = total
    },

    async logout() {
      if (!confirm('Are you sure you want to logout?')) return
      await axios.post('/logout')
      window.location.href = '/'
    }
  }
}
</script>
