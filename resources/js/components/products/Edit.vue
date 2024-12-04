<template>
    <ProductForm :id="id" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import ProductForm from './ProductForm.vue'

const route = useRoute()
const id = ref(route.params.id)
</script>
