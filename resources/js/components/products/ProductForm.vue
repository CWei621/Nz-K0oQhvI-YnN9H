<template>
    <div class="max-w-2xl mx-auto p-4">
        <h2 class="text-2xl font-bold mb-6">{{ isEdit ? '編輯商品' : '新增商品' }}</h2>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- 商品名稱 -->
            <div>
                <label class="block text-sm font-medium text-gray-700">商品名稱 *</label>
                <input
                    type="text"
                    v-model="form.name"
                    required
                    maxlength="255"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': errors.name }"
                >
                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
            </div>

            <!-- 描述 -->
            <div>
                <label class="block text-sm font-medium text-gray-700">描述</label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                ></textarea>
            </div>

            <!-- 價格 -->
            <div>
                <label class="block text-sm font-medium text-gray-700">價格 *</label>
                <input
                    type="number"
                    v-model="form.price"
                    required
                    min="0"
                    step="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': errors.price }"
                >
                <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price[0] }}</p>
            </div>

            <!-- 庫存 -->
            <div>
                <label class="block text-sm font-medium text-gray-700">庫存 *</label>
                <input
                    type="number"
                    v-model="form.stock"
                    required
                    min="0"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    :class="{ 'border-red-500': errors.stock }"
                >
                <p v-if="errors.stock" class="mt-1 text-sm text-red-600">{{ errors.stock[0] }}</p>
            </div>

            <!-- 是否啟用 -->
            <div>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.is_active"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">啟用商品</span>
                </label>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">商品圖片</label>

              <!-- Image Preview -->
              <div v-if="imagePreview || form.image_path" class="mb-4">
                <img
                  :src="imagePreview || `/api/products/image/${form.image_path?.split('/').pop()}`"
                  alt="Product preview"
                  class="h-32 w-32 object-cover rounded-lg shadow-sm"
                />
              </div>

              <!-- Upload Area -->
              <div class="flex items-center justify-center w-full">
                <label class="flex flex-col w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="mb-2 text-sm text-gray-500">
                      <span class="font-semibold">點擊上傳</span>或拖放
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF</p>
                  </div>
                  <input
                    type="file"
                    class="hidden"
                    accept="image/*"
                    @change="handleImageChange"
                  />
                </label>
              </div>
            </div>

            <!-- 按鈕 -->
            <div class="flex justify-end space-x-3">
                <router-link
                    to="/products"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    取消
                </router-link>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors"
                >
                    {{ isEdit ? '更新' : '新增' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useProducts } from '../composables/useProducts'
import axios from 'axios'

const imagePreview = ref('')
const imageFile = ref(null)
const errors = ref({})

const props = defineProps({
    id: {
        type: String,
        default: ''
    }
})

const router = useRouter()
const route = useRoute()
const { getProduct } = useProducts()

const isEdit = computed(() => !!props.id)

const form = ref({
    name: '',
    description: '',
    price: 0,
    stock: 0,
    is_active: true
})

onMounted(async () => {
    if (isEdit.value) {
        try {
            const response = await getProduct(props.id)
            form.value = {
                ...response,
                is_active: response.is_active === 1 || response.is_active === '1'
            }
        } catch (error) {
            console.error('Error fetching product:', error)
            router.push({ name: 'products.index' })
        }
    }
})

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const storeProduct = async (data) => {
    try {
        const response = await axios.post('/products', data)
        return response.data
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
        throw e
    }
}

const updateProduct = async (id, data) => {
    try {
        data.append('_method', 'PUT')  // Laravel PUT 模擬
        const response = await axios.post(`/products/${id}`, data)
        return response.data
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors
        }
        throw e
    }
}

const handleSubmit = async () => {
    try {
        const formData = new FormData()
        formData.append('name', form.value.name)
        formData.append('description', form.value.description || '')
        formData.append('price', form.value.price)
        formData.append('stock', form.value.stock)
        formData.append('is_active', form.value.is_active ? '1' : '0')

        if (imageFile.value) {
          formData.append('image', imageFile.value, imageFile.value.name)
        }

        if (isEdit.value) {
            await updateProduct(props.id, formData)
        } else {
            await storeProduct(formData)
        }

        router.push({ name: 'products.index' })
    } catch (error) {
        console.error('Error submitting form:', error)
    }
}

// Cleanup
onUnmounted(() => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
  }
})
</script>
