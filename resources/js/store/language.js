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
            console.log('🔍 [VUEX_LANGUAGE] setLearnLanguage mutation called with:', locale);
            console.log('🔍 [VUEX_LANGUAGE] Old learn_language value:', state.learn_language);
            state.learn_language = locale;
            console.log('🔍 [VUEX_LANGUAGE] New learn_language value:', state.learn_language);
        }
    },
    getters: {
        // Геттер для получения текущего языка изучения
        getLearnLanguage(state) {
            console.log('🔍 [VUEX_LANGUAGE] getLearnLanguage getter called, returning:', state.learn_language);
            return state.learn_language;
        }
    },
};
