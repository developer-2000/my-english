import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter);

const routes = [
    {
        path: '/page_list_words',
        name: 'page_list_words',
        component: () => import('../components/Pages/PageListWords.vue')
    },
    {
        path: '/word_sentences',
        name: 'word_sentences',
        component: () => import('../components/Pages/PageWordSentences.vue')
    },
];


const index = new VueRouter({
    mode: 'history',
    routes,
});

export default index
