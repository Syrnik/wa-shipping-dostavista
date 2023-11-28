const {VueLoaderPlugin} = require('vue-loader');
// const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

let config = {
    entry: './settings.js',
    output: {
        path: __dirname,
        filename: '../js/settings-legacy.js',
        libraryTarget: 'var',
        library: 'ShippingDostavistaPluginSettings'
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.runtime.esm.js'
        }
    },
    optimization: {
        // minimizer: [
        //     new UglifyJsPlugin({uglifyOptions: {mangle: true, output: {comments: false}, ecma: 5}})
        // ]
        minimizer: [
            new TerserPlugin({terserOptions: {ecma: 5, output: {comments: false}}})
        ]
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                exclude: /node_modules/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader'
                }
            },
            {
                test: /\.styl(us)?$/,
                exclude: /node_modules/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'stylus-loader'
                ]
            },
            {
                test: /\.scss?$/,
                exclude: /node_modules/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.css?$/,
                exclude: /node_modules/,
                use: [
                    'vue-style-loader',
                    'css-loader'
                ]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ]
};

module.exports = config;
