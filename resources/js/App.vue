<template>
  <div :class="isAdminLayout ? 'min-h-screen bg-slate-100' : 'min-h-screen bg-orange-50'">
    <template v-if="isAdminLayout">
      <div class="min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="border-r border-slate-200 bg-slate-950 px-5 py-6 text-slate-100">
          <router-link to="/admin/products" class="flex items-center gap-3 rounded-2xl bg-slate-900/70 px-4 py-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-400 text-white shadow-sm">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
              </svg>
            </span>
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Admin Panel</p>
              <p class="text-lg font-black text-white">{{ appName }}</p>
            </div>
          </router-link>

          <nav class="mt-8 space-y-2">
            <router-link to="/admin/products"
              class="flex items-center justify-between rounded-xl px-4 py-3 font-semibold text-slate-300 transition hover:bg-slate-900 hover:text-white"
              :class="{ 'bg-orange-400 text-white hover:bg-orange-400': $route.path.startsWith('/admin/products') }">
              <span>Products</span>
              <span class="text-xs uppercase tracking-[0.2em]">CRUD</span>
            </router-link>

            <router-link to="/admin/orders"
              class="flex items-center justify-between rounded-xl px-4 py-3 font-semibold text-slate-300 transition hover:bg-slate-900 hover:text-white"
              :class="{ 'bg-orange-400 text-white hover:bg-orange-400': $route.path.startsWith('/admin/orders') }">
              <span>Orders</span>
              <span class="text-xs uppercase tracking-[0.2em]">Review</span>
            </router-link>

            <router-link to="/products"
              class="flex items-center justify-between rounded-xl px-4 py-3 font-semibold text-slate-300 transition hover:bg-slate-900 hover:text-white">
              <span>Storefront</span>
              <span class="text-xs uppercase tracking-[0.2em]">View</span>
            </router-link>
          </nav>
        </aside>

        <div class="min-w-0">
          <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-400">Admin Workspace</p>
                <h1 class="text-2xl font-black text-slate-800">{{ $route.meta.title || 'Admin Panel' }}</h1>
              </div>
              <div class="flex items-center gap-3">
                <span class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">{{ user?.email }}</span>
                <button @click="logout"
                  class="rounded-xl bg-orange-400 px-4 py-2 font-semibold text-white transition hover:bg-orange-500">
                  Logout
                </button>
              </div>
            </div>
          </header>

          <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6">
            <router-view />
          </main>
        </div>
      </div>
    </template>

    <template v-else>
      <nav class="sticky top-0 z-30 border-b border-orange-100/80 bg-white/90 px-4 py-3 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
          <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <router-link to="/" class="mr-1 flex items-center gap-3 rounded-2xl px-2 py-1 text-gray-800 transition hover:bg-orange-50">
              <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-400 text-white shadow-lg shadow-orange-200/70">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                </svg>
              </span>
              <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-orange-400">Storefront</p>
                <span class="text-lg font-black">{{ appName }}</span>
              </div>
            </router-link>

            <router-link to="/products"
              class="rounded-xl px-4 py-2 font-semibold text-gray-700 transition hover:bg-orange-50"
              :class="{ 'bg-orange-100 text-orange-500 shadow-sm': $route.path === '/products' }">
              Products
            </router-link>

            <router-link to="/orders"
              class="rounded-xl px-4 py-2 font-semibold text-gray-700 transition hover:bg-orange-50"
              :class="{ 'bg-orange-100 text-orange-500 shadow-sm': $route.path.startsWith('/orders') }">
              Orders
            </router-link>

            <router-link to="/cart"
              class="relative rounded-xl px-4 py-2 font-semibold text-gray-700 transition hover:bg-orange-50"
              :class="{ 'bg-orange-100 text-orange-500 shadow-sm': $route.path.startsWith('/cart') }">
              <span class="flex items-center gap-2">
                <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M2.25 2.25a.75.75 0 0 0 0 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 0 0-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 0 0 0-1.5H5.378A2.25 2.25 0 0 1 7.5 15h11.218a.75.75 0 0 0 .674-.421 60.358 60.358 0 0 0 2.96-7.228.75.75 0 0 0-.525-.965A60.864 60.864 0 0 0 5.68 4.509l-.232-.867A1.875 1.875 0 0 0 3.636 2.25H2.25ZM3.75 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0ZM16.5 20.25a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0Z" />
                </svg>
                Cart
              </span>
              <span v-if="cartCount > 0"
                class="absolute -right-1 -top-1 rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white shadow">
                {{ cartCount }}
              </span>
            </router-link>
          </div>

          <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <template v-if="!user">
              <a href="/login"
                class="rounded-xl px-4 py-2 font-semibold text-gray-700 transition hover:bg-orange-50">Login</a>
              <a href="/register"
                class="rounded-xl bg-orange-400 px-4 py-2 font-semibold text-white transition hover:bg-orange-500">Register</a>
            </template>

            <template v-else>
              <a v-if="!user.email_verified_at" href="/verify-email"
                class="rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 font-semibold text-orange-500 transition hover:bg-orange-100">
                Verify Email
              </a>
              <span class="rounded-full bg-orange-50 px-4 py-2 text-sm font-medium text-gray-600">{{ user.email }}</span>
              <button @click="logout"
                class="rounded-xl bg-orange-400 px-4 py-2 font-semibold text-white transition hover:bg-orange-500">Logout</button>
            </template>
          </div>
        </div>
      </nav>

      <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
        <router-view />
      </main>
    </template>
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

  computed: {
    isAdminLayout() {
      return this.$route.meta.layout === 'admin'
    }
  },

  async mounted() {
    this.fetchUser()
    this.fetchCartCount()
    window.addEventListener('cart-updated', this.fetchCartCount)
  },

  methods: {
    async fetchUser() {
      try {
        const res = await axios.get('/api/user')
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
      this.user = null

      if (this.$route.meta.requiresAuth || this.$route.meta.requiresAdmin) {
        this.$router.push('/')
        return
      }

      this.fetchCartCount()
    }
  }
}
</script>
