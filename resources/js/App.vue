<template>
  <div>
    <!-- Navbar -->
    <div style="padding:10px; background:#eee;">
      <router-link to="/">Products</router-link> |
      <router-link to="/cart">Cart</router-link> |
      <router-link to="/orders">Order History</router-link>

      <!-- NOT logged in -->
      <span v-if="!user">
        <a href="/login">Login</a>
      </span>

      <!-- Logged in -->
      <span v-else>
        {{ user.email }}
        <button @click="logout">Logout</button>
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
      user: null
    }
  },

  async mounted() {
    this.fetchUser()
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

    async logout() {
      await axios.post('/logout')
      window.location.href = '/'
    }
  }
}
</script>