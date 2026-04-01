import { createRouter, createWebHistory } from 'vue-router'
import ProductList from './components/ProductList.vue'
import CartPage from './components/CartPage.vue'
import OrderHistory from './components/OrderHistory.vue'
import axios from 'axios'

axios.defaults.withCredentials = true

const routes = [
    {
        path: '/',
        component: ProductList
    },
    {
        path: '/cart',
        component: CartPage
    },
    {
        path: '/orders',
        component: OrderHistory,
        meta: { requiresAuth: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async (to, from, next) => {
  if (to.meta.requiresAuth) {
    try {
      const res = await axios.get('/api/user')

      if (res.data) {
        next() // user logged in
      } else {
        window.location.href = '/login?redirect=' + to.fullPath
      }

    } catch (err) {
      window.location.href = '/login?redirect=' + to.fullPath
    }
  } else {
    next()
  }
})

export default router