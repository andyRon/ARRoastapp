import './bootstrap';
import { createApp } from 'vue';
import router from './routes.js'
import Home from "./pages/Home.vue";
import store from "./store.js";


// createApp(Home).use(router).mount('#app')

const app = createApp({
    router,
    store
})
app.mount('#app')
