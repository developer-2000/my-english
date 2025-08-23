module.exports = {
    env: {
        browser: true,
        es2021: true,
        node: true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/recommended',
        'prettier',
    ],
    parserOptions: {
        ecmaVersion: 12,
        sourceType: 'module',
    },
    plugins: ['vue'],
    rules: {
        'vue/multi-word-component-names': 'off',
        'vue/no-v-html': 'off',
        'vue/require-default-prop': 'off',
        'vue/require-prop-types': 'off',
        'vue/valid-v-slot': 'off',
        'vue/attribute-hyphenation': 'off',
        'vue/order-in-components': 'off',
        'no-console': 'off',
        'no-debugger': 'error',
        'no-unused-vars': 'error',
        'prefer-const': 'error',
        'no-var': 'error',
    },
    globals: {
        // Vue глобальные переменные
        Vue: 'readonly',
        // Laravel глобальные переменные
        route: 'readonly',
        // Bootstrap глобальные переменные
        bootstrap: 'readonly',
        // Другие глобальные переменные
        $: 'readonly',
        jQuery: 'readonly',
    },
};
