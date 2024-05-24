import {createApp} from "vue";
import Vuex from 'vuex';
import 'es6-promise';

const app = createApp({})
app.use(Vuex)

import {cafes} from "./modules/cafes.js";
import {brewMethods} from "./modules/brewMethods.js";

// 导出一个新的Vuex数据存储器，将其应用到 Vue 实例并让所有模块在各个组件和路由中都可以访问
export default new Vuex.Store({
    modules: {
        cafes,
        brewMethods
    }
})
