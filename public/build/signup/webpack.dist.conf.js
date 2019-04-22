'use strict'

const version = require('../../package.json').version
const merge = require('deep-assign')
const webpack = require('webpack')
const options = require('../options')
const banner =
  'Flex.io Web Sign In v' + version + ' (https://github.com/flexiodata/flexio)\n' +
  '(c) ' + new Date().getFullYear() + ' Gold Prairie LLC\n' +

const base = {
  entry: options.paths.resolve('src/signup.js'),
  // without this, webpack throws in a polyfill for node.js's Buffer class
  node: {
    Buffer: false,
    process: false
  },
  output: {
    filename: options.isProduction ? 'flexio-signup.min.js' : 'flexio-signup.js',
    path: options.paths.output.signup,
    library: 'FlexioSignUpModal',
    libraryExport: 'default',
    libraryTarget: 'umd'
  },
  resolve: {
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
      }
    ]
  }
}

const config = merge(base, {
  devtool: '#source-map',
  plugins: [
    new webpack.BannerPlugin({
      banner,
      raw: false,
      entryOnly: true
    })
  ]
})

/* extend module rules */

config.module = (config.module || {})
config.module.rules = (config.module.rules || [])

/* load plugins */

// https://vuejs.org/v2/guide/deployment.html
config.plugins = (config.plugins || []).concat([
  // Set the production environment
  new webpack.DefinePlugin({
    'process.env': {
      NODE_ENV: JSON.stringify('production')
    }
  }),

  new webpack.ProvidePlugin({
    _: 'lodash'
  }),

  new webpack.optimize.UglifyJsPlugin({
    //sourceMap: true,
    compress: {
      warnings: false
    }
  })
])

module.exports = config
