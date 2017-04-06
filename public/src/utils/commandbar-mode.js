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
          verb: null,         // this is a string containing the main command bar verb, like "input" or "convert"
          args_regexp: null,  // contains a list, based off of the verb, for which arguments should be highlighted
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
          state.tokenize = tokenString(peek_ch)
          return state.tokenize(stream, state)
        }

        //if (undefined === stream.eat(/[0-9]+/))
        //  return 'number'
        var res = stream.match(cmds_regexp)
        if (res !== null)
        {
          // save the cmdbar verb for later highlighting decisions;
          // for instance, the args are filtered based on which verb
          // is used. e.g. "convert" 
          state.verb = res[0]
          var args = parser.getVerbArguments(state.verb)
          state.args_regexp = (_.isArray(args) && args.length > 0) ? wordRegexp(args) : null;
          return 'variable-2'
        }

        if (stream.eat(/[0-9]/) !== undefined)
          return 'number'

        //if (stream.eat(operator_regexp) !== undefined)
        //  return 'operator'
        
        if (state.args_regexp && stream.match(state.args_regexp) !== null)
          return 'variable-2'

        var ch = stream.next()

        return null;
      }

    };
    return external;
  });

  CodeMirror.defineMIME('text/x-flexio-commandbar', 'flexio-commandbar');
});
