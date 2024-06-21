<template>
    <div>
        <!-- Header меню -->
        <header class="d-flex justify-content-between align-items-center p-3">
            <a href="/" class="header-element header-main-link">
                {{getLanguageText("word", "learn")}}
            </a>
            <!-- Выпадающее меню справа -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="dropdownMenuButton"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                >
                    {{ user.name ?? $t('all.menu') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#languageLearn">
                        {{ $t('all.language_learning') }}
                    </a>
                </div>
            </div>
        </header>

        <!-- Левое меню index страницы-->
        <div class="main-page">
            <ul id="left_menu">
                <li>
                    <router-link to="/page-list-words" class="left_menu" exact>
                        {{ $t('all.word_list') }}
                    </router-link>
                </li>
                <li>
                    <router-link to="/page-word-sentences" class="left_menu" exact>
                        {{ $t('all.word_sentences') }}
                    </router-link>
                </li>
            </ul>

            <!-- Вставляемый контент страницы -->
            <router-view :user="user"></router-view>
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
            this.$store.commit('setLearnLanguage', this.getLanguageText())
            this.updateUser(this.user)
            this.loadTranslations(this.getLanguageText())
        }
    },
};
</script>

<style scoped>
/* Ваши стили */
</style>
