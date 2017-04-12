// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: http://codemirror.net/LICENSE

// Depends on csslint.js from https://github.com/stubbornella/csslint

// declare global: CSSLint

(function(mod) {
  if (typeof exports == 'object' && typeof module == 'object') // CommonJS
    mod(require('../../node_modules/codemirror/lib/codemirror'), require('../utils/parser'))
  else if (typeof define == 'function' && define.amd) // AMD
    define(['../../node_modules/codemirror/lib/codemirror', '../utils/parser'], mod)
  else // Plain browser env
    mod(CodeMirror, CommandBarParser)
})(function(CodeMirror, parser) {
  'use strict'

  CodeMirror.registerHelper('lint', 'flexio-commandbar', function(text) {
    var found = []

    if (!parser)
      return found

    var errors = parser.validate(text)

    // no errors; we're done
    if (errors === true)
      return found

    for (var i = 0; i < errors.length; i++) {
      var error = errors[i]
      var startLine = 0
      var endLine = 0
      var startCol = error.offset
      var endCol = error.offset+error.length

      found.push({
        from: CodeMirror.Pos(startLine, startCol),
        to: CodeMirror.Pos(endLine, endCol),
        message: error.message
      })
    }

    return found
  })

})
