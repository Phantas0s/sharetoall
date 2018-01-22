'use strict';

const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const PATHS = {
    web: path.join(__dirname, 'src/web.js'),
    auth: path.join(__dirname, 'src/auth.js'),
    sharetoall: path.join(__dirname, 'src/sharetoall.js'),
    confirm: path.join(__dirname, 'src/confirm.js'),
    build: path.join(__dirname, '../web/assets/build'),
};

const sassPlugin = new ExtractTextPlugin({
    filename: '[name].css'
});

// See https://github.com/webpack/loader-utils/issues/56
process.noDeprecation = true;

module.exports = {
    entry: {
        web: PATHS.web,
        auth: PATHS.auth,
        confirm: PATHS.confirm,
        sharetoall: PATHS.sharetoall,
    },
    output: {
        path: PATHS.build,
        filename: '[name].js',
    },
    resolve: {
        modules: [
            path.join(__dirname, 'src'),
            path.join(__dirname, 'node_modules'),
        ],
        alias: {
            vue: 'vue/dist/vue.js',
        },
    },
    plugins: [
        sassPlugin
    ],
    node: {
        fs: 'empty',
    },
    module: {
        rules: [
            {
                test: /\.(js)$/,
                include: PATHS.app,
                enforce: 'pre',
                loader: 'eslint-loader',
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015'],
                },
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        js: 'babel-loader?presets[]=es2015',
                    },
                },
            },
            {
                test: /\.scss$/,
                loader: sassPlugin.extract({
                    use: [
                        'css-loader',
                        'sass-loader',
                        'postcss-loader'
                    ],
                    fallback: 'style-loader'
                })
            },
            {
                test: /\.(png|jpg|jpeg|gif|svg|woff|woff2)$/,
                loader: 'url-loader',
            },
            {
                test: /\.(wav|mp3|eot|ttf)$/,
                loader: 'file-loader',
            },
            {
                test: /\.svg/,
                use: {
                    loader: 'svg-url-loader',
                    options: {},
                },
            },
        ],
    }
};
