import { createRouter, createWebHashHistory } from "vue-router";

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
        history: createWebHashHistory(),
        routes
});

export default router;


