import { createRouter, createWebHistory } from "vue-router";
import Home from "./pages/Home.vue";
import Layout from "./pages/Layout.vue";
import Cafes from "./pages/Cafes.vue";
import NewCafe from "./pages/NewCafe.vue";
import Cafe from  "./pages/Cafe.vue";

const routes = [
    {
        path: '/',
        name: 'layout',
        component: Layout,
        children: [
            {
                path: '/',
                name: 'home',
                component: Home,
            },
            {
                path: '/cafes',
                name: 'cafes',
                // component: () => import('./pages/Cafes.vue'),
                component: Cafes,
            },
            {
                path: '/cafes/new',
                name: 'newcafe',
                // component: () => import('./pages/NewCafe.vue'),
                component: NewCafe,
            },
            {
                path: '/cafes/:id',
                name: 'cafe',
                // component: () => import('./pages/Cafe.vue'),
                component: Cafe,
            },
        ]
    }
];

const router = createRouter({
        history: createWebHistory(),
        routes
});

export default router;


