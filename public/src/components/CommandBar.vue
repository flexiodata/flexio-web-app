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

  // simple way to accomplish jQuery.data()
  // ref: http://stackoverflow.com/questions/29222027/vanilla-alternative-to-jquery-data-function-any-native-javascript-alternati
  window.$ = {
    data: function(obj, key, val) {
        if (!obj)
        {
          return this._data
        }
         else if (!key)
        {
          if (!(obj in this._data))
            return {}

          return this._data[obj]
        }
         else if (arguments.length < 3)
        {
          if (!(obj in this._data))
            return undefined

          return this._data[obj][key]
        }
         else
        {
          if (!(obj in this._data))
            this._data[obj] = {}

          this._data[obj][key] = val
        }
    },
    _data: {}
  }

  // helper function for creating DOM nodes to insert into the dropdown
  function createEl(tagname, cls /*, ... strings or elements*/)
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
      var base_cls = 'CodeMirror-flexio-cmdbar-'

      return {
        cmd_text: '',
        editor: null,
        dropdown_cls: base_cls,
        dropdown_item_cls: base_cls+'tooltip-item',
        dropdown_item_active_cls: base_cls+'tooltip-item-active',
        active_dropdown: null
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
      //this.editor.focus()
      //this.editor.setCursor({ line: 1, ch: 1000000 })

      this.editor.on('blur', (cm) => {
        this.closeDropdown()
      })

      this.editor.on('change', (cm) => {
        this.cmd_text = cm.getValue()
        this.$emit('change', this.cmd_text, this.cmd_json)
      })

      this.editor.on('keydown', (cm, evt) => {
        if (evt.key == 'Enter')
        {
          // don't allow multiple lines in the command bar
          evt.preventDefault()

          if (evt.ctrlKey === true)
          {
            // force a save when the user presses Ctrl+Enter
            this.save()
          }
           else if (this.active_dropdown)
          {
            // insert the value from the dropdown item into the CodeMirror editor
            this.useDropdownItem()
          }
           else
          {
            // save when user presses Enter without a dropdown open
            this.save()
          }
        }

        if (evt.code == 'ArrowUp')
        {
          evt.preventDefault()

          if (this.active_dropdown)
            this.highlightPrevDropdownItem()
        }

        if (evt.code == 'ArrowDown')
        {
          evt.preventDefault()

          if (this.active_dropdown)
            this.highlightNextDropdownItem()
        }
      })

      this.editor.on('cursorActivity', (cm) => {
        this.updateDropdown()
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

      save() {
        this.closeDropdown()
        this.$emit('save', this.cmd_text, this.cmd_json)
      },

      getHints(idx) {
        var val = this.editor.getValue()

        // if we didn't pass a number in, use the cursor position
        if (!_.isNumber(idx))
          idx = this.editor.getCursor().ch

        // if no text, no hint
        if (val.length == 0)
          return null

        return parser.getHints(val, idx, {})
      },

      /* -- autocomplete dropdown methods */

      createDropdown(x, y, content) {
        var node = createEl('div', this.dropdown_cls+'tooltip', content)
        node.style.left = x+'px'
        node.style.top = y+'px'
        document.body.appendChild(node)
        return node
      },

      closeDropdown() {
        if (this.active_dropdown)
        {
          if (this.active_dropdown.parentNode)
            this.active_dropdown.parentNode.removeChild(this.active_dropdown)

          this.active_dropdown = null
        }
      },

      showDropdown() {
        this.closeDropdown()

        var hints = this.getHints()
        if (_.isNil(hints) || hints.type == 'none' || !_.isArray(hints.items))
          return

        var tip = createEl('div', null)
        for (var i = 0; i < hints.items.length; ++i) {
          var tip_cls = this.dropdown_item_cls
          if (i == 0)
            tip_cls += ' '+this.dropdown_item_active_cls

          var child_el = tip.appendChild(createEl('div', tip_cls, hints.items[i]))
          $.data(child_el, 'item', hints.items[i])
        }

        var offset = this.editor.charCoords({ line: 0, ch: hints.offset }, 'page')
        this.active_dropdown = this.createDropdown(offset.left + 1, offset.bottom + 3, tip)
      },

      updateDropdown() {
        this.closeDropdown()

        var state = this.editor.getTokenAt(this.editor.getCursor()).state
        var inner = CodeMirror.innerMode(this.editor.getMode(), state)

        if (inner.mode.name != 'flexio-commandbar')
          return

        this.showDropdown()
      },

      getActiveDropdownItem() {
        if (this.active_dropdown)
        {
          var els = this.active_dropdown.getElementsByClassName(this.dropdown_item_active_cls)

          if (!_.isNil(els) && els.length == 1)
            return els[0]
        }

        return null
      },

      useDropdownItem() {
        var el = this.getActiveDropdownItem()

        if (!_.isNil(el))
        {
          var item = $.data(el, 'item')
          if (_.isString(item))
          {
            if (item.length == 0)
              return

            var hints = this.getHints()
            var word = this.editor.findWordAt({ line: 0, ch: hints.offset })
            var next_char = this.editor.getRange(word.head, { line: 0, ch: word.head.ch+1 })

            // account for potential colons that have already been inserted
            if (next_char == ':')
              word.head = { line: 0, ch: word.head.ch+1 }

            // do the replace
            this.editor.replaceRange(item, word.anchor, word.head)
          }
        }

        this.closeDropdown()
      },

      highlightPrevDropdownItem() {
        var el = this.getActiveDropdownItem()

        if (!_.isNil(el) && !_.isNil(el.previousSibling))
        {
          el.className = this.dropdown_item_cls
          el.previousSibling.className = this.dropdown_item_cls+' '+this.dropdown_item_active_cls
        }
      },

      highlightNextDropdownItem() {
        var el = this.getActiveDropdownItem()

        if (!_.isNil(el) && !_.isNil(el.nextSibling))
        {
          el.className = this.dropdown_item_cls
          el.nextSibling.className = this.dropdown_item_cls+' '+this.dropdown_item_active_cls
        }
      }
    }
  }
</script>
