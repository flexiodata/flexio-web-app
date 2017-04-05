// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: http://codemirror.net/LICENSE

(function(mod) {
  if (typeof exports == 'object' && typeof module == 'object') // CommonJS
    mod(require('../../node_modules/codemirror/lib/codemirror'), require('../utils/parser'));
  else if (typeof define == 'function' && define.amd) // AMD
    define(['../../node_modules/codemirror/lib/codemirror', '../utils/parser'], mod);
  else // Plain browser env
    mod(CodeMirror, CommandBarParser);
})(function(CodeMirror, parser) {
  'use strict';



  function wordRegexp(words) {
    return new RegExp("^((" + words.join(")|(") + "))\\b");
  }

  CodeMirror.defineMode('flexio-commandbar', function(conf, parserConf) {

    var cmds = parser.getHintableCommands();
    var cmds_regexp = wordRegexp(cmds);

    var external = {
      startState: function(basecolumn) {
        return {

        }
      },

      token: function(stream, state) {

        //if (undefined === stream.eat(/[0-9]+/))
        //  return 'number'
        if (stream.match(cmds_regexp) !== null)
          return 'keyword'

        if (stream.eat(/[0-9]/) !== undefined)
          return 'number'

        if (stream.match(/[a-z]+[:]/) !== null)
          return 'keyword'

        var ch = stream.next()

        return null;
      }

    };
    return external;
  });

  CodeMirror.defineMIME('text/x-flexio-commandbar', 'flexio-commandbar');
});
