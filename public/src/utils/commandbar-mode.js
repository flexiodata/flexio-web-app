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
    //var operator_regexp = /[+\-*&%=<>!?|~^]/;

    var external = {
      startState: function(basecolumn) {
        return {
          tokenize: null
        }
      },

      token: function(stream, state) {

        function tokenString(quote) {
          return function(stream, state) {
            var escaped = false, next;
            while ((next = stream.next()) != null) {
              
              if (next == quote)
              {
                if (stream.peek() == quote)
                {
                  // escaped quote -- consume it and move on
                  stream.next();
                }
                 else
                {
                  break;
                }
              }

              //if (next == quote && !escaped) break;
              //escaped = !escaped && next == "\\";
            }
            if (!escaped) {
              state.tokenize = null;
            }
            return "string"
          };
        }


        if (state.tokenize)
          return state.tokenize(stream.state);



        var peek_ch = stream.peek()

        if (peek_ch == '"' || peek_ch == "'") {
          peek_ch = stream.next()
          state.tokenize = tokenString(peek_ch);
          return state.tokenize(stream, state);
        }

        //if (undefined === stream.eat(/[0-9]+/))
        //  return 'number'
        if (stream.match(cmds_regexp) !== null)
          return 'variable-2'

        if (stream.eat(/[0-9]/) !== undefined)
          return 'number'

        //if (stream.eat(operator_regexp) !== undefined)
        //  return 'operator'
        
        if (stream.match(/[a-z]+[:]/) !== null)
          return 'variable-2'

        var ch = stream.next()

        return null;
      }

    };
    return external;
  });

  CodeMirror.defineMIME('text/x-flexio-commandbar', 'flexio-commandbar');
});
