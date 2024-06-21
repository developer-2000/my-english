import Vue from 'vue';
import Vuex from 'vuex';
import language from './language.js';
import user from './user.js';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        language,
        user,
    }
});
