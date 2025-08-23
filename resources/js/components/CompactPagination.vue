<template>
    <div class="compact-pagination">
        <div class="pagination-info">
            <span>Showing {{ startIndex }} - {{ endIndex }} of {{ total }} results</span>
            <span class="separator">|</span>
            <span>Pages: {{ totalPages }}</span>
        </div>

        <div class="pagination-controls">
            <!-- Кнопка в начало (двойная стрелка влево) -->
            <button v-if="currentPage > 3" class="nav-btn" title="В начало" @click="goToPage(1)">
                <svg
                    stroke="currentColor"
                    fill="currentColor"
                    stroke-width="0"
                    viewBox="0 0 24 24"
                    class="w-4 h-4"
                >
                    <path d="M18.41 16.59L13.82 12l4.59-4.59L17 6l-6 6 6 6zM6 6h2v12H6z"></path>
                </svg>
            </button>

            <!-- Предыдущая страница -->
            <button
                class="nav-btn"
                :disabled="currentPage === 1"
                title="Предыдущая страница"
                @click="goToPage(currentPage - 1)"
            >
                <svg
                    stroke="currentColor"
                    fill="currentColor"
                    stroke-width="0"
                    viewBox="0 0 256 512"
                    class="w-4 h-4"
                >
                    <path
                        d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"
                    ></path>
                </svg>
            </button>

            <!-- Номера страниц -->
            <div v-for="page in visiblePages" :key="page">
                <button
                    class="page-btn"
                    :class="{ active: page === currentPage }"
                    @click="goToPage(page)"
                >
                    {{ page }}
                </button>
            </div>

            <!-- Следующая страница -->
            <button
                class="nav-btn"
                :disabled="currentPage === totalPages"
                title="Следующая страница"
                @click="goToPage(currentPage + 1)"
            >
                <svg
                    stroke="currentColor"
                    fill="currentColor"
                    stroke-width="0"
                    viewBox="0 0 256 512"
                    class="w-4 h-4"
                >
                    <path
                        d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4 9.4-9.4 24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"
                    ></path>
                </svg>
            </button>

            <!-- Кнопка в конец (двойная стрелка вправо) -->
            <button
                v-if="currentPage < totalPages - 2"
                class="nav-btn"
                title="В конец"
                @click="goToPage(totalPages)"
            >
                <svg
                    stroke="currentColor"
                    fill="currentColor"
                    stroke-width="0"
                    viewBox="0 0 24 24"
                    class="w-4 h-4"
                >
                    <path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z"></path>
                </svg>
            </button>
        </div>

        <div class="per-page-control">
            <span class="label">Show:</span>
            <select v-model="globalPerPage" @change="onPerPageChange">
                <option v-for="option in perPageOptions" :key="option" :value="option">
                    {{ option }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        name: 'CompactPagination',
        props: {
            currentPage: {
                type: Number,
                required: true,
            },
            total: {
                type: Number,
                required: true,
            },
            perPage: {
                type: Number,
                required: true,
            },
            perPageOptions: {
                type: Array,
                default: () => [10, 25, 50, 100],
            },
        },
        computed: {
            ...mapGetters(['getPerPage']),
            globalPerPage: {
                get() {
                    return this.getPerPage;
                },
                set(value) {
                    this.$store.commit('SET_PER_PAGE', parseInt(value));
                },
            },
            totalPages() {
                return Math.ceil(this.total / this.globalPerPage);
            },
            startIndex() {
                return (this.currentPage - 1) * this.globalPerPage + 1;
            },
            endIndex() {
                const end = this.currentPage * this.globalPerPage;
                return end > this.total ? this.total : end;
            },
            visiblePages() {
                const pages = [];
                const maxVisible = 5;

                if (this.totalPages <= maxVisible) {
                    // Показываем все страницы
                    for (let i = 1; i <= this.totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    // Показываем максимум 5 страниц с логикой
                    let start = Math.max(1, this.currentPage - 2);
                    const end = Math.min(this.totalPages, start + maxVisible - 1);

                    // Корректируем если достигли конца
                    if (end === this.totalPages) {
                        start = Math.max(1, end - maxVisible + 1);
                    }

                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }
                }

                return pages;
            },
        },
        methods: {
            goToPage(page) {
                if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
                    // Скролл наверх страницы
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth',
                    });
                    this.$emit('page-changed', page);
                }
            },
            onPerPageChange() {
                // Скролл наверх страницы при изменении количества записей
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth',
                });
                this.$emit('per-page-changed', this.globalPerPage);
            },
        },
    };
</script>
