import {createApp} from "vue";
import Vuex from 'vuex';
import 'es6-promise';

const app = createApp({})
app.use(Vuex)

import {cafes} from "./modules/cafes.js";
import {brewMethods} from "./modules/brewMethods.js";

export default new Vuex.Store({
    modules: {
        cafes,
        brewMethods
    }
})
