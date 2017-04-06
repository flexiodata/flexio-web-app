<template>
  <div>
    <textarea
      ref="textarea"
      class="awesomeplete"
      spellcheck="false"
      v-model.trim="cmd_text"
    ></textarea>
  </div>
</template>

<script>
  import { HOSTNAME } from '../constants/common'
  import { TASK_TYPE_EXECUTE } from '../constants/task-type'
  import * as connections from '../constants/connection-info'
  import CodeMirror from 'codemirror'
  import {} from '../../node_modules/codemirror/addon/hint/show-hint'
  import {} from '../../node_modules/codemirror/addon/display/placeholder'
  import {} from '../utils/commandbar-mode'
  import parser from '../utils/parser'

  export default {
    props: {
      'val': {
        default: ''
      },
      'orig-json': {
        type: Object,
        default: () => { return {} }
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    watch: {
      origJson(val, old_val) {
        var cmd_text = _.defaultTo(parser.toCmdbar(val), '')
        var end_idx = cmd_text.indexOf(' code:')

        if (_.get(val, 'type') == TASK_TYPE_EXECUTE && end_idx != -1)
          cmd_text = cmd_text.substring(0, end_idx)

        this.setValue(cmd_text)
      }
    },
    data() {
      return {
        cmd_text: '',
        editor: null,
        dropdown_open: false,
        insert_char_start_idx: 0,
        insert_char_end_idx: 0
      }
    },
    computed: {
      is_changed() {
        return this.cmd_text != this.val
      },
      cmd_json() {
        return this.is_changed ? parser.toJSON(this.cmd_text) : this.origJson
      }
    },
    created() {
      this.cmd_text = this.val
    },
    mounted() {
      var me = this
      var opts = _.assign({
        lineNumbers: false,
        lineWrapping: true,
        extraKeys: {"Ctrl-Space": "autocomplete"},
        mode: 'flexio-commandbar',
        placeholder: 'Type a command...'
      }, this.options)

      // create the CodeMirror editor
      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      this.editor.focus()
      this.editor.setCursor({ line: 1, ch: 1000000 })

      this.editor.on('change', (cm) => {
        this.cmd_text = cm.getValue()
        this.$emit('change', this.cmd_text, this.cmd_json)
      })




var cls = 'CodeMirror-flexio-cmdbar-';
var ts = {}

function elt(tagname, cls /*, ... elts*/) {
  var e = document.createElement(tagname);
  if (cls) e.className = cls;
  for (var i = 2; i < arguments.length; ++i) {
    var elt = arguments[i];
    if (typeof elt == "string") elt = document.createTextNode(elt);
    e.appendChild(elt);
  }
  return e;
}

function remove(node) {
    var p = node && node.parentNode;
    if (p) p.removeChild(node);
}

function makeTooltip(x, y, content) {
    var node = elt("div", cls + "tooltip", content);
    node.style.left = x + "px";
    node.style.top = y + "px";
    document.body.appendChild(node);
    return node;
}

function closeArgHints(ts) {
  if (ts.activeArgHints) { remove(ts.activeArgHints); ts.activeArgHints = null; }
}

function showArgHints(ts, cm, pos) {
  closeArgHints(ts);

  var tip = elt("span", null, 
                  elt("span", "db", "Test1"),
                  elt("span", "db", "Test2"),
                  elt("span", "db", "Test3")
                  );
  var place = cm.cursorCoords(null, "page");
  //console.log("(" + place.right + "," + place.bottom + ")");

  ts.activeArgHints = makeTooltip(place.right + 1, place.bottom, tip);
/*
  var cache = ts.cachedArgHints, tp = cache.type;
  var tip = elt("span", cache.guess ? cls + "fhint-guess" : null,
                elt("span", cls + "fname", cache.name), "(");
  for (var i = 0; i < tp.args.length; ++i) {
    if (i) tip.appendChild(document.createTextNode(", "));
    var arg = tp.args[i];
    tip.appendChild(elt("span", cls + "farg" + (i == pos ? " " + cls + "farg-current" : ""), arg.name || "?"));
    if (arg.type != "?") {
      tip.appendChild(document.createTextNode(":\u00a0"));
      tip.appendChild(elt("span", cls + "type", arg.type));
    }
  }
  tip.appendChild(document.createTextNode(tp.rettype ? ") ->\u00a0" : ")"));
  if (tp.rettype) tip.appendChild(elt("span", cls + "type", tp.rettype));
  var place = cm.cursorCoords(null, "page");
  ts.activeArgHints = makeTooltip(place.right + 1, place.bottom, tip);
  */
}

function updateArgHints(ts, cm) {

  closeArgHints(ts);

  var state = cm.getTokenAt(cm.getCursor()).state;
  var inner = CodeMirror.innerMode(cm.getMode(), state);
  console.log(inner.mode.name);
  
  if (inner.mode.name != "flexio-commandbar") return;

  var ch, pos = 0;


  showArgHints(ts, cm, pos);

  /*
    closeArgHints(ts);

    if (cm.somethingSelected()) return;
    var state = cm.getTokenAt(cm.getCursor()).state;
    var inner = CodeMirror.innerMode(cm.getMode(), state);
    if (inner.mode.name != "javascript") return;
    var lex = inner.state.lexical;
    if (lex.info != "call") return;

    var ch, argPos = lex.pos || 0, tabSize = cm.getOption("tabSize");
    for (var line = cm.getCursor().line, e = Math.max(0, line - 9), found = false; line >= e; --line) {
      var str = cm.getLine(line), extra = 0;
      for (var pos = 0;;) {
        var tab = str.indexOf("\t", pos);
        if (tab == -1) break;
        extra += tabSize - (tab + extra) % tabSize - 1;
        pos = tab + 1;
      }
      ch = lex.column - extra;
      if (str.charAt(ch) == "(") {found = true; break;}
    }
    if (!found) return;

    var start = Pos(line, ch);
    var cache = ts.cachedArgHints;
    if (cache && cache.doc == cm.getDoc() && cmpPos(start, cache.start) == 0)
      return showArgHints(ts, cm, argPos);

    ts.request(cm, {type: "type", preferFunction: true, end: start}, function(error, data) {
      if (error || !data.type || !(/^fn\(/).test(data.type)) return;
      ts.cachedArgHints = {
        start: start,
        type: parseFnType(data.type),
        name: data.exprName || data.name || "fn",
        guess: data.guess,
        doc: cm.getDoc()
      };
      showArgHints(ts, cm, argPos);
    });
  */
}











      this.editor.on('cursorActivity', function(cm) { updateArgHints(ts, cm) });
    },
    methods: {
      setValue(val) {
        this.cmd_text = val
        this.editor.setValue(val)
      },
      reset() {
        this.cmd_text = this.val
        this.editor.setValue(this.val)
      }
    }
  }
</script>
