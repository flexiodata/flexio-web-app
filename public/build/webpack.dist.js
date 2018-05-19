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
      raw: false,
      entryOnly: true,
      test: /app\.js$/
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
  // extract stylus files
  {
    test: /\.styl$/,
    loader: ExtractTextPlugin.extract({
      fallback: 'style-loader',
      use: 'css-loader!autoprefixer-loader!stylus-loader'
    })
  }
])

/* load plugins */

// https://vuejs.org/v2/guide/deployment.html
config.plugins = (config.plugins || []).concat([
  new webpack.DefinePlugin({
    'process.env': {
      NODE_ENV: JSON.stringify('production')
    }
  }),

  // Provide lodash and Clipboard.js to every file without needing to do an import
  new webpack.ProvidePlugin({
    _: 'lodash',
    Clipboard: 'clipboard'
  }),

  new BundleAnalyzerPlugin({
    analyzerMode: 'static',
    reportFilename: '../src/build/report.html'
    //statsFilename: '../src/build/stats.json',
    //generateStatsFile: true,
  }),

  // Extract all 3rd party modules into a separate 'vendor' chunk
  new webpack.optimize.CommonsChunkPlugin({
    name: 'vendor',
    filename: 'js/vendor.js',
    minChunks(module, count) {
      var context = module.context
      return context && context.indexOf('node_modules') >= 0
    }
  }),

  /*
  // Generate a 'manifest' chunk to be inlined in the HTML template
  new webpack.optimize.CommonsChunkPlugin('manifest'),
  */

  // IgnorePlugin doesn't load 'zn-CN' local (which is right) but in runtime, when page is being loaded,
  // the code which requires 'zh-CN' locale is executed. Therefore an error is thrown to browser.
  // Check out https://github.com/ElemeFE/element/issues/1315 for more info.
  new webpack.NormalModuleReplacementPlugin(/element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/, 'element-ui/lib/locale/lang/en'),

  // Reduce build size by not including a ton of moment.js locale files
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

  /*
  new AssetsPlugin({
    filename: options.paths.resolve('src/build/assets.json'),
    prettyPrint: true
  }),
  */

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

module.exports = config
