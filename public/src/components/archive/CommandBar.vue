<template>
  <div>
    <textarea
      ref="textarea"
      spellcheck="false"
      v-model.trim="cmd_text"
    ></textarea>
  </div>
</template>

<script>
  import { HOSTNAME } from '../constants/common'
  import { TASK_OP_EXECUTE } from '../constants/task-op'
  import * as connections from '../constants/connection-info'
  import { mapGetters } from 'vuex'
  import CodeMirror from 'codemirror'
  import {} from 'codemirror/addon/hint/show-hint'
  import {} from 'codemirror/addon/display/placeholder'
  import {} from 'codemirror/addon/lint/lint'
  import {} from '../utils/commandbar-mode'
  import {} from '../utils/commandbar-lint'
  import parser from '../utils/parser'

  function toBase64(str) {
    try { return btoa(unescape(encodeURIComponent(str))) } catch(e) { return e }
  }

  function fromBase64(str) {
    try { return decodeURIComponent(escape(atob(str))) } catch(e) { return e }
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
      'active-process': {
        type: Object,
        default: () => { return {} }
      },
      'is-scrolling': {
        type: Boolean,
        default: false
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    watch: {
      isScrolling(val, old_val) {
        if (val === true)
          this.closeDropdown()
      },
      origJson(val, old_val) {
        var cmd_text = _.defaultTo(parser.toCmdbar(val), '')
        var end_idx = cmd_text.indexOf(' code:')

        if (_.get(val, 'op') == TASK_OP_EXECUTE && end_idx != -1)
          cmd_text = cmd_text.substring(0, end_idx)

        this.setValue(cmd_text)
      },
      input_columns(val, old_val) {
        // if a user clicks on the command bar in a place where
        // the column hint is supposed to show, but the columns
        // weren't loaded yet, this watcher will ensure that
        // the dropdown shows as soon as the query returns
        if (_.isArray(val) && val.length > 0)
          this.showDropdown()
      }
    },
    data() {
      var base_cls = 'CodeMirror-flexio-commandbar-'

      return {
        cmd_text: '',
        editor: null,
        freeze_cursor_activity: false,
        dropdown_hints: {},
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
      },
      process_eid() {
        return _.get(this.activeProcess, 'eid', '')
      },
      task_eid() {
        return _.get(this.origJson, 'eid', '')
      },
      process_task_id() {
        if (this.process_eid.length == 0 || this.task_eid.length == 0)
          return ''

        return this.process_eid + '--' + this.task_eid
      },
      input_columns() {
        var cols = _.get(this.$store, 'state.objects.'+this.process_task_id+'.input_columns', [])

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(cols)
          .sortBy([ function(c) { return c.name } ])
          .value()
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
        lint: { delay: 500 },
        theme: 'flexio-commandbar',
        mode: 'flexio-commandbar',
        placeholder: 'Type a command...'
      }, this.options)

      // create the CodeMirror editor
      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      //this.editor.focus()
      //this.editor.setCursor({ line: 1, ch: 1000000 })

      this.editor.on('focus', (cm) => {
        this.showDropdown()
        this.tryFetchColumns()
      })

      this.editor.on('blur', (cm) => {
        this.closeDropdown()
        this.clearSelection()
      })

      this.editor.on('change', (cm) => {
        this.cmd_text = cm.getValue()
        this.$emit('change', this.cmd_text, this.cmd_json)
      })

      this.editor.on('keydown', (cm, evt) => {
        if (evt.key == 'Escape')
        {
          evt.preventDefault()

          if (evt.ctrlKey === true)
          {
            // force a revert when the user presses Ctrl+Enter
            this.revert()
          }
           else if (this.active_dropdown)
          {
            this.closeDropdown()
          }
           else
          {
            // revert when user presses Escape without a dropdown open
            this.revert()
          }
        }
         else if (evt.key == 'Enter')
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
            this.replaceFromDropdownItem()
          }
           else
          {
            // save when user presses Enter without a dropdown open
            this.save()
          }
        }
         else if (evt.code == 'ArrowUp')
        {
          evt.preventDefault()

          if (this.active_dropdown)
            this.highlightPrevDropdownItem()
        }
         else if (evt.code == 'ArrowDown')
        {
          evt.preventDefault()

          if (this.active_dropdown)
            this.highlightNextDropdownItem()
        }
      })

      this.editor.on('cursorActivity', (cm) => {
        if (!this.freeze_cursor_activity)
          this.updateDropdown()
      })
    },
    beforeDestroy() {
      this.closeDropdown()
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),

      setValue(val) {
        // this 'freeze_cursor_activity' variable helps us know not to show
        // the dropdown on cursor activity in the CodeMirror editor
        this.freeze_cursor_activity = true
        this.cmd_text = val
        this.editor.setValue(val)
        setTimeout(() => { this.freeze_cursor_activity = false }, 500)
      },

      // does not fire an event
      reset() {
        this.setValue(this.val)
      },

      revert() {
        this.closeDropdown()
        this.reset()
        this.$emit('revert', this.cmd_text, this.cmd_json)
      },

      save() {
        this.closeDropdown()
        this.$emit('save', this.cmd_text, this.cmd_json)
      },

      clearSelection() {
        // this 'freeze_cursor_activity' variable helps us know not to show
        // the dropdown on cursor activity in the CodeMirror editor
        this.freeze_cursor_activity = true
        this.editor.setSelection({ line: 0, ch: 0 })
        setTimeout(() => { this.freeze_cursor_activity = false }, 500)
      },

      getHints(idx) {
        var val = this.editor.getValue()

        // if we didn't pass a number in, use the cursor position
        if (!_.isNumber(idx))
          idx = Math.max(this.editor.getCursor().ch, 0)

        var hints = parser.getHints(val, idx, {
          connections: this.getAllConnections(),
          columns: this.input_columns
        })
        hints.items = this.getFilteredDropdownItems(hints)

        // store dropdown hints for use elsewhere
        this.dropdown_hints = _.cloneDeep(hints)
        return this.dropdown_hints
      },

      getFilteredDropdownItems(hints) {
        if (_.isNil(hints))
          return []

        var items = hints.items
        if (!_.isArray(items) || items.length == 0)
          return []

        var findPartialMatch = function(obj, find_val) {
          var pick_arr = _
            .chain(obj)
            .mapValues(_.method('toLowerCase'))
            .values()
            .value()

          return _.some(pick_arr, _.method('includes', find_val))
        }

        if (hints.type == 'commands' ||
            hints.type == 'values'   ||
            hints.type == 'arguments')
        {
          return _
            .chain(hints.items)
            .filter(val => { return _.includes(_.toLower(val), _.toLower(hints.current_word)) })
            .value()
        }
         else if (hints.type == 'connections')
        {
          return _
            .chain(hints.items)
            .filter((obj, key) => {
              var find_val = _.toLower(hints.current_word)
              if (find_val.length == 0)
                return true

              return findPartialMatch(_.pick(obj, ['eid', 'ename', 'name', 'description']), find_val)
            })
            .compact()
            .value()
        }
         else if (hints.type == 'columns')
        {
          return _
            .chain(hints.items)
            .filter((obj, key) => {
              var find_val = _.toLower(hints.current_word)
              if (find_val.length == 0)
                return true

              return findPartialMatch(_.pick(obj, ['name']), find_val)
            })
            .compact()
            .value()
        }

        return []
      },

      getCurrentWord() {
        var word = this.editor.findWordAt({ line: 0, ch: this.dropdown_hints.offset })
        var first_char = this.editor.getRange(word.anchor, { line: 0, ch: word.anchor.ch+1 })
        var next_char = this.editor.getRange(word.head, { line: 0, ch: word.head.ch+1 })

        if (first_char == ' ')
          word.anchor = { line: 0, ch: word.anchor.ch+1 }

        // account for potential colons that have already been inserted
        if (next_char == ':')
          word.head = { line: 0, ch: word.head.ch+1 }

        return word
      },

      /* -- column fetching methods -- */

      tryFetchColumns() {
        if (this.process_task_id.length == 0)
          return

        var eid = this.process_eid
        var task_eid = this.task_eid

        // if we haven't fetched the columns for the active process task, do so now
        var is_fetched = _.get(this.$store, 'state.objects.'+this.process_task_id+'.input_fetched', false)
        if (!is_fetched)
          this.$store.dispatch('fetchProcessTaskInputInfo', { eid, task_eid })
      },

      /* -- autocomplete dropdown methods -- */

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
        var me = this

        this.closeDropdown()

        // get a new set of hints based on the cursor position
        var hints = this.getHints()

        // no hint or items, don't show a dropdown
        if (_.isNil(hints) || !_.isArray(hints.items) || hints.items.length == 0)
          return

        // don't show dropdown if there's only one result
        // and the current word is a full match
        if (hints.items.length == 1)
        {
          if (hints.type == 'commands' ||
              hints.type == 'values'   ||
              hints.type == 'arguments')
          {
            if (hints.items[0] == hints.current_word)
              return
          }
           else if (hints.type == 'connections')
          {
            var found = _
              .chain(hints.items[0])
              .pick(['eid', 'ename', 'name', 'description'])
              .includes(hints.current_word)
              .value()

            if (found)
              return
          }
           else if (hints.type == 'columns')
          {
            var found = _
              .chain(hints.items[0])
              .pick(['name'])
              .includes(hints.current_word)
              .value()

            if (found)
              return
          }
        }

        var tip = createEl('div', null)
        for (var i = 0; i < hints.items.length; ++i)
        {
          var tip_cls = this.dropdown_item_cls
          tip_cls += (i == 0) ? ' '+this.dropdown_item_active_cls : ''

          var child_el

          if (hints.type == 'commands' ||
              hints.type == 'values'   ||
              hints.type == 'arguments')
          {
            child_el = tip.appendChild(this.buildTextDropdownItem(tip_cls, hints.items[i]))
          }
           else if (hints.type == 'connections')
          {
            child_el = tip.appendChild(this.buildConnectionDropdownItem(tip_cls, hints.items[i]))
          }
           else if (hints.type == 'columns')
          {
            child_el = tip.appendChild(this.buildColumnDropdownItem(tip_cls, hints.items[i]))
          }

          // store item data with the DOM node
          this.setNodeData(child_el, 'item', _.cloneDeep(hints.items[i]))

          // update the command bar text when the user clicks on a dropdown item
          child_el.addEventListener('mousedown', function(evt) {
            evt.preventDefault()
            me.replaceFromDropdownItem(this)
          })
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

      replaceFromDropdownItem(el) {
        // if an element is passed to us, use it,
        // otherwise use the active dropdown item
        if (_.isNil(el))
          el = this.getActiveDropdownItem()

        if (!_.isNil(el))
        {
          var val = ''
          var item = this.getNodeData(el, 'item')

          if (!_.isNil(item))
          {
            if (this.dropdown_hints.type == 'commands' ||
                this.dropdown_hints.type == 'values'   ||
                this.dropdown_hints.type == 'arguments')
            {
              val = item
            }
             else if (this.dropdown_hints.type == 'connections')
            {
              var identifier = _.get(item, 'ename', '')
              identifier = identifier.length > 0 ? identifier : _.get(item, 'eid')
              val = identifier
            }
             else if (this.dropdown_hints.type == 'columns')
            {
              val = _.get(item, 'name', '')
            }

            if (val.length > 0)
            {
              // do the replace
              var word = this.getCurrentWord()
              this.editor.replaceRange(val+' ', word.anchor, word.head)
            }
          }
        }
      },

      setNodeData(el, key, val) {
        var str
        if (_.isObject(val))
          str = JSON.stringify(val)
           else
          str = val
        str = toBase64(str)
        el.setAttribute(key, str)
      },

      getNodeData(el, key) {
        var str = el.getAttribute(key)
        str = fromBase64(str)

        try
        {
          return JSON.parse(str)
        }
        catch(e)
        {
          return str
        }

        return null
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
      },

      buildTextDropdownItem(cls, text) {
        return createEl('div', cls, text)
      },

      buildConnectionDropdownItem(cls, item) {
        var c = _.assign({}, item)

        var identifier = _.get(c, 'ename', '')
        identifier = identifier.length > 0 ? identifier : _.get(c, 'eid')

        var cinfo = _.find(connections, { connection_type: c.connection_type })
        var icon = _.result(cinfo, 'icon', false)
        var name = _.result(cinfo, 'service_name', '')

        if (icon !== false)
          icon = '<img src="'+icon+'" alt="'+name+'" title="'+name+'" class="flex-none mr1 br2 square-2">'

        var html = '' +
          '<div class="flex flex-row items-center">' +
            (icon ? icon : '') +
            '<div class="flex-fill font-default fw6 lh-title pv2" style="font-size: 14px">'+c.name+'</div>' +
            '<div class="flex-none ml3">'+identifier+'</div>' +
          '</div>'

        var el = document.createElement('div')
        el.innerHTML = html

        return createEl('div', cls, el)
      },

      buildColumnDropdownItem(cls, item) {
        var c = _.assign({}, item)

        var html = '' +
          '<div class="flex flex-row items-center">' +
            '<div class="flex-fill black">'+c.name+'</div>' +
            '<div class="font-default ttu b ml3 bg-white-60 black-60 ba b--black-20" style="padding: 2px; font-size: 10px">'+c.type+'</div>' +
          '</div>'

        var el = document.createElement('div')
        el.innerHTML = html

        return createEl('div', cls, el)
      }
    }
  }
</script>
