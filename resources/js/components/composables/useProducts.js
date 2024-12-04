// resources/js/composables/useProducts.js
import { ref } from 'vue'
import axios from 'axios'

export function useProducts() {
    const products = ref([])
    const errors = ref({})

    const getProducts = async () => {
        const response = await axios.get('/products')
        products.value = response.data.data
    }

    const getProduct = async (id) => {
        const response = await axios.get(`/products/${id}`)
        return response.data.data
    }

    const deleteProduct = async (id) => {
        await axios.delete(`/products/${id}`)
    }

    return {
        products,
        errors,
        getProducts,
        getProduct,
        deleteProduct
    }
}
