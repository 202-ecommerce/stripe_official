/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) Stripe
 * @license   Commercial license
 */

const path = require('path');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');

const minimizers = [];
const plugins = [
  new FixStyleOnlyEntriesPlugin(),
  new MiniCssExtractPlugin({
    filename: '[name].css',
  }),
];

const config = {
  entry: {
    'js/back': './_dev/js/back.js',
    'js/faq': './_dev/js/faq.js',
    'js/payments': './_dev/js/payments.js',
    'js/stripeCard': './_dev/js/stripeCard.js',
    'js/PSTabs': './_dev/js/PSTabs.js',

    'css/checkout': './_dev/scss/checkout.scss',
    'css/admin': './_dev/scss/admin.scss',
  },

  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, './views/'),
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['@babel/preset-env', {
                useBuiltIns: 'usage',
                corejs: 3
              }]],
              plugins: [['@babel/plugin-transform-runtime', {
                proposals: true,
                corejs: 3
              }]],
            },
          },
        ],
      },

      {
        test: /\.(s)?css$/,
        use: [
          {loader: MiniCssExtractPlugin.loader},
          {loader: 'css-loader'},
          {loader: 'postcss-loader'},
          {loader: 'sass-loader'},
        ],
      },

      {
        test: /\.(png|jpe?g|gif)$/i,
        use: [
          {
            loader: 'file-loader',
            options: {
              emitFile: false,
              name: '[path][name].[ext]',
              publicPath: '../..'
            },
          },
        ],
      },

    ],
  },

  externals: {
    $: '$',
    jquery: 'jQuery',
  },

  plugins,

  optimization: {
    minimizer: minimizers,
  },

  resolve: {
    extensions: ['.js', '.scss', '.css'],
    alias: {
      '~': path.resolve(__dirname, './node_modules'),
      'img_dir': path.resolve(__dirname, './views/img'),
    },
  },
};

module.exports = (env, argv) => {
  // Production specific settings
  if (argv.mode === 'production') {
    const terserPlugin = new TerserPlugin({
      cache: true,
      extractComments: /^\**!|@preserve|@license|@cc_on/i, // Remove comments except those containing @preserve|@license|@cc_on
      parallel: true,
      terserOptions: {
        drop_console: true,
      },
    });

    config.optimization.minimizer.push(terserPlugin);
  }

  return config;
};
