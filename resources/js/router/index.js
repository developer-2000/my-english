import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        name: 'PAGE_INDEX',
        component: () => import('../components/Pages/PageIndex.vue')
    },
    {
        path: '/page_list_words',
        name: 'PAGE_LIST_WORDS',
        component: () => import('../components/Pages/PageListWords.vue')
    },
    {
        path: '/page_word_sentences',
        name: 'PAGE_WORD_SENTENCES',
        component: () => import('../components/Pages/PageWordSentences.vue')
    },
];


const index = new VueRouter({
    mode: 'history',
    routes,
});

export default index
