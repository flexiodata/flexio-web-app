'use strict'

const options = require('./options')

module.exports = {
  entry: {
    vendor: [
      'lodash',
      'vue',
      'vuex',
      'vue-resource',
      'vue-router',
      'vue-simple-spinner',
      'vue2-grid',
      'vee-validate',
      'vue-scrollto',
      'keen-ui',
      'filesize',
      'clipboard',
      'marked',
      'moment',
      'codemirror',
      'axios',
      'autosize'
    ],
    app: options.paths.resolve('src/main.js')
  },

  output: {
    path: options.paths.output.main,
    publicPath: '/dist/',
    filename: options.isProduction ? '[name]-[chunkhash].js' : '[name].js'
  },

  resolve: {
    alias: {
      'vue$': 'vue/dist/vue'
    }
  },

  module: {
    rules: [
      // allow support for .vue file syntax:
      // ref. https://vue-loader.vuejs.org/en/start/spec.html
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          // vue-loader options go here
        }
      },
      // allow support for ECMAScript 6
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/
      },
      // compress images before outputting them
      {
        test: /\.(jpe?g|png|gif|svg)$/,
        loaders: [
          {
            loader: 'file-loader',
            options: {
              name: 'assets/[name]-[hash].[ext]'
            }
          },
          {
            loader: 'image-webpack-loader',
            query: {
              progressive: true,
              optimizationLevel: 7,
              interlaced: false,
              pngquant: {
                quality: '65-90',
                speed: 4
              }
            }
          }
        ]
      }
    ]
  }/*,

  // Stats is used to customize Webpack's console output
  // https://webpack.js.org/configuration/stats/
  stats: {
    hash: false,
    colors: true,
    chunks: false,
    version: false,
    children: false,
    timings: true
  }*/
}
