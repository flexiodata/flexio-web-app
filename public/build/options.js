'use strict'

const path = require('path')
const version = require('../package.json').version

const banner =
  '/*!\n' +
  ' * Flex.io Web App v' + version + ' (https://github.com/flexiodata/flexio)\n' +
  ' * (c) ' + new Date().getFullYear() + ' Gold Prairie LLC\n' +
  ' */'

module.exports = {
  banner,

  isProduction: process.env.NODE_ENV === 'production',

  paths: {
    root: path.join(__dirname, '..'),

    src: {
      main: path.join(__dirname, '..', 'src')
    },

    output: {
      main: path.join(__dirname, '..', 'dist'),
      signup: path.join(__dirname, '..', 'dist/signup')
    },

    resolve(location) {
      return path.join(__dirname, '..', location)
    }
  }
}
