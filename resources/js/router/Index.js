// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/products',
        name: 'products.index',
        component: () => import('../components/products/Index.vue')
    },
    {
        path: '/products/create',
        name: 'products.create',
        component: () => import('../components/products/Create.vue')
    },
    {
        path: '/products/:id/edit',
        name: 'products.edit',
        component: () => import('../components/products/Edit.vue')
    }
]

export default createRouter({
    history: createWebHistory(),
    routes
})
