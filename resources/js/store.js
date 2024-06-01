import {createStore} from 'vuex';
import 'es6-promise';
import {cafes} from "./modules/cafes.js";
import {brewMethods} from "./modules/brewMethods.js";


const store = createStore({
    state () {
        return {
            count: 0
        }
    },
    modules: {
        cafes,
        brewMethods
    }
})
export default store;
// 导出一个新的Vuex数据存储器，将其应用到 Vue 实例并让所有模块在各个组件和路由中都可以访问

