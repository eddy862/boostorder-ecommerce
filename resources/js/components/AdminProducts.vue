<template>
  <section class="space-y-6">
    <div class="flex flex-col gap-3 rounded-3xl bg-gradient-to-br from-orange-400 via-orange-300 to-amber-200 px-6 py-8 text-white shadow-xl shadow-orange-200 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-50">Admin Dashboard</p>
        <h1 class="mt-2 text-3xl font-black sm:text-4xl">Product Management</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-orange-50">
          Add new products, update stock and pricing, and keep the storefront catalog organized from one place.
        </p>
      </div>
      <button
        @click="startCreate"
        class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-orange-500 shadow-md transition hover:bg-orange-50"
      >
        Add Product
      </button>
    </div>

    <div
      v-if="message"
      class="rounded-2xl border px-4 py-3 text-sm font-medium"
      :class="messageType === 'error' ? 'border-red-200 bg-red-50 text-red-600' : 'border-green-200 bg-green-50 text-green-700'"
    >
      {{ message }}
    </div>

    <div v-if="showForm" class="rounded-3xl border border-orange-100 bg-white p-6 shadow-sm">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">
            {{ form.id ? 'Edit Product' : 'New Product' }}
          </p>
          <h2 class="mt-1 text-2xl font-black text-gray-800">
            {{ form.id ? form.name || 'Update product' : 'Create a product' }}
          </h2>
        </div>
        <button
          @click="resetForm"
          class="rounded-lg border border-orange-200 px-4 py-2 text-sm font-semibold text-orange-500 transition hover:bg-orange-50"
        >
          Cancel
        </button>
      </div>

      <form class="mt-6 grid gap-4 md:grid-cols-2" @submit.prevent="submitForm">
        <div class="md:col-span-2">
          <label class="mb-2 block text-sm font-semibold text-gray-700">Product Name</label>
          <input v-model="form.name" type="text" class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          <p v-if="errors.name" class="mt-2 text-sm text-red-500">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-gray-700">Price (RM)</label>
          <input v-model="form.price" type="number" min="0" step="0.01" class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          <p v-if="errors.price" class="mt-2 text-sm text-red-500">{{ errors.price[0] }}</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-gray-700">Stock</label>
          <input v-model="form.stock" type="number" min="0" step="1" class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          <p v-if="errors.stock" class="mt-2 text-sm text-red-500">{{ errors.stock[0] }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
          <textarea v-model="form.description" rows="4" class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>
          <p v-if="errors.description" class="mt-2 text-sm text-red-500">{{ errors.description[0] }}</p>
        </div>

        <div class="md:col-span-2 flex justify-end">
          <button
            type="submit"
            :disabled="submitting"
            class="rounded-xl bg-orange-400 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 disabled:cursor-not-allowed disabled:bg-orange-200"
          >
            {{ submitting ? 'Saving...' : (form.id ? 'Update Product' : 'Create Product') }}
          </button>
        </div>
      </form>
    </div>

    <div class="rounded-3xl border border-orange-100 bg-white p-6 shadow-sm">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">Catalog</p>
          <h2 class="mt-1 text-2xl font-black text-gray-800">All Products</h2>
        </div>
        <span class="rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-500">
          {{ products.length }} items
        </span>
      </div>

      <div v-if="loading" class="py-12 text-center text-lg font-semibold text-orange-400">
        Loading products...
      </div>

      <div v-else-if="products.length === 0" class="py-12 text-center text-gray-400">
        No products available yet.
      </div>

      <div v-else class="mt-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-orange-100">
          <thead>
            <tr class="text-left text-sm font-semibold text-gray-500">
              <th class="pb-3">Product</th>
              <th class="pb-3">Price</th>
              <th class="pb-3">Stock</th>
              <th class="pb-3">Description</th>
              <th class="pb-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-orange-50">
            <tr v-for="product in products" :key="product.id">
              <td class="py-4 pr-4">
                <p class="font-semibold text-gray-800">{{ product.name }}</p>
                <p class="text-xs text-gray-400">#{{ product.id }}</p>
              </td>
              <td class="py-4 pr-4 font-semibold text-orange-500">RM {{ formatPrice(product.price) }}</td>
              <td class="py-4 pr-4">
                <span
                  class="rounded-full px-3 py-1 text-xs font-semibold"
                  :class="Number(product.stock) > 0 ? 'bg-orange-100 text-orange-500' : 'bg-gray-100 text-gray-400'"
                >
                  {{ product.stock }}
                </span>
              </td>
              <td class="max-w-xs py-4 pr-4 text-sm leading-6 text-gray-500">
                {{ product.description || 'No description.' }}
              </td>
              <td class="py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button
                    @click="startEdit(product)"
                    class="rounded-lg border border-orange-200 px-3 py-2 text-sm font-semibold text-orange-500 transition hover:bg-orange-50"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteProduct(product)"
                    class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-500 transition hover:bg-red-50"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      loading: true,
      submitting: false,
      showForm: false,
      products: [],
      errors: {},
      message: '',
      messageType: 'success',
      form: this.emptyForm(),
    }
  },

  mounted() {
    this.fetchProducts()
  },

  methods: {
    emptyForm() {
      return {
        id: null,
        name: '',
        price: '',
        stock: '',
        description: '',
      }
    },

    async fetchProducts() {
      this.loading = true

      try {
        const res = await axios.get('/api/products')
        this.products = res.data
      } finally {
        this.loading = false
      }
    },

    startCreate() {
      this.resetForm()
      this.showForm = true
      this.message = ''
    },

    startEdit(product) {
      this.showForm = true
      this.errors = {}
      this.message = ''
      this.form = {
        id: product.id,
        name: product.name,
        price: product.price,
        stock: product.stock,
        description: product.description || '',
      }
    },

    resetForm() {
      this.form = this.emptyForm()
      this.errors = {}
      this.showForm = false
    },

    async submitForm() {
      this.submitting = true
      this.errors = {}
      this.message = ''

      const payload = {
        name: this.form.name,
        price: this.form.price,
        stock: this.form.stock,
        description: this.form.description,
      }

      try {
        if (this.form.id) {
          await axios.put(`/api/products/${this.form.id}`, payload)
          this.message = 'Product updated successfully.'
        } else {
          await axios.post('/api/products', payload)
          this.message = 'Product created successfully.'
        }

        this.messageType = 'success'
        this.resetForm()
        await this.fetchProducts()
      } catch (error) {
        if (error.response?.status === 422) {
          console.error('Validation errors:', error.response.data.errors)
          this.errors = error.response.data.errors || {}
          this.message = 'Please fix the highlighted fields.'
          this.messageType = 'error'
        } else {
          this.message = error.response?.data?.message || 'Unable to save the product right now.'
          this.messageType = 'error'
        }
      } finally {
        this.submitting = false
      }
    },

    async deleteProduct(product) {
      if (!confirm(`Delete "${product.name}"?`)) {
        return
      }

      try {
        await axios.delete(`/api/products/${product.id}`)
        this.message = 'Product deleted successfully.'
        this.messageType = 'success'
        await this.fetchProducts()

        if (this.form.id === product.id) {
          this.resetForm()
        }
      } catch (error) {
        this.message = error.response?.data?.message || 'Unable to delete the product right now.'
        this.messageType = 'error'
      }
    },

    formatPrice(price) {
      return Number(price).toFixed(2)
    },
  },
}
</script>
