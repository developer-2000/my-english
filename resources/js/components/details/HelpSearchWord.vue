<template>
    <div>
        <div class="help_search_words" v-html="`${helpDynamic}`"></div>
    </div>
</template>

<script>
    export default {
        name: 'HelpSearchWord',
        props: ['helpDynamic'],
        watch: {
            helpDynamic: function () {},
        },
    };
</script>

<style scoped>
    .help_search_words {
        font-size: 14px;
    }
</style>
