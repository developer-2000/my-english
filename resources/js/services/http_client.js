// import axios from "axios";
//
// export default {
//     async get(url, data = {}, headers = {}) {
//         return await axios.get(url, {data, headers});
//     },
//     async post(url, data = {}, headers = {}) {
//         return await axios.post(url, data, {headers: headers})
//     },
//     async patch(url, data = {}, headers = {}) {
//         return await axios.patch(url, data, {headers: headers})
//     },
//     apiUrl() {
//         return 'http://english.my/api/';
//     },
//     webUrl() {
//         return 'http://english.my/';
//     },
// }
import axios from 'axios';

// Добавляем интерцепторы для логирования
axios.interceptors.request.use(
    config => {
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        return Promise.reject(error);
    }
);

export default {
    async get(url, data = {}, headers = {}) {
        try {
            // Добавляем CSRF токен в заголовки
            const csrfToken = this.getCsrfToken();
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await axios.get(url, { data, headers });
            return response;
        } catch (e) {
            return this.handleError(e); // Обработка ошибки
        }
    },
    async post(url, data = {}, headers = {}, showAlert = true) {
        try {
            // Добавляем CSRF токен в заголовки
            const csrfToken = this.getCsrfToken();
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await axios.post(url, data, { headers: headers });
            return response;
        } catch (e) {
            return this.handleError(e, showAlert); // Обработка ошибки с флагом
        }
    },
    async patch(url, data = {}, headers = {}) {
        try {
            const response = await axios.patch(url, data, { headers: headers });
            return response;
        } catch (e) {
            return this.handleError(e); // Обработка ошибки
        }
    },
    // Обработка ошибок
    handleError(e, showAlert = true) {
        if (e.response) {
            // Если ответ от сервера есть, обрабатываем ошибку
            if (e.response.status === 422) {
                // Обработка ошибки валидации
                const responseData = e.response.data;

                // Проверяем если это наш кастомный формат с message
                if (responseData && responseData.data && responseData.data.message) {
                    const errorMessage = responseData.data.message;

                    if (showAlert) {
                        alert('Ошибка:\n' + errorMessage);
                    }

                    return { error: { message: errorMessage } };
                }

                // Стандартный формат ошибок валидации Laravel
                if (responseData && typeof responseData === 'object') {
                    const errorMessages = [];

                    for (const field in responseData) {
                        if (Object.prototype.hasOwnProperty.call(responseData, field)) {
                            const fieldErrors = responseData[field];
                            if (Array.isArray(fieldErrors)) {
                                fieldErrors.forEach(error => {
                                    errorMessages.push(error);
                                });
                            } else if (typeof fieldErrors === 'string') {
                                errorMessages.push(fieldErrors);
                            }
                        }
                    }

                    const errorString = errorMessages.join('\n');

                    if (showAlert) {
                        alert('Ошибки:\n' + errorString);
                    }

                    return { error: responseData };
                }

                return { error: 'Ошибка валидации' };
            } else {
                // Обрабатываем другие ошибки (например, 500 или 404)
                return { error: 'Произошла ошибка при запросе. Попробуйте позже.' };
            }
        } else if (e.request) {
            // Если запрос был отправлен, но ответа нет (например, из-за проблем с сетью)
            return { error: 'Нет ответа от сервера. Проверьте ваше подключение.' };
        } else {
            // Если ошибка произошла при настройке запроса
            return { error: 'Ошибка запроса. Попробуйте позже.' };
        }
    },
    apiUrl() {
        return 'http://english.my/api/';
    },
    webUrl() {
        return 'http://english.my/';
    },
    // Получить CSRF токен
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    },
};
