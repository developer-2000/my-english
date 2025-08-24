<template>
    <div class="box-container-page">
        <!-- Header меню -->
        <header class="d-flex justify-content-between align-items-center p-3">
            <!-- Язык изучения -->
            <router-link to="/" class="header-element header-main-link">
                <div class="header-logo">{{ getLanguageText('word', 'learn').charAt(0) }}</div>
                {{ getLanguageText('word', 'learn').substring(1) }}
            </router-link>

            <!-- Theme Toggle Button -->

            <!-- Выпадающее меню справа -->
            <div class="dropdown">
                <!-- кнопка меню -->
                <button
                    id="dropdownMenuButton"
                    class="btn btn-secondary dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    {{ user.name ?? $t('all.menu') }}
                </button>
                <!-- меню -->
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <!-- вызов модалки изучения языка -->
                    <a
                        class="dropdown-item"
                        href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#languageLearn"
                    >
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
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                ></path>
                            </svg>
                        </div>
                        <span class="nav-text">{{ $t('all.word_list') }}</span>
                    </router-link>

                    <router-link to="/page-word-sentences" class="nav-menu-item" exact>
                        <div class="nav-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                ></path>
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
        <div
            id="languageLearn"
            class="modal fade"
            tabindex="-1"
            aria-labelledby="languageLearnLabel"
            aria-hidden="true"
            @click.self="closeLanguageModal"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="languageLearnLabel" class="modal-title">
                            {{ $t('all.study_language') }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="Close"
                            @click="closeLanguageModal"
                        ></button>
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
    import $ from 'jquery';
    import response_methods_mixin from '../mixins/response_methods_mixin.js';
    import translation_i18n_mixin from '../mixins/translation_i18n_mixin.js';
    import user_mixin from '../mixins/user_mixin.js';

    export default {
        components: {},
        mixins: [response_methods_mixin, translation_i18n_mixin, user_mixin],
        props: {
            user: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
                // Добавляем переменную для хранения списка языков
                languages: [],
                // Ссылки на элементы для удаления обработчиков событий
                modalElement: null,
                modalShowHandler: null,
            };
        },
        mounted() {
            // Используем Bootstrap 5 API для события показа модалки
            const modalElement = document.getElementById('languageLearn');
            if (modalElement) {
                this.modalShowHandler = () => {
                    this.loadLanguages();
                };
                modalElement.addEventListener('show.bs.modal', this.modalShowHandler);
                // Сохраняем ссылку на элемент для последующего удаления
                this.modalElement = modalElement;
            }
            // установить язык интерфейса пользователя

            if (this.user?.language_user?.interface_language?.language) {
                this.updateUser(this.user);
                const code_interface = this.getLanguageText();
                this.$store.commit('setLearnLanguage', code_interface);
                this.loadTranslations(code_interface);
            }

            // Добавляем стили для убирания подчеркивания
            this.addRouterLinkStyles();
            
            // Дополнительно применяем стили после рендера
            this.$nextTick(() => {
                this.applyRouterLinkStyles();
            });
        },
        beforeDestroy() {
            // Удаляем обработчик события модалки
            if (this.modalElement && this.modalShowHandler) {
                this.modalElement.removeEventListener('show.bs.modal', this.modalShowHandler);
            }
        },
        methods: {
            // Загружает выбор языков для модалки выбора изучения языков
            async loadLanguages() {
                // запрос для получения списка языков
                try {
                    const response = await this.$http.post(
                        `${this.$http.webUrl()}get-languages`,
                        {}
                    );
                    if (this.checkSuccess(response)) {
                        this.languages = response.data.data.learn_languages || [];
                        const languagesList = $('#languagesList');
                        languagesList.empty();

                        if (this.languages) {
                            // Перебираем языки и создаем для каждого кнопку
                            $.each(this.languages, (language, code) => {
                                const languageOption = $('<div class="language-option"></div>');
                                languageOption.attr('data-code', code);
                                languageOption.text(language);
                                languagesList.append(languageOption);

                                // 2 Язык изучения выбран
                                languageOption.on('click', async event => {
                                    const selectedLanguage = $(event.currentTarget).data('code');
                                    await this.selectLanguage(selectedLanguage);
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
                const data = { language: language };
                try {
                    const response = await this.$http.post(
                        `${this.$http.webUrl()}set-language-learn-user`,
                        data
                    );
                    if (this.checkSuccess(response)) {
                        if (response?.data?.data?.user) {
                            this.updateUser(response.data.data.user);
                        }

                        this.$store.commit('setLearnLanguage', language);
                        await this.loadTranslations(language);

                        // Закрываем модалку после успешного сохранения
                        this.closeLanguageModal();
                    }
                } catch (e) {
                    console.error('Error fetching languages:', e);
                }
            },
            // Закрыть модалку выбора языка
            closeLanguageModal() {
                const modalElement = document.getElementById('languageLearn');
                if (modalElement) {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    } else {
                        modalElement.style.display = 'none';
                        modalElement.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        // Восстанавливаем скролл body
                        document.body.style.overflow = '';
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                    }
                }
            },
            // Добавляем стили для router-link
            addRouterLinkStyles() {
                const style = document.createElement('style');
                style.textContent = `
                    /* Убираем псевдоэлемент ::after который создает подчеркивание */
                    .router-link-active::after,
                    .router-link-exact-active::after,
                    .header-main-link.router-link-active::after,
                    .header-main-link.router-link-exact-active::after,
                    a.router-link-active::after,
                    a.router-link-exact-active::after,
                    a.header-main-link.router-link-active::after,
                    a.header-main-link.router-link-exact-active::after {
                        display: none !important;
                        content: none !important;
                    }
                    
                    /* Максимальная специфичность для убирания подчеркивания */
                    a.router-link-active,
                    a.router-link-exact-active,
                    a.header-main-link.router-link-active,
                    a.header-main-link.router-link-exact-active,
                    .header-main-link.router-link-active,
                    .header-main-link.router-link-exact-active,
                    .header-element.header-main-link.router-link-active,
                    .header-element.header-main-link.router-link-exact-active,
                    a.header-element.header-main-link.router-link-active,
                    a.header-element.header-main-link.router-link-exact-active {
                        text-decoration: none !important;
                        text-decoration-line: none !important;
                        text-decoration-style: none !important;
                        text-decoration-color: transparent !important;
                        cursor: pointer !important;
                    }
                    
                    /* Убираем подчеркивание для всех header-main-link */
                    .header-main-link,
                    a.header-main-link,
                    .header-element.header-main-link,
                    a.header-element.header-main-link {
                        text-decoration: none !important;
                        text-decoration-line: none !important;
                        text-decoration-style: none !important;
                        text-decoration-color: transparent !important;
                        cursor: pointer !important;
                    }
                    
                    /* Убираем подчеркивание при наведении */
                    .header-main-link:hover,
                    a.header-main-link:hover,
                    .header-element.header-main-link:hover,
                    a.header-element.header-main-link:hover {
                        text-decoration: none !important;
                        text-decoration-line: none !important;
                        text-decoration-style: none !important;
                        text-decoration-color: transparent !important;
                    }
                    
                    /* Глобально убираем подчеркивание для всех router-link */
                    a[class*="router-link"] {
                        text-decoration: none !important;
                        text-decoration-line: none !important;
                        text-decoration-style: none !important;
                        text-decoration-color: transparent !important;
                        cursor: pointer !important;
                    }
                    
                    /* Изменяем цвет фона логотипа на #007bff */
                    .header-logo {
                        background-color: #007bff !important;
                    }
                `;
                document.head.appendChild(style);
            },
            // Применяем стили напрямую к элементам
            applyRouterLinkStyles() {
                const routerLinks = document.querySelectorAll('.router-link-active, .router-link-exact-active, .header-main-link');
                routerLinks.forEach(link => {
                    link.style.textDecoration = 'none';
                    link.style.textDecorationLine = 'none';
                    link.style.textDecorationStyle = 'none';
                    link.style.textDecorationColor = 'transparent';
                    link.style.cursor = 'pointer';
                });
                
                // Изменяем цвет фона логотипа
                const headerLogos = document.querySelectorAll('.header-logo');
                headerLogos.forEach(logo => {
                    logo.style.backgroundColor = '#007bff';
                });
            },
        },
    };
</script>

<style scoped>
    /* Ваши стили */
</style>

<style>
/* Глобальные стили для router-link */
.router-link-active,
.router-link-exact-active {
    text-decoration: none !important;
}

a.router-link-active,
a.router-link-exact-active {
    text-decoration: none !important;
    cursor: pointer !important;
}

.header-main-link.router-link-active,
.header-main-link.router-link-exact-active {
    text-decoration: none !important;
    cursor: pointer !important;
}

/* Убираем подчеркивание для всех ссылок с классом header-main-link */
.header-main-link {
    text-decoration: none !important;
    cursor: pointer !important;
}

.header-main-link:hover {
    text-decoration: none !important;
}
</style>
