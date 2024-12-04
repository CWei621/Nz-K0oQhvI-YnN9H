// resources/js/app.js
import { createApp } from 'vue'
import router from './router'
import App from './components/App.vue'
import axios from 'axios'

axios.defaults.baseURL = '/api'

const app = createApp(App)
app.use(router)
app.mount('#app')
