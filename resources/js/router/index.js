import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        name: 'PAGE_INDEX',
        component: () => import('../components/Pages/PageIndex.vue'),
    },
    {
        path: '/page-list-words',
        name: 'PAGE_LIST_WORDS',
        component: () => import('../components/Pages/PageListWords.vue'),
    },
    {
        path: '/page-word-sentences',
        name: 'PAGE_WORD_SENTENCES',
        component: () => import('../components/Pages/PageWordSentences.vue'),
    },
    {
        path: '/technical/page',
        name: 'PAGE_TECHNICAL',
        component: () => import('../components/Pages/PageTechnical.vue'),
    },
];

const index = new VueRouter({
    mode: 'history',
    routes,
});

export default index;
