<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800">404</h1>
            <p class="mt-4 text-xl text-gray-600">頁面不存在</p>
            <router-link
                to="/products"
                class="mt-6 inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
            >
                返回首頁
            </router-link>
        </div>
    </div>
</template>
