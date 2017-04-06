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
        dropdown_cls: 'CodeMirror-flexio-cmdbar-',
        insert_char_start_idx: 0,
        insert_char_end_idx: 0,
        active_tooltip: null
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




      // dropdown hinting engine
      function elt(tagname, cls /*, ... strings or elements*/) {

        var e = document.createElement(tagname)
        if (cls) e.className = cls
        for (var i = 2; i < arguments.length; ++i) {
          var elt = arguments[i]
          if (typeof(elt) == 'string') {
            elt = document.createTextNode(elt)
          }
          e.appendChild(elt)
        }
        return e
      }

      function makeTooltip(x, y, content) {
        var node = elt('div', me.dropdown_cls + 'tooltip', content)
        node.style.left = x + 'px'
        node.style.top = y + 'px'
        document.body.appendChild(node)
        return node
      }

      function closeArgHints() {

        if (me.active_tooltip) {
          if (me.active_tooltip.parentNode) {
            me.active_tooltip.parentNode.removeChild(me.active_tooltip)
          }
          me.active_tooltip = null
        }
      }

      function showArgHints() {
        
        closeArgHints()

        var idx = me.editor.getCursor().ch
        var value = me.editor.getValue()

        // if no text, no hint
        if (value.length == 0)
          return

        var hints = parser.getHints(value, idx, {})

        if (hints.type == 'none')
          return

        if (!_.isArray(hints.items))
          return

        var tip = elt("span", null);
        for (var i = 0; i < hints.items.length; ++i) {
          tip.appendChild(elt("span", "db", hints.items[i]))
        }

        var place = me.editor.cursorCoords(null, "page")

        me.active_tooltip = makeTooltip(place.right + 1, place.bottom, tip)
      }

      function updateArgHints() {
        
        closeArgHints()

        var state = me.editor.getTokenAt(me.editor.getCursor()).state
        var inner = CodeMirror.innerMode(me.editor.getMode(), state)

        if (inner.mode.name != "flexio-commandbar") {
          return
        }

        showArgHints()
      }


      this.editor.on('cursorActivity', function(cm) { updateArgHints() });
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
