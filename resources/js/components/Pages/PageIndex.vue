<template>
    <div class="index-page">
        <div class="container-fluid">
            <div class="space-y-6">
                <!-- Header -->
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight">Полезные ресурсы</h1>
                    <p class="text-muted-foreground">
                        Коллекция полезных материалов для изучения английского языка
                    </p>
                </div>

                <!-- Links Grid -->
                <div class="grid gap-4">
                    <a v-for="(link, index) in links"
                       :key="index"
                       :href="link.url"
                       target="_blank"
                       class="group block p-4 rounded-lg border border-border bg-card hover:bg-accent transition-all duration-200 hover:shadow-md">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-primary-foreground text-sm font-medium group-hover:scale-110 transition-transform duration-200">
                                {{ index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-card-foreground group-hover:text-primary transition-colors duration-200">
                                    {{ link.title }}
                                </p>
                                <p class="text-sm text-muted-foreground mt-1 leading-relaxed">
                                    {{ link.description }}
                                </p>
                            </div>
                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            links: [
                {
                    title: "YouTube БЕСПЛАТНЫЙ РЕПЕТИТОР - АНГЛИЙСКИЙ ЯЗЫК. УРОКИ 2",
                    description: "Бесплатные видеоуроки английского языка от профессионального репетитора",
                    url: "https://www.youtube.com/channel/UCcnjJu-ejZlLaz-OwpBd7dQ/featured"
                },
                {
                    title: "YouTube АНГЛИЙСКИЙ ЯЗЫК ДО ПОЛНОГО АВТОМАТИЗМА С САМОГО НУЛЯ",
                    description: "Систематический курс английского языка от нуля до автоматизма",
                    url: "https://www.youtube.com/watch?v=vV0-52Z7AOE&list=PLcmoTwMF9KeaGJEsIA3fA5ffFTYbLyUS-&index=1"
                },
                {
                    title: "LanGeek - Иллюстрированный словарь",
                    description: "Изучение языков с помощью иллюстрированного словаря. Изображения помогают ассоциировать слова с визуальными образами",
                    url: "https://dictionary.langeek.co/en-RU"
                },
                {
                    title: "YouTube Короткие предложения и частые слова",
                    description: "Коллекция коротких предложений и часто используемых слов для практики",
                    url: "https://www.youtube.com/@play-en/playlists"
                },
                {
                    title: "Таблица неправильных глаголов",
                    description: "Полная таблица неправильных глаголов английского языка во всех временах",
                    url: "/table-of-irregular-verbs.pdf"
                },
                {
                    title: "Top 3000 English words",
                    description: "Список 3000 самых часто используемых английских слов с переводом",
                    url: "/Top%203000%20English%20words.pdf"
                }
            ]
        };
    },
    methods: {},
    mounted() {
    },
}
</script>

<style lang="scss" scoped>
.index-page {
    padding: var(--spacing-6) 0;

    .container {
        padding: 0 var(--spacing-4);

        @media (min-width: 640px) {
            padding: 0 var(--spacing-6);
        }
    }

    .space-y-6 > * + * {
        margin-top: var(--spacing-6);
    }

    .space-y-2 > * + * {
        margin-top: var(--spacing-2);
    }

    .space-y-4 > * + * {
        margin-top: var(--spacing-4);
    }

    .space-y-3 > * + * {
        margin-top: var(--spacing-3);
    }

    .space-x-3 > * + * {
        margin-left: var(--spacing-3);
    }

    .space-x-4 > * + * {
        margin-left: var(--spacing-4);
    }

    .grid {
        display: grid;
        gap: var(--spacing-4);
    }

    .max-w-4xl {
        max-width: 56rem;
    }

    .mx-auto {
        margin-left: auto;
        margin-right: auto;
    }

    .text-3xl {
        font-size: 1.875rem;
        line-height: 2.25rem;
    }

    .text-sm {
        font-size: 0.875rem;
        line-height: 1.25rem;
    }

    .font-bold {
        font-weight: 700;
    }

    .font-medium {
        font-weight: 500;
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .leading-relaxed {
        line-height: 1.625;
    }

    .p-4 {
        padding: var(--spacing-4);
    }

    .p-6 {
        padding: var(--spacing-6);
    }

    .w-8 {
        width: 2rem;
    }

    .h-8 {
        height: 2rem;
    }

    .w-6 {
        width: 1.5rem;
    }

    .h-6 {
        height: 1.5rem;
    }

    .w-4 {
        width: 1rem;
    }

    .h-4 {
        height: 1rem;
    }

    .w-3 {
        width: 0.75rem;
    }

    .h-3 {
        height: 0.75rem;
    }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    .border {
        border: 1px solid var(--border);
    }

    .border-border {
        border-color: var(--border);
    }

    .bg-card {
        background-color: var(--card);
    }

    .bg-primary {
        background-color: var(--primary);
    }

    .bg-muted\/50 {
        background-color: rgb(var(--muted) / 0.5);
    }

    .bg-info {
        background-color: var(--info);
    }

    .text-card-foreground {
        color: var(--card-foreground);
    }

    .text-primary-foreground {
        color: var(--primary-foreground);
    }

    .text-muted-foreground {
        color: var(--muted-foreground);
    }

    .text-foreground {
        color: var(--foreground);
    }

    .text-info-foreground {
        color: var(--info-foreground);
    }

    .hover\:bg-accent:hover {
        background-color: var(--accent);
    }

    .hover\:shadow-md:hover {
        box-shadow: var(--shadow-md);
    }

    .hover\:text-primary:hover {
        color: var(--primary);
    }

    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }

    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }

    .transition-all {
        transition: all 200ms ease-in-out;
    }

    .transition-colors {
        transition: color 200ms ease-in-out, background-color 200ms ease-in-out, border-color 200ms ease-in-out;
    }

    .transition-transform {
        transition: transform 200ms ease-in-out;
    }

    .transition-opacity {
        transition: opacity 200ms ease-in-out;
    }

    .duration-200 {
        transition-duration: 200ms;
    }

    .flex {
        display: flex;
    }

    .items-start {
        align-items: flex-start;
    }

    .items-center {
        align-items: center;
    }

    .justify-center {
        justify-content: center;
    }

    .flex-1 {
        flex: 1 1 0%;
    }

    .flex-shrink-0 {
        flex-shrink: 0;
    }

    .min-w-0 {
        min-width: 0;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }

    .mt-8 {
        margin-top: 2rem;
    }

    .opacity-0 {
        opacity: 0;
    }

    .block {
        display: block;
    }

    .text-decoration-none {
        text-decoration: none;
    }

    svg {
        display: block;
    }
}
</style>
