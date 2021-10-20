<template>
    <div>
        <div class="help_search_words" v-html="`${helpDynamic}`"></div>
    </div>
</template>

<script>
    export default {
        props: [
            'helpDynamic',
        ],
        watch: {
            helpDynamic : function (value) {},
        },
        name: "HelpSearchWord",
    }
</script>

<style scoped>
    .help_search_words{
        font-size: 14px;
    }
</style>
