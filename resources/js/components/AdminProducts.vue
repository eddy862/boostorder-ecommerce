<template>
  <section class="space-y-6">
    <div
      class="flex flex-col gap-3 rounded-3xl bg-gradient-to-br from-orange-400 via-orange-300 to-amber-200 px-6 py-8 text-white shadow-xl shadow-orange-200 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-50">Admin Dashboard</p>
        <h1 class="mt-2 text-3xl font-black sm:text-4xl">Product Management</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-orange-50">
          Add new products, upload one image per product, and keep the storefront catalog organized from one place.
        </p>
      </div>
      <button @click="startCreate"
        class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-orange-500 shadow-md transition hover:bg-orange-50">
        Add Product
      </button>
    </div>

    <div v-if="message" class="rounded-2xl border px-4 py-3 text-sm font-medium"
      :class="messageType === 'error' ? 'border-red-200 bg-red-50 text-red-600' : 'border-green-200 bg-green-50 text-green-700'">
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
        <button @click="resetForm"
          class="rounded-lg border border-orange-200 px-4 py-2 text-sm font-semibold text-orange-500 transition hover:bg-orange-50">
          Cancel
        </button>
      </div>

      <form class="mt-6 grid gap-4 md:grid-cols-2" @submit.prevent="submitForm">
        <div class="md:col-span-2">
          <label class="mb-2 block text-sm font-semibold text-gray-700">Product Name</label>
          <input v-model="form.name" type="text"
            class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          <p v-if="errors.name" class="mt-2 text-sm text-red-500">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-gray-700">Price (RM)</label>
          <input v-model="form.price" type="number" min="0" step="0.01"
            class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          <p v-if="errors.price" class="mt-2 text-sm text-red-500">{{ errors.price[0] }}</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-semibold text-gray-700">Stock</label>
          <input v-model="form.stock" type="number" min="0" step="1"
            class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300" />
          <p v-if="errors.stock" class="mt-2 text-sm text-red-500">{{ errors.stock[0] }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
          <textarea v-model="form.description" rows="4"
            class="w-full rounded-xl border border-orange-200 bg-orange-50/40 px-4 py-3 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>
          <p v-if="errors.description" class="mt-2 text-sm text-red-500">{{ errors.description[0] }}</p>
        </div>

        <div class="md:col-span-2">
          <label class="mb-2 block text-sm font-semibold text-gray-700">Product Image</label>
          <div
            class="grid gap-4 rounded-2xl border border-dashed border-orange-200 bg-orange-50/40 p-4 md:grid-cols-[180px,1fr]">
            <div class="overflow-hidden rounded-2xl border border-orange-100 bg-white shadow-sm">
              <img v-if="imagePreviewUrl" :src="imagePreviewUrl" alt="Product preview"
                class="h-40 w-full object-cover" />
              <div v-else class="flex h-40 items-center justify-center bg-orange-100 text-sm text-orange-400">
                No image selected
              </div>
            </div>

            <div class="space-y-3">
              <input :key="fileInputKey" type="file" accept="image/*" @change="handleImageSelection"
                class="block w-full rounded-xl border border-orange-200 bg-white px-4 py-3 text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-orange-100 file:px-4 file:py-2 file:font-semibold file:text-orange-500 hover:file:bg-orange-200" />
              <p class="text-sm leading-6 text-gray-500">
                Upload one image per product to Firebase Storage at
                <span class="font-semibold text-orange-500">products/{{ form.id || '&lt;product-id&gt;' }}</span>.
              </p>
              <p v-if="selectedFileName" class="text-sm font-medium text-gray-600">
                Selected file: {{ selectedFileName }}
              </p>
              <p v-if="errors.image_url" class="text-sm text-red-500">{{ errors.image_url[0] }}</p>
            </div>
          </div>
        </div>

        <div class="md:col-span-2 flex justify-end">
          <button type="submit" :disabled="submitting"
            class="rounded-xl bg-orange-400 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-500 disabled:cursor-not-allowed disabled:bg-orange-200">
            {{ submitLabel }}
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
                <div class="flex items-center gap-3">
                  <img v-if="product.image_url" :src="product.image_url" :alt="product.name"
                    class="h-16 w-16 rounded-2xl border border-orange-100 object-cover shadow-sm" />
                  <div v-else
                    class="flex h-16 w-16 items-center justify-center rounded-2xl border border-orange-100 bg-orange-50 text-xs text-orange-400">
                    No image
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ product.name }}</p>
                    <p class="text-xs text-gray-400">#{{ product.id }}</p>
                  </div>
                </div>
              </td>
              <td class="py-4 pr-4 font-semibold text-orange-500">RM {{ formatPrice(product.price) }}</td>
              <td class="py-4 pr-4">
                <span class="rounded-full px-3 py-1 text-xs font-semibold"
                  :class="Number(product.stock) > 0 ? 'bg-orange-100 text-orange-500' : 'bg-gray-100 text-gray-400'">
                  {{ product.stock }}
                </span>
              </td>
              <td class="max-w-xs py-4 pr-4 text-sm leading-6 text-gray-500">
                {{ product.description || 'No description.' }}
              </td>
              <td class="py-4 text-right">
                <div class="flex justify-end gap-2">
                  <button @click="startEdit(product)"
                    class="rounded-lg border border-orange-200 px-3 py-2 text-sm font-semibold text-orange-500 transition hover:bg-orange-50">
                    Edit
                  </button>
                  <button @click="deleteProduct(product)"
                    class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-500 transition hover:bg-red-50">
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
import { deleteObject, getDownloadURL, ref as storageRef, uploadBytes } from 'firebase/storage'
import { storage } from '../firebase'

const MAX_IMAGE_SIZE = 5 * 1024 * 1024 // 5MB

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
      imageFile: null,
      imagePreviewUrl: '',
      selectedFileName: '',
      fileInputKey: 0,
      form: this.emptyForm(),
    }
  },

  computed: {
    submitLabel() {
      if (this.submitting && this.imageFile) {
        return this.form.id ? 'Saving and Uploading...' : 'Creating and Uploading...'
      }

      if (this.submitting) {
        return this.form.id ? 'Saving...' : 'Creating...'
      }

      return this.form.id ? 'Update Product' : 'Create Product'
    },
  },

  mounted() {
    this.fetchProducts()
  },

  beforeUnmount() {
    this.revokePreviewUrl()
  },

  methods: {
    emptyForm() {
      return {
        id: null,
        name: '',
        price: '',
        stock: '',
        description: '',
        image_url: '',
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
      this.scrollToTop()
    },

    startEdit(product) {
      this.showForm = true
      this.errors = {}
      this.message = ''
      this.revokePreviewUrl()
      this.imageFile = null
      this.selectedFileName = ''
      this.fileInputKey += 1
      this.form = {
        id: product.id,
        name: product.name,
        price: product.price,
        stock: product.stock,
        description: product.description || '',
        image_url: product.image_url || '',
      }
      this.imagePreviewUrl = product.image_url || ''
      this.scrollToTop()
    },

    resetForm() {
      this.revokePreviewUrl()
      this.form = this.emptyForm()
      this.errors = {}
      this.showForm = false
      this.imageFile = null
      this.imagePreviewUrl = ''
      this.selectedFileName = ''
      this.fileInputKey += 1
    },

    handleImageSelection(event) {
      const [file] = event.target.files || []

      if (!file) {
        return
      }

      if (!file.type.startsWith('image/')) {
        this.errors = {
          ...this.errors,
          image_url: ['Please choose an image file.'],
        }
        event.target.value = ''
        return
      }

      if (file.size > MAX_IMAGE_SIZE) {
        this.errors = {
          ...this.errors,
          image_url: ['Please choose an image smaller than 5MB.'],
        }
        event.target.value = ''
        return
      }

      this.errors = {
        ...this.errors,
        image_url: null,
      }
      this.imageFile = file
      this.selectedFileName = file.name
      this.revokePreviewUrl()
      this.imagePreviewUrl = URL.createObjectURL(file)
    },

    revokePreviewUrl() { // revote previously created preview URL to avoid memory leaks
      if (this.imagePreviewUrl && this.imagePreviewUrl.startsWith('blob:')) {
        URL.revokeObjectURL(this.imagePreviewUrl)
      }
    },

    async uploadProductImage(productId, file) {
      const productImageRef = storageRef(storage, `products/${productId}`)

      await uploadBytes(productImageRef, file, {
        contentType: file.type,
      })

      return getDownloadURL(productImageRef)
    },

    async deleteProductImage(productId) {
      const productImageRef = storageRef(storage, `products/${productId}`)

      try {
        await deleteObject(productImageRef)
      } catch (error) {
        if (error?.code !== 'storage/object-not-found') {
          throw error
        }
      }
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
        image_url: this.form.image_url || null,
      }

      try {
        let savedProduct

        if (this.form.id) {
          const updateRes = await axios.put(`/api/products/${this.form.id}`, payload)
          savedProduct = updateRes.data
        } else {
          const createRes = await axios.post('/api/products', payload)
          savedProduct = createRes.data
        }

        if (this.imageFile) {
          const imageUrl = await this.uploadProductImage(savedProduct.id, this.imageFile)
          const imageUpdateRes = await axios.put(`/api/products/${savedProduct.id}`, {
            name: savedProduct.name,
            price: savedProduct.price,
            stock: savedProduct.stock,
            description: savedProduct.description,
            image_url: imageUrl,
          })

          savedProduct = imageUpdateRes.data
        }

        this.message = this.form.id ? 'Product updated successfully.' : 'Product created successfully.'
        this.messageType = 'success'
        this.resetForm()
        await this.fetchProducts()
      } catch (error) {
        if (error.response?.status === 422) {
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
        await this.deleteProductImage(product.id)
        this.message = 'Product deleted successfully.'
        this.messageType = 'success'
        await this.fetchProducts()

        if (this.form.id === product.id) {
          this.resetForm()
        }
      } catch (error) {
        this.message = error.response?.data?.message || error.message || 'Unable to delete the product right now.'
        this.messageType = 'error'
      }
    },

    formatPrice(price) {
      return Number(price).toFixed(2)
    },

    scrollToTop() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth',
      })
    },
  },
}
</script>
