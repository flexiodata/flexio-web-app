'use strict'

const path = require('path')
const options = require('./options')

module.exports = {
  entry: {
    app: options.paths.resolve('src/main.js')
  },
  output: {
    path: options.paths.output.main,
    publicPath: '/dist/',
    filename: options.isProduction ? 'js/[name]-[chunkhash].js' : 'js/[name].js'
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      'vue$': options.isProduction ? 'vue/dist/vue.min' : 'vue/dist/vue',
      '@': options.paths.resolve('src'),
      '@comp': options.paths.resolve('src/components')
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
        loader: 'json-loader!yaml-loader',
        exclude: /node_modules/
      },
      // compress images before outputting them
      {
        test: /\.(jpe?g|png|gif|svg)$/,
        loaders: [
          {
            loader: 'file-loader',
            options: {
              name: 'assets/[name]-[hash:7].[ext]'
            }
          },
          {
            loader: 'image-webpack-loader',
            options: {
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
  }
}