import { createRouter, createWebHistory } from "vue-router";
import Home from "./pages/Home.vue";
import Layout from "./layouts/Layout.vue";
import Cafes from "./pages/Cafes.vue";
import NewCafe from "./pages/NewCafe.vue";
import Cafe from  "./pages/Cafe.vue";
import Profile from "./pages/Profile.vue";

const routes = [
    {
        path: '/',
        redirect: {name: 'cafes'},
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
            {
                path: '/profile',
                name: '/profile',
                component: Profile,
                // beforeEnter:
            }
        ]
    }
];

const router = createRouter({
        history: createWebHistory(),
        routes
});

export default router;


