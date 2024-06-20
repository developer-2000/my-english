<template>
    <div class="index-page">
        <a href="https://www.youtube.com/channel/UCcnjJu-ejZlLaz-OwpBd7dQ/featured" target="_blank">
            YouTube БЕСПЛАТНЫЙ РЕПЕТИТОР - АНГЛИЙСКИЙ ЯЗЫК. УРОКИ 2
        </a>
        <a href="https://www.youtube.com/watch?v=vV0-52Z7AOE&list=PLcmoTwMF9KeaGJEsIA3fA5ffFTYbLyUS-&index=1" target="_blank">
            YouTube АНГЛИЙСКИЙ ЯЗЫК ДО ПОЛНОГО АВТОМАТИЗМА С САМОГО НУЛЯ
        </a>
        <a href="https://kids-books.com.ua/nepravilnye-glagoly-angliyskogo-yazyka" target="_blank">
            Не правильные глаголы во временах
        </a>
        <a href="https://dictionary.langeek.co/en-RU" target="_blank">
            LanGeek - изучение языков, иллюстрированный словарь изображений. Изображения помогают учащимся ассоциировать слова с визуальными образами.
        </a>
        <a href="https://www.youtube.com/@play-en/playlists" target="_blank">
            YouTube Короткие предложения и частые слова
        </a>
    </div>
</template>

<script>
    export default {
        data() {
            return {
            };
        },
        methods: {
        },
        mounted() {
        },
    }
</script>

<style lang="scss" scoped>

.index-page {
    counter-reset: link-counter;
    list-style-type: none;
    padding: 15px 0 0 30px;
    a {
        display: block;
        position: relative;
        padding-left: 25px;
        margin-bottom: 10px;
        text-decoration: none;
        &::before {
            counter-increment: link-counter;
            content: counter(link-counter) ". ";
            position: absolute;
            left: 0;
            top: 0;
            font-weight: bold;
        }
    }
}

</style>
