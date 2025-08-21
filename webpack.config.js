const path = require('path');

module.exports = {
    watchOptions: {
        ignored: /node_modules/,
        poll: 1000,
        aggregateTimeout: 300
    },
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '~': path.resolve('node_modules')
        }
    },
    stats: {
        children: false,
        modules: false
    }
};
