// Полифил для Node.js переменных в браузере
if (typeof global === 'undefined') {
    var global = globalThis;
}

import './bootstrap';
import 'bootstrap';
import '@popperjs/core';

import Vue from 'vue';

// Отключаем Vue DevTools предупреждения и dev режим предупреждения
Vue.config.productionTip = false;
Vue.config.devtools = false;
Vue.config.silent = true;

import VueRouter from 'vue-router'
Vue.use(VueRouter);
import router from './router'

import Vuelidate from 'vuelidate'
Vue.use(Vuelidate)

import VueTippy, { TippyComponent } from "vue-tippy";
Vue.use(VueTippy);
Vue.component("tippy", TippyComponent);

import http_client from './services/http_client';
Vue.prototype.$http = http_client;

import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

// Добавляем Vuex
import Vuex from 'vuex';
Vue.use(Vuex);

// Импортируем наш store
import store from './store';

import hallway from './components/Hallway.vue'

const app = new Vue({
    el: '#app',
    router,
    store, // подключаем Vuex хранилище к приложению
    components:{
        hallway
    },
});

