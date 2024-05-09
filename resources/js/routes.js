// import { createApp } from "vue";
// import { createRouter, createWebHashHistory } from "vue-router";
import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use( VueRouter )


export default new VueRouter({
    routes: [
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
})
