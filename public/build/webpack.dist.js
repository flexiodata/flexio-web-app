'use strict'

const merge = require('deep-assign')
const webpack = require('webpack')

const options = require('./options')
const base = require('./webpack.base.js')

// webpack plugins
const WebpackMd5Hash = require('webpack-md5-hash')
const AssetsPlugin = require('assets-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin

const config = merge(base, {
  // without this, webpack throws in a polyfill for node.js's Buffer class
  node: {
    Buffer: false,
    process: false
  },

  devtool: '#source-map',

  plugins: [
    new webpack.BannerPlugin({
      banner: options.banner,
      raw: true,
      entryOnly: true
    })
  ]
})

/* extend module rules */

config.module = (config.module || {})
config.module.rules = (config.module.rules || []).concat([
  // extract css files
  {
    test: /\.css$/,
    loader: ExtractTextPlugin.extract({
      fallback: 'style-loader',
      use: 'css-loader!autoprefixer-loader'
    })
  },
  // extract less files
  {
    test: /\.less$/,
    loader: ExtractTextPlugin.extract({
      fallback: 'style-loader',
      use: 'css-loader!autoprefixer-loader!less-loader'
    })
  }
])

/* load plugins */

// http://vue-loader.vuejs.org/en/workflow/production.html
config.plugins = (config.plugins || []).concat([
  new webpack.DefinePlugin({
    'process.env': {
      NODE_ENV: '"production"'
    }
  }),
  new webpack.ProvidePlugin({
    _: 'lodash',
    Clipboard: 'clipboard'
  }),
  new webpack.optimize.CommonsChunkPlugin({
    name: 'vendor',
    minChunks: Infinity
  }),
  new webpack.NormalModuleReplacementPlugin(/element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/, 'element-ui/lib/locale/lang/en'),
  new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/), // TODO: find out if there's a no-locale moment.js NPM repo
  new WebpackMd5Hash(), // use standard md5 hash when using [chunkfile]
  new webpack.optimize.UglifyJsPlugin({
    //sourceMap: true,
    compress: {
      warnings: false
    }
  }),
  new webpack.LoaderOptionsPlugin({
    minimize: true
  }),
  new AssetsPlugin({
    filename: options.paths.resolve('src/build/assets.json'),
    prettyPrint: true
  }),
  new ExtractTextPlugin({
    filename: options.isProduction ? 'css/style-[contenthash].css' : 'css/style.css'
  }),
  new HtmlWebpackPlugin({
    template: options.paths.resolve('src/index-template.ejs'), // load a custom template (ejs by default see the FAQ for details)
    filename: options.paths.resolve('src/build/index-template.html')
  }),
  new HtmlWebpackPlugin({
    template: options.paths.resolve('src/index-template.ejs'), // load a custom template (ejs by default see the FAQ for details)
    filename: options.paths.resolve('../application/views/layout.phtml')
  })
])

if (!options.isProduction && options.showBundleAnalyzer)
{
  config.plugins = config.plugins.concat([
    new BundleAnalyzerPlugin()
  ])
}

module.exports = config
