// store.js
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default {
    state: {
        learn_language: 'ru'
    },
    mutations: {
        // Мутация для установки языка изучения
        setLearnLanguage(state, locale) {
            state.learn_language = locale;
        }
    },
    getters: {
        // Геттер для получения текущего языка изучения
        getLearnLanguage(state) {
            return state.learn_language;
        }
    },
};
