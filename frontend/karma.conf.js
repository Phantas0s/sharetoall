'use strict';

const path = require('path');

// See https://github.com/webpack/loader-utils/issues/56
process.noDeprecation = true;

module.exports = (config) => {
    const tests = 'tests/*/*.test.js';

    config.set({
        frameworks: ['mocha'],

        phantomjsLauncher: {
            // Have phantomjs exit if a ResourceError is encountered (useful if karma exits without killing phantom)
            exitOnResourceError: true,
        },

        browsers: ['PhantomJS'],

        /*        customLaunchers: {
            'chrome-webdriver': {
                base: 'WebDriver',
                config: {
                    hostname: 'chrome',
                    port: 4444
                },
                browserName: 'chrome',
                pseudoActivityInterval: 30000
            }
        }, */

        files: [
            {pattern: 'tests/**/*_test.js', watched: false},
        ],

        // Preprocess through webpack
        preprocessors: {
            'tests/**/*_test.js': ['webpack'],
        },

        reporters: ['progress', 'html'],

        htmlReporter: {
            outputFile: 'tests/result.html',
        },

        webpack: {
            resolve: {
                modules: [
                    path.join(__dirname, 'src'),
                    path.join(__dirname, 'node_modules'),
                    path.join(__dirname, 'tests'),
                ],
                alias: {
                    vue: 'vue/dist/vue.js',
                },
            },
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        loader: 'babel-loader',
                        query: {
                            presets: ['es2015'],
                        },
                    },
                ],
            },
        },

        singleRun: true,
    });
};
