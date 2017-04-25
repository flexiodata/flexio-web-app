'use strict'

const path = require('path')
const webpack = require('webpack')
const options = require('./options')

// webpack plugins
const WebpackMd5Hash = require('webpack-md5-hash')
const AssetsPlugin = require('assets-webpack-plugin')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin

// constants
const OUTPUT_FILENAME = options.isProduction ? '[name]-[chunkhash].js' : '[name].js'
const EXTRACT_TEXT_FILENAME = options.isProduction ? 'css/style-[contenthash].css' : 'css/style.css'
const SHOW_BUNDLE_ANALYZER = false

module.exports = {
  entry: {
    vendor: [
      'lodash',
      'vue',
      'vuex',
      'vue-resource',
      'vue-router',
      'vue2-grid',
      'vee-validate',
      'keen-ui',
      'filesize',
      'clipboard',
      'moment',
      'jquery',
      'tinycolor2',
      'codemirror'
    ],
    app: options.paths.resolve('src/main.js')
  },
  output: {
    path: options.paths.output.main,
    publicPath: '/dist/',
    filename: OUTPUT_FILENAME
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
      // extract css files
      {
        test: /\.css$/,
        loader: ExtractTextPlugin.extract({
          fallbackLoader: 'style-loader',
          loader: 'css-loader!autoprefixer-loader'
        })
      },
      // extract less files
      {
        test: /\.less$/,
        loader: ExtractTextPlugin.extract({
          fallbackLoader: 'style-loader',
          loader: 'css-loader!autoprefixer-loader!less-loader'
        })
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
  },
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue',
      'jquery': 'jquery/src/jquery'
    }
  },
  devtool: '#source-map'
}

/* debug and production plugins */

// http://vue-loader.vuejs.org/en/workflow/production.html
module.exports.plugins = (module.exports.plugins || []).concat([
  new webpack.DefinePlugin({
    'process.env': {
      NODE_ENV: '"production"'
    }
  }),
  new webpack.ProvidePlugin({
    _: 'lodash',
    $: 'jquery',
    jQuery: 'jquery',
    Clipboard: 'clipboard',
    tinycolor: 'tinycolor2'
  }),
  new webpack.optimize.CommonsChunkPlugin({
    name: 'vendor',
    minChunks: Infinity
  }),
  new WebpackMd5Hash(), // use standard md5 hash when using [chunkfile]
  new webpack.optimize.UglifyJsPlugin({
    //sourceMap: true,
    compress: {
      warnings: false
    }
  }),
  new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/), // TODO: find out if there's a no-locale moment.js NPM repo
  new webpack.LoaderOptionsPlugin({
    minimize: true
  }),
  new AssetsPlugin({
    filename: options.paths.resolve('src/build/assets.json'),
    prettyPrint: true
  }),
  new ExtractTextPlugin({
    filename: EXTRACT_TEXT_FILENAME
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

if (!options.isProduction)
{
  /* debug-only plugins */
  if (SHOW_BUNDLE_ANALYZER)
  {
    module.exports.plugins = module.exports.plugins.concat([
      new BundleAnalyzerPlugin()
    ])
  }
}
 else
{
  /* production-only plugins */
}
