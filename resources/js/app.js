import './bootstrap';
import { createApp } from 'vue';
import router from './routes.js'
import Home from "./pages/Home.vue";


createApp(Home).use(router).mount('#app')
