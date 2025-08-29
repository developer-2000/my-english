<template>
    <div class="technical-page">
        <div class="container-fluid">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="space-y-1">
                    <h1 class="text-3xl font-bold tracking-tight">Техническая страница</h1>
                </div>
            </div>
            <div class="table-wrapper">
                <div class="table-header">
                    <h3 class="table-title">Глаголы</h3>
                    <div class="table-actions"></div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="text-start">Тип глагола</th>
                            <th class="text-center">Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">Правильные глаголы</div>
                                        <div class="text-muted small">Глаголы с регулярными формами</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button id="but-regular-verbs" class="btn btn-success btn-sm me-2" @click="saveRegularVerbsInJSON">
                                    <i class="fas fa-download me-1"></i>
                                    Сохранить в JSON
                                </button>
                                <button id="but-save-regular-verbs"  class="btn btn-warning btn-sm" @click="serverSaveRegularVerbsInDatabase">
                                    <i class="fas fa-save me-1"></i>
                                    Сохранить в базу
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">Неправильные глаголы</div>
                                        <div class="text-muted small">Глаголы с нерегулярными формами</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button id="but-irregular-verbs"  class="btn btn-success btn-sm me-2" @click="saveIrregularVerbsInJSON">
                                    <i class="fas fa-download me-1"></i>
                                    Сохранить в JSON
                                </button>
                                <button id="but-save-irregular-verbs" class="btn btn-warning btn-sm" @click="serverSaveIrregularVerbsInDatabase">
                                    <i class="fas fa-save me-1"></i>
                                    Сохранить в базу
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import responseMethodsMixin from '../../mixins/response_methods_mixin.js';

export default {
    name: 'PageTechnical',
    mixins: [responseMethodsMixin],
    data() {
        return {
            // Данные компонента
        };
    },
    methods: {
        // SERVER - Сохранить в JSON правильные глаголы
        async serverSaveRegularVerbsInJSON() {
            try {
                const response = await this.$http.get('/technical/regular-verbs');

                if (this.checkSuccess(response)) {
                    const result = response.data.data;
                    this.message(result.message, 'success');
                } else {
                    this.message('Ошибка при получении данных', 'error');
                }
            } catch (error) {
                console.error('Ошибка при получении правильных глаголов:', error);
                this.message('Ошибка при получении данных с сервера', 'error');
            }
        },
        // SERVER - Сохранить в JSON не правильные глаголы
        async serverSaveIrregularVerbsInJSON() {
            try {
                const response = await this.$http.get('/technical/irregular-verbs');

                if (this.checkSuccess(response)) {
                    const result = response.data.data;
                    this.message(result.message, 'success');
                } else {
                    this.message('Ошибка при получении данных', 'error');
                }
            } catch (error) {
                console.error('Ошибка при получении неправильных глаголов:', error);
                this.message('Ошибка при получении данных с сервера', 'error');
            }
        },
        // SERVER - Сохранить из JSON в базу правильные глаголы
        async serverSaveRegularVerbsInDatabase() {
            try {
                // Confirm
                const result = await this.showSaveVerbsConfirmDialog('regular');

                if (result.isConfirmed) {
                    const response = await this.$http.post('/technical/save-regular-verbs-db');

                    if (this.checkSuccess(response)) {
                        const result = response.data.data;
                        this.message(result.message, 'success');
                    } else {
                        const errorMessage = response.data.data?.message || 'Ошибка при сохранении в базу';
                        this.message(errorMessage, 'error');
                    }
                }

            } catch (error) {
                console.error('Ошибка при сохранении правильных глаголов в базу:', error);
                this.message('Ошибка при сохранении в базу данных', 'error');
            }
        },
        // SERVER - Сохранить из JSON в базу не правильные глаголы
        async serverSaveIrregularVerbsInDatabase() {
            try {
                // Confirm
                const result = await this.showSaveVerbsConfirmDialog('irregular');

                if (result.isConfirmed) {
                    const response = await this.$http.post('/technical/save-irregular-verbs-db');

                    if (this.checkSuccess(response)) {
                        const result = response.data.data;
                        this.message(result.message, 'success');
                    } else {
                        const errorMessage = response.data.data?.message || 'Ошибка при сохранении в базу';
                        this.message(errorMessage, 'error');
                    }
                }

            } catch (error) {
                console.error('Ошибка при сохранении неправильных глаголов в базу:', error);
                this.message('Ошибка при сохранении в базу данных', 'error');
            }
        },
        // ===== CLIENT METHODS =====
        // Инициализация Confirm
        async showConfirmDialog(title, text) {
            return await this.$swal({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да, заменить',
                cancelButtonText: 'Отмена'
            });
        },
        // Показ confirm диалога для сохранения глаголов
        async showSaveVerbsConfirmDialog(verbType) {
            const today = new Date();
            // Получение текущей даты в формате Y-m-d
            const dateString =  today.getFullYear() + '-' +
                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                String(today.getDate()).padStart(2, '0');
            const verbTypeText = verbType === 'regular' ? 'правильные' : 'неправильные';
            const fileName = `but-${verbType}-verbs-${dateString}.json`;
            const text = `Вы собираетесь заменить в базе данных ${verbTypeText} глаголы из файла \\storage\\app\\json words\\${fileName}. Подтвердите это действие.`;

            return await this.showConfirmDialog(verbTypeText.charAt(0).toUpperCase() + verbTypeText.slice(1) + ' глаголы', text);
        },
        // ===== BUTTON HANDLERS =====
        // Сохранение правильных глаголов в JSON
        async saveRegularVerbsInJSON() {
            await this.serverSaveRegularVerbsInJSON();
        },
        // Сохранение неправильных глаголов в JSON
        async saveIrregularVerbsInJSON() {
            await this.serverSaveIrregularVerbsInJSON();
        },
    }
};
</script>

<style lang="scss" scoped>
.technical-page {
    min-height: 100vh;
    background-color: var(--background);

    .table-wrapper {
        margin-top: 1.5rem;
        box-shadow: none;

        .table {
            box-shadow: none;
            margin: 0;

            th {
                font-weight: 600;
                color: var(--foreground);
                border-bottom: 1px solid var(--border);
                padding: 1rem;
            }

            td {
                padding: 1rem;
                border-bottom: 1px solid var(--border);
                vertical-align: middle;
            }

            tbody tr:hover {
                background-color: var(--accent);
            }
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius);
            transition: all 200ms ease-in-out;

            &:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
        }
    }
}
</style>
