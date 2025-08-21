<template>
    <div class="box-container-page">
        <!-- Header меню -->
        <header class="d-flex justify-content-between align-items-center p-3">
            <!-- Язык изучения -->
            <a href="/" class="header-element header-main-link">
                <div class="header-logo">M</div>
                {{getLanguageText("word", "learn")}}
            </a>

            <!-- Theme Toggle Button -->


            <!-- Выпадающее меню справа -->
            <div class="dropdown">
                <!-- кнопка меню -->
                <button class="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="dropdownMenuButton"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    {{ user.name ?? $t('all.menu') }}
                </button>
                <!-- меню -->
                <div class="dropdown-menu dropdown-menu-right"
                     aria-labelledby="dropdownMenuButton"
                >
                    <!-- вызов модалки изучения языка -->
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#languageLearn">
                        {{ $t('all.language_learning') }}
                    </a>
                    <!-- кнопка выхода -->
                    <a class="dropdown-item" href="/auth/logout">
                        {{ $t('all.logout') }}
                    </a>
                </div>
            </div>
        </header>

        <!-- Левое меню index страницы-->
        <div class="main-page">
            <div class="left-menu-container">
                <nav id="left_menu">
                    <router-link to="/page-list-words" class="nav-menu-item" exact>
                        <div class="nav-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="nav-text">{{ $t('all.word_list') }}</span>
                    </router-link>

                    <router-link to="/page-word-sentences" class="nav-menu-item" exact>
                        <div class="nav-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <span class="nav-text">{{ $t('all.word_sentences') }}</span>
                    </router-link>
                </nav>
            </div>

            <!-- Вставляемый контент страницы -->
            <div class="content-wrapper">
                <router-view :user="user"></router-view>
            </div>
        </div>

        <!-- Модалка выбора языка изучения -->
        <div class="modal fade" id="languageLearn"
             tabindex="-1"
             aria-labelledby="languageLearnLabel"
             aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="languageLearnLabel">
                            {{ $t('all.study_language') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="languagesList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $ from "jquery";
import response_methods_mixin from "../mixins/response_methods_mixin";
import translation_i18n_mixin from "../mixins/translation_i18n_mixin";
import user_mixin from "../mixins/user_mixin";



export default {
    data() {
        return {
            // Добавляем переменную для хранения списка языков
            languages: [],
        };
    },
    mixins: [
        response_methods_mixin,
        translation_i18n_mixin,
        user_mixin
    ],
    components: {
    },
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    methods: {
        // Загружает выбор языков для модалки выбора изучения языков
        async loadLanguages() {
            // запрос для получения списка языков
            try {
                const response = await this.$http.post(`${this.$http.webUrl()}get-languages`, {});
                if(this.checkSuccess(response)){
                    this.languages = response.data.data.learn_languages || []
                    let languagesList = $('#languagesList');
                    languagesList.empty();

                    if (this.languages) {
                        // Перебираем языки и создаем для каждого кнопку
                        $.each(this.languages, (language, code) => {
                            let languageOption = $('<div class="language-option"></div>');
                            languageOption.attr('data-code', code);
                            languageOption.text(language);
                            languagesList.append(languageOption);

                            // 2 Язык изучения выбран
                            languageOption.on('click', (event) => {
                                const selectedLanguage = $(event.currentTarget).data('code');
                                this.selectLanguage(selectedLanguage);
                                $('#languageLearn').modal('hide');
                            });
                        });
                    }
                }
            } catch (e) {
                console.error('Error fetching languages:', e);
            }
        },
        // Сохраняет выбор изучаемого языка в базе для юзера
        async selectLanguage(language) {
            // сохранения выбранного языка изучения
            let data = { language: language };
            try {
                const response = await this.$http.post(`${this.$http.webUrl()}set-language-learn-user`, data);
                if(this.checkSuccess(response)){
                    if(response?.data?.data?.user){
                        this.updateUser(response.data.data.user)
                    }

                    this.$store.commit('setLearnLanguage', language)
                    await this.loadTranslations(language);
                }
            } catch (e) {
                console.error('Error fetching languages:', e);
            }
        },


    },
    mounted() {
        $('#languageLearn').on('show.bs.modal', (e) => {
            this.loadLanguages();
        });
        // установить язык интерфейса пользователя

        if(this.user?.language_user?.interface_language?.language){
            this.updateUser(this.user)
            const code_interface = this.getLanguageText()
            this.$store.commit('setLearnLanguage', code_interface)
            this.loadTranslations(code_interface)
        }
    },
};
</script>

<style scoped>
/* Ваши стили */
</style>
