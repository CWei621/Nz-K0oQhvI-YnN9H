// resources/js/app.js
import { createApp } from 'vue'
import router from './router'
import App from './components/App.vue'
import axios from 'axios'
import { Toast, options } from "./plugins/toast";
import NotificationService from "./services/NotificationService";

axios.defaults.baseURL = '/api'

const app = createApp(App)
app.use(router)
app.use(Toast, options);
app.provide('notify', new NotificationService());
app.mount('#app')
