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

  // helper function for creating DOM nodes to insert into the dropdown
  function elt(tagname, cls /*, ... strings or elements*/)
  {
    var el = document.createElement(tagname)

    if (cls)
      el.className = cls

    for (var i = 2; i < arguments.length; ++i)
    {
      var child_el = arguments[i]

      if (typeof(child_el) == 'string')
        child_el = document.createTextNode(child_el)

      el.appendChild(child_el)
    }

    return el
  }


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
        //extraKeys: { 'Ctrl-Space': 'autocomplete' },
        mode: 'flexio-commandbar',
        placeholder: 'Type a command...'
      }, this.options)

      // create the CodeMirror editor
      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      this.editor.focus()
      this.editor.setCursor({ line: 1, ch: 1000000 })

      this.editor.on('blur', (cm) => {
        this.closeArgHints()
      })

      this.editor.on('change', (cm) => {
        this.cmd_text = cm.getValue()
        this.$emit('change', this.cmd_text, this.cmd_json)
      })

      this.editor.on('cursorActivity', (cm) => {
        this.updateArgHints()
      })
    },
    methods: {
      setValue(val) {
        this.cmd_text = val
        this.editor.setValue(val)
      },

      reset() {
        this.cmd_text = this.val
        this.editor.setValue(this.val)
      },

      /* -- autocomplete dropdown methods */

      makeTooltip(x, y, content) {
        var node = elt('div', this.dropdown_cls+'tooltip', content)
        node.style.left = x+'px'
        node.style.top = (y+3)+'px'
        document.body.appendChild(node)
        return node
      },

      closeArgHints() {
        if (this.active_tooltip)
        {
          if (this.active_tooltip.parentNode)
            this.active_tooltip.parentNode.removeChild(this.active_tooltip)

          this.active_tooltip = null
        }
      },

      showArgHints() {
        this.closeArgHints()

        var idx = this.editor.getCursor().ch
        var val = this.editor.getValue()

        // if no text, no hint
        if (val.length == 0)
          return

        var hints = parser.getHints(val, idx, {})

        if (hints.type == 'none' || !_.isArray(hints.items))
          return

        var tip = elt('span', null)
        for (var i = 0; i < hints.items.length; ++i) {
          tip.appendChild(elt('span', this.dropdown_cls+'tooltip-item', hints.items[i]))
        }

        var offset = this.editor.cursorCoords(null, 'page')
        this.active_tooltip = this.makeTooltip(offset.right + 1, offset.bottom, tip)
      },

      updateArgHints() {
        this.closeArgHints()

        var state = this.editor.getTokenAt(this.editor.getCursor()).state
        var inner = CodeMirror.innerMode(this.editor.getMode(), state)

        if (inner.mode.name != 'flexio-commandbar')
          return

        this.showArgHints()
      }
    }
  }
</script>
