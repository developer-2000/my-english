import { mapState, mapActions, mapGetters } from 'vuex';
import $ from "jquery";
export default {
    computed: {
        ...mapState(['currentUser']), // Подключение к состоянию Vuex
        ...mapGetters(['getUser']), // Подключение к геттеру Vuex
    },
    methods: {
        ...mapActions(['updateUser']),
    },
}


