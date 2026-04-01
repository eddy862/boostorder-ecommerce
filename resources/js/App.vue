<template>
  <div>
    <!-- Navbar -->
    <div style="padding:10px; background:#eee;">
      <router-link to="/">Products</router-link> |
      <router-link to="/cart">
        Cart
        <span v-if="cartCount > 0" style="
            background:red;
            color:white;
            border-radius:50%;
            padding:2px 6px;
            font-size:12px;
            margin-left:5px;
        ">
          {{ cartCount }}
        </span>
      </router-link> |
      <router-link to="/orders">Order History</router-link> 

      <span style="float:right;">
        <!-- NOT logged in -->
        <span v-if="!user">
          <a href="/login">Login</a> |
        </span>

        <!-- Logged in -->
        <span v-else>
          {{ user.email }}
          <button @click="logout">Logout</button> 
        </span>

        <a v-if="!user" href="/register">Register</a>
      </span>
    </div>

    <router-view />
  </div>
</template>

<script>
import axios from 'axios'
axios.defaults.withCredentials = true

export default {
  data() {
    return {
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
        total += item.quantity
      })

      this.cartCount = total
    },

    async logout() {
      await axios.post('/logout')
      window.location.href = '/'
    }
  }
}
</script>