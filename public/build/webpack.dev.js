'use strict'

const merge = require('deep-assign')
const webpack = require('webpack')

const options = require('./options')
const base = require('./webpack.base.js')

const config = merge(base, {
  devServer: {
    historyApiFallback: {
      index: './src/index-dev.html'
    },
    noInfo: true,
    proxy: {
      '/api/**': {
        target: 'https://localhost',
        secure: false
      },
      '/def/**': {
        target: 'https://test.flex.io',
        changeOrigin: true,
        secure: false
      }
    }
  },
  devtool: '#eval-source-map'
})

/* extend module rules */

config.module = (config.module || {})
config.module.rules = (config.module.rules || []).concat([
  // load css files
  {
    test: /\.css$/,
    loader: 'style-loader!css-loader!autoprefixer-loader'
  },
  // load less files
  {
    test: /\.styl$/,
    loader: 'style-loader!css-loader!autoprefixer-loader!stylus-loader'
  }
])


/* load plugins */

// http://vue-loader.vuejs.org/en/workflow/production.html
config.plugins = (config.plugins || []).concat([
  new webpack.ProvidePlugin({
    _: 'lodash',
    Clipboard: 'clipboard'
  })
])

module.exports = config
