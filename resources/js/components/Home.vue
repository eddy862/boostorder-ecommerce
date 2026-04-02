<template>
  <section class="space-y-8">
    <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-400 via-orange-300 to-amber-200 px-6 py-10 text-white shadow-xl shadow-orange-200 sm:px-10">
      <p class="text-sm font-semibold uppercase tracking-[0.28em] text-orange-100">Welcome</p>
      <h1 class="mt-3 max-w-2xl text-4xl font-black leading-tight sm:text-5xl">
        Shop your favorites faster with one clean storefront.
      </h1>
      <p class="mt-4 max-w-2xl text-sm leading-7 text-orange-50 sm:text-base">
        Browse products, review your cart, and keep track of every order from one place with a simple Shopee-like experience.
      </p>

      <div class="mt-8 flex flex-wrap gap-3">
        <router-link
          to="/products"
          class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-orange-500 shadow-md transition hover:bg-orange-50"
        >
          Start Shopping
        </router-link>
        <router-link
          to="/cart"
          class="rounded-xl border border-white/50 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20"
        >
          View Cart
        </router-link>
      </div>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
      <router-link
        to="/products"
        class="rounded-2xl border border-orange-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
      >
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">Products</p>
        <h2 class="mt-3 text-2xl font-black text-gray-800">Explore the catalog</h2>
        <p class="mt-2 text-sm leading-6 text-gray-500">
          Search available items, adjust quantity, and add them to cart or buy instantly.
        </p>
      </router-link>

      <router-link
        to="/cart"
        class="rounded-2xl border border-orange-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
      >
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">Cart</p>
        <h2 class="mt-3 text-2xl font-black text-gray-800">Review before checkout</h2>
        <p class="mt-2 text-sm leading-6 text-gray-500">
          Update quantities, remove products, and continue straight to checkout when ready.
        </p>
      </router-link>

      <router-link
        to="/orders"
        class="rounded-2xl border border-orange-100 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
      >
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-orange-400">Orders</p>
        <h2 class="mt-3 text-2xl font-black text-gray-800">Track order history</h2>
        <p class="mt-2 text-sm leading-6 text-gray-500">
          Check your latest purchases and manage pending orders from a single page.
        </p>
      </router-link>
    </div>
  </section>
</template>
