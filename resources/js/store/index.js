import Vue from 'vue';
import Vuex from 'vuex';
import language from './language.js';
import user from './user.js';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        pagination: {
            perPage: parseInt(localStorage.getItem('pagination_per_page')) || 10,
        },
    },
    mutations: {
        SET_PER_PAGE(state, perPage) {
            state.pagination.perPage = perPage;
            localStorage.setItem('pagination_per_page', perPage);
        },
    },
    getters: {
        getPerPage: state => {
            return state.pagination.perPage;
        },
    },
    modules: {
        language,
        user,
    },
});
