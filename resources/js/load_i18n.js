import Vue from 'vue';
import VueI18n from 'vue-i18n';
import axios from 'axios';
import store from './store';

Vue.use(VueI18n);

const i18n = new VueI18n({
    locale: store.getters.getLearnLanguage,
    fallbackLocale: 'en',
    messages: {},
});

const loadTranslations = async locale => {
    // Проверяем, есть ли переводы в sessionStorage
    const storedTranslations = sessionStorage.getItem(`translations_${locale}`);
    if (storedTranslations) {
        // Если переводы найдены в sessionStorage, используем их
        i18n.setLocaleMessage(locale, JSON.parse(storedTranslations));
        return;
    }

    try {
        // Если переводы не найдены, загружаем их с сервера
        const response = await axios.get(`/translations?lang=${locale}`);
        let translations = [];

        if (response.data.error === null) {
            translations = response.data.data.translations;
        } else {
            console.error(response.data.error);
        }

        // Сохраняем переводы в sessionStorage
        sessionStorage.setItem(`translations_${locale}`, JSON.stringify(translations));

        // Устанавливаем переводы для текущего языка
        i18n.setLocaleMessage(locale, translations);
    } catch (error) {
        console.error('Error loading translations:', error);
    }
};

// Загрузка переводов при инициализации приложения
loadTranslations(store.getters.getLearnLanguage);

export { i18n, loadTranslations }; // Убедитесь, что экспорт происходит корректно
