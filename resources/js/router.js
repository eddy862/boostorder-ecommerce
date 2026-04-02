import { createRouter, createWebHistory } from 'vue-router'
import Home from './components/Home.vue'
import ProductList from './components/ProductList.vue'
import CartPage from './components/CartPage.vue'
import OrderHistory from './components/OrderHistory.vue'
import axios from 'axios'

axios.defaults.withCredentials = true
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

const routes = [
  {
    path: '/',
    component: Home,
    meta: { title: 'Home' }
  },
  {
    path: '/products',
    component: ProductList,
    meta: { title: 'Products' }
  },
  {
    path: '/cart',
    component: CartPage,
    meta: { title: 'Cart' }
  },
  {
    path: '/orders',
    component: OrderHistory,
    meta: { requiresAuth: true, title: 'Order History' }
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

router.afterEach((to) => {
  document.title = to.meta.title ? `${appName} - ${to.meta.title}` : appName
})

export default router
