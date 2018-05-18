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
      'vue-grid2',
      'vue-scrollto',
      'chart.js',
      'vue-chartjs',
      'axios',
      'filesize',
      'clipboard',
      'stickybits',
      'marked',
      'moment',
      'flexio-sdk-js',
      'vue-codemirror',
      'codemirror/mode/javascript/javascript',
      'codemirror/mode/python/python'
      /*
      'codemirror/mode/css/css',
      'codemirror/mode/xml/xml',
      'codemirror/mode/htmlmixed/htmlmixed',
      */
    ],
    app: options.paths.resolve('src/main.js')
  },

  output: {
    path: options.paths.output.main,
    publicPath: '/dist/',
    filename: options.isProduction ? 'js/[name]-[chunkhash].js' : 'js/[name].js'
  },

  resolve: {
    alias: {
      'vue$': options.isProduction ? 'vue/dist/vue.min' : 'vue/dist/vue'
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
      {
        test: /\.styl$/,
        loader: 'style-loader!css-loader!stylus-loader'
      },
      {
        test: /\.yml$/,
        loader: 'yaml-loader',
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
              mozjpeg: {
                progressive: true,
              },
              gifsicle: {
                interlaced: false,
              },
              optipng: {
                optimizationLevel: 7,
              },
              pngquant: {
                quality: '65-90',
                speed: 4
              }
            }
          }
        ]
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        options: {
          limit: 10000,
          name: 'fonts/[name].[hash:7].[ext]'
        }
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
