// import { createApp } from "vue";
import { createRouter, createWebHashHistory } from "vue-router";
// import VueRouter from 'vue-router'


const routes = [
    {
        path: '/',
        name: 'layout',
        component: import('./pages/Layout.vue'),
        children: [
            {
                path: '/',
                name: 'home',
                component: () => import('./pages/Home.vue')
            },
            {
                path: '/cafes',
                name: 'cafes',
                component: () => import('./pages/Cafes.vue')
            },
            {
                path: '/cafes/new',
                name: 'newcafe',
                component: () => import('./pages/NewCafe.vue')
            },
            {
                path: '/cafes/:id',
                name: 'cafe',
                component: () => import('./pages/Cafe.vue')
            },
        ]
    }
];

const router = createRouter({
        routes
});

export default router;


