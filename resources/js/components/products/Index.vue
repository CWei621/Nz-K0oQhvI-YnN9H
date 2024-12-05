<template>
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">商品列表</h1>
            <router-link
                :to="{ name: 'products.create' }"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
            >
                新增商品
            </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="product in products"
                 :key="product.id"
                 class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold">{{ product.name }}</h3>
                <p class="text-gray-600 text-sm mt-1">{{ product.description }}</p>
                <div class="mt-2">
                    <p class="text-gray-800">價格: ${{ product.price }}</p>
                    <p class="text-gray-800">庫存: {{ product.stock }}</p>
                </div>
                <div class="mt-4 flex space-x-2">
                    <router-link
                        :to="{ name: 'products.edit', params: { id: product.id }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm"
                    >
                        編輯
                    </router-link>
                    <button
                        @click="handleDelete(product)"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm"
                    >
                        刪除
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, onUnmounted, inject } from 'vue'
import { useProducts } from '../composables/useProducts'

const { products, getProducts, deleteProduct } = useProducts()
const notify = inject('notify');

const handleDelete = async (product) => {
    if (!confirm(`確定要刪除 ${product.name} ?`)) return

    await deleteProduct(product.id)
    await getProducts()
}

onMounted(() => {
    getProducts()

    window.Echo.channel('products')
      .listen('ProductEvent', (event) => {
        const productLink = `/products/${event.product.id}/edit`
        notify.successWithLink('商品列表已更新', productLink)
      })
})

onUnmounted(() => {
    window.Echo.leave('products')
})
</script>
