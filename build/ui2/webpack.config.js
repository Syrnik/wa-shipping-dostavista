const {VueLoaderPlugin} = require('vue-loader');
const TerserPlugin = require('terser-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const webpack = require('webpack');

const config = {
    entry: "./settings/index.js",
    output: {
        path: __dirname,
        filename: '../../js/settings.js',
        libraryTarget: "umd",
        library: "ShippingDostavistaPluginSettings",
        libraryExport: "default"
    },
    resolve: {
        extensions: ["", ".js", ".vue"],
        alias: {
            'vue$': 'vue/dist/vue.runtime.esm-bundler.js'
        }
    },
    optimization: {
        minimizer: [
            new TerserPlugin({
                terserOptions: {ecma: 2021, mangle: true, output: {comments: false}},
                extractComments: false
            })
        ]
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                //exclude: /node_modules/,
                loader: 'vue-loader',
            },
            {
                test: /\.styl(us)?$/,
                exclude: /node_modules/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'stylus-loader']
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                options: {
                    presets: [
                        ['@babel/preset-env', {
                            useBuiltIns: 'usage',
                            corejs: '3.0.0',
                            debug: true,
                            targets: [
                                'last 5 Chrome versions',
                                'last 5 Firefox versions',
                                'last 5 Edge versions',
                                'last 5 Opera versions',
                                'last 5 Safari versions',
                                'last 5 Safari versions',
                                'last 5 iOS versions'
                            ]
                        }]
                    ],
                    plugins: ['@babel/plugin-transform-runtime']
                }
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({filename: '../../css/settings.css'}),
        new VueLoaderPlugin(),
        new WebpackNotifierPlugin({alwaysNotify: true}),
        new webpack.DefinePlugin({
            __VUE_OPTIONS_API__: JSON.stringify(true),
            __VUE_PROD_DEVTOOLS__: JSON.stringify(false)
        })
    ]
};

module.exports = config;
