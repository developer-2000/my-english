require('./bootstrap');
window.Vue = require('vue').default;

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

const app = new Vue({
    el: '#app',
    router
});

