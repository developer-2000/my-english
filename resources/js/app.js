require('./bootstrap');
window.Vue = require('vue').default;

import VueRouter from 'vue-router'
window.Vue.use(VueRouter);
import router from './router'

import Vuelidate from 'vuelidate'
window.Vue.use(Vuelidate)

import VueTippy, { TippyComponent } from "vue-tippy";
window.Vue.use(VueTippy);
window.Vue.component("tippy", TippyComponent);

import http_client from './services/http_client';
window.Vue.prototype.$http = http_client;

import VueSweetalert2 from 'vue-sweetalert2';
window.Vue.use(VueSweetalert2);

const app = new Vue({
    el: '#app',
    router
});

