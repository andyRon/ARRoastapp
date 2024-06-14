import './bootstrap';
import { createApp } from 'vue';
import router from './routes.js'
import store from "./store.js";

const app = createApp({
    router,
    store
})
app.mount('#app')
app.config.devtools=true


console.log(router.getRoutes())


