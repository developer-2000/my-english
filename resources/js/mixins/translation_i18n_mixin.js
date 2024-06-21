// {{ $t('auth.failed') }}

import { loadTranslations, i18n } from '../load_i18n';
import user_mixin from "../mixins/user_mixin";

export default {
    mixins: [
        user_mixin
    ],
    data() {
        return {
            codeLanguage: [
                {"ru": "Русский"},{"en": "English"},{"ro": "Română"},
            ],
        };
    },
    methods: {
        // Взвращает Код языка или его название
        getLanguageText(value = "code", subject = 'interface') {
            // Получаем объект с языками пользователя
            const code = this.getUser !== null ?
                this.getUser.language_user :
                this.user.language_user

            if (code !== undefined) {
                // Если показать код языка
                if (value === "code") {
                    // Язык обучения
                    if (subject === "learn") {
                        return code.learn_language.language;
                    }
                    // Язык интерфейса
                    if (subject === "interface") {
                        return code.interface_language.language;
                    }
                }
                // Если показать слово языка
                if (value === "word") {
                    let languageCode;
                    if (subject === "learn") {
                        languageCode = code.learn_language.language;
                    }
                    else if (subject === "interface") {
                        languageCode = code.interface_language.language;
                    }

                    // Находим объект с соответствующим языком в массиве codeLanguage
                    const languageObject = this.codeLanguage.find(item => item.hasOwnProperty(languageCode));
                    if (languageObject) {
                        // показать слово языка
                        return languageObject[languageCode];
                    }
                }
            }

            // Возвращаем значение по умолчанию
            return "ru";
        },
        // Вызов импортированной функции для перевода интерфейса
        async loadTranslations(locale) {
            await loadTranslations(locale);
        },
        // для удаления указанно сессии переводов
        removeTranslations (locale) {
            sessionStorage.removeItem(`translations_${locale}`);
        }
    },
    computed: {
        // Пример вычисляемого свойства для доступа к объекту i18n
        $t() {
            return i18n.t.bind(i18n);
        }
    },
}


