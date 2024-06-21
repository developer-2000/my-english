import Vue from 'vue';
import Vuex from 'vuex';
import language from './language.js';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        language,
    }
});
