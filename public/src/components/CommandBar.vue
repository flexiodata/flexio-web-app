<template>
  <div @mouseup="onMouseup">
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
  import Awesomplete from './experimental/Awesomplete2'
  import {} from '../../node_modules/codemirror/addon/display/placeholder'
  import {} from '../utils/commandbar-mode'
  import parser from '../utils/parser'

  /* -- BEGIN AWESOMEPLETE HELPERS -- */

  /*
  // unused as of this moment...
  var sortOriginal = function(a, b) { return false }
  var sortReverse = function(a, b) { return true }
  var sortAlphaAscending = function(a, b) { return a.toLowerCase() > b.toLowerCase() }
  var sortAlphaDescending = function(a, b) { return a.toLowerCase() < b.toLowerCase() }
  */

  var sortObjNameAlphaAscending = function(a, b) {
    var s1 = _.get(a, 'value.name', '').toLowerCase()
    var s2 = _.get(b, 'value.name', '').toLowerCase()
    return s1 > s2
  }

  var sortObjNameAlphaDescending = function(a, b) {
    var s1 = _.get(a, 'value.name', '').toLowerCase()
    var s2 = _.get(b, 'value.name', '').toLowerCase()
    return s1 < s2
  }

  // copied from Awesomplete $
  function $(expr, con) {
    return typeof expr === 'string'? (con || document).querySelector(expr) : expr || null
  }

  // copied from Awesomplete $.regExpEscape
  $.regExpEscape = function (s) {
    return s.replace(/[-\\^$*+?.()|[\]{}]/g, "\\$&")
  }

  // copied from Awesomplete $.create
  $.create = function(tag, o) {
    var el = document.createElement(tag)

    for (var i in o) {
      var val = o[i]

      if (i === 'inside') {
        $(val).appendChild(el)
      }
      else if (i === 'around') {
        var ref = $(val)
        ref.parentNode.insertBefore(el, ref)
        el.appendChild(ref)
      }
      else if (i in el) {
        el[i] = val
      }
      else {
        el.setAttribute(i, val)
      }
    }

    return el
  }

  /* -- END AWESOMEPLETE HELPERS -- */

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

      this.editor.on('blur', (cm) => {
        this.hideDropdown()
      })

      this.editor.on('keydown', (cm, evt) => {
        // show the dropdown
        if (evt.code == 'Escape')
        {
          if (this.dropdown_open)
            this.hideDropdown()
            // else
            // revert changes
        }

        // show the dropdown
        if (evt.code == 'Space' && evt.ctrlKey === true)
          setTimeout(() => { this.updateDropdown() }, 10)
      })

      this.editor.on('keyup', (cm, evt) => {
        // don't do anything when these keys are pressed on their own
        if (_.includes(['Control','Alt','Shift','Meta'], evt.key))
          return

        // already handled by keydown.esc and keydown.enter.prevent
        if (_.includes(['Escape','Enter'], evt.code))
          return

        // handled in keydown
        if (evt.code == 'Enter' && evt.ctrlKey === true)
          return

        // handled in keydown
        if (evt.code == 'Space' && evt.ctrlKey === true)
          return

        if (this.dropdown_open)
        {
          if (evt.code == 'ArrowUp' || evt.code == 'ArrowDown')
            return
        }

        // for all other keys, make sure the dropdown is open
        this.updateDropdown()
      })

      // create awesomplete
      this.$nextTick(() => {
        this.ta = this.$refs['textarea']
        this.ac = new Awesomplete(this.ta, {
          list: [],
          autoFirst: true,
          minChars: 0,
          maxItems: 1000,
          useSort: false, // we'll sort on our own
          filter(text, input) {
            var str = _.get(me.hint, 'current_word', '')
            return Awesomplete.FILTER_CONTAINS(text, str.match(/[^ ]*$/)[0])
          },
          replace(text) {
            me.insert_char_end_idx = me.editor.getCursor().ch
          }
        })

        this.ta.addEventListener('awesomplete-open', this.onDropdownOpen = () => {
          me.dropdown_open = true
        })

        this.ta.addEventListener('awesomplete-close', this.onDropdownClose = () => {
          me.dropdown_open = false
        })
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
      /* -- BEGIN AWESOMPLETE METHODS -- */
      onMouseup() {
        setTimeout(() => { this.updateDropdown() }, 10)
      },
      showDropdown() {
        if (_.isNil(this.ac))
          return

        if (this.dropdown_open)
          return

        // show the droplist on focus (even if we haven't typed anything)
        if (this.ac.ul.childNodes.length === 0)
        {
          this.ac.minChars = 0
          this.ac.evaluate()
        }
         else
        {
          this.ac.open()
        }

        this.insert_char_start_idx = this.editor.getCursor().ch
      },
      hideDropdown() {
        if (!_.isNil(this.ac))
          this.ac.close()
      },
      updateDropdown(char_idx) {
        if (_.isNil(this.ac))
          return

        var caret_idx = this.editor.getCursor().ch

        this.hint = parser.getHints(this.cmd_text, caret_idx, {
          connections: [],
          columns: []
        })

        if (this.hint.type == 'none')
        {
          this.hideDropdown()
          return
        }

        // update the dropdown contents
        if (this.hint.type == 'connections')
        {
          this.hint.items = _
            .chain(this.hint.items)
            .sortBy([ function(c) { return _.get(c, 'name', '').toLowerCase() } ])
            .value()

          this.renderConnectionDropdown()
        }
         else if (this.hint.type == 'columns')
        {
          this.hint.items = _
            .chain(this.hint.items)
            .sortBy([ function(c) { return _.get(c, 'name', '').toLowerCase() } ])
            .value()

          this.renderColumnDropdown()
        }
         else
        {
          this.renderDefaultDropdown()
        }

        // update the list items
        this.ac.list = this.hint.items

        // position the dropdown
        if (this.ac.ul)
        {
          if (char_idx === undefined)
            char_idx = this.hint.offset

          var pos = this.editor.cursorCoords(true, 'local')
          this.ac.ul.style.top = (pos.top + 16)+'px'
          this.ac.ul.style.left = (pos.left)+'px'
        }

        // show the dropdown
        if (!this.dropdown_open)
          this.showDropdown()
      },
      renderConnectionDropdown() {
        var me = this

        this.ac.sort = sortObjNameAlphaAscending

        this.ac.data = function (item, input) {
          var label = _.get(item, 'ename')
          label = label.length > 0 ? label : _.get(item, 'eid')
          return { label: label, value: item }
        }

        this.ac.filter = function(item, input) {
          var c = _.assign({}, item.value)
          var str = _.get(me.hint, 'current_word', '')

          var matches = _
            .chain(c)
            .pick(['eid', 'name', 'ename', 'description'])
            .filter((val, key) => { return _.includes(_.toLower(val), _.toLower(str)) })
            .value()

          return matches.length > 0 ? true : false
        },

        this.ac.item =  function(item, input) {
          var c = _.assign({}, item.value)

          var identifier = _.get(c, 'ename', '')
          identifier = identifier.length > 0 ? identifier : _.get(c, 'eid')

          var cinfo = _.find(connections, { connection_type: c.connection_type })
          var icon = _.result(cinfo, 'icon', false)
          var name = _.result(cinfo, 'service_name', '')

          if (icon !== false)
            icon = '<img src="'+icon+'" alt="'+name+'" title="'+name+'" class="dib v-mid br2 fx-square-2">'

          var html = '' +
            '<div class="flex flex-row items-center pv1">' +
              (icon ? '<div class="flex-none lh-title mr2">'+icon+'</div>' : '') +
              '<div class="flex-fill">' +
                '<div class="font-default f6 fw6 lh-title">'+c.name+'</div>' +
              '</div>' +
              '<div class="flex-none ml3">'+identifier+'</div>' +
            '</div>' +
          ''

          return $.create('li', {
            innerHTML: html,
            'aria-selected': 'false'
          })
        }
      },
      renderColumnDropdown() {
        var me = this

        this.ac.data = function (item, input) {
          var label = item.name
          _.includes(label, ' ') ? '['+label+']' : label
          return { label: label, value: item }
        }

        this.ac.filter = function(text, input) {
          var str = _.get(me.hint, 'current_word', '')
          return Awesomplete.FILTER_CONTAINS(text, str.match(/[^ ]*$/)[0])
        },

        this.ac.item =  function(item, input) {
          var c = _.assign({}, item.value)

          var html = '' +
            '<div class="flex flex-row items-center">' +
              '<div class="flex-fill black">' + c.name + '</div>' +
              '<div class="font-default ttu b ml3 bg-white-60 black-60 ba b--black-20" style="padding: 2px; font-size: 10px">' + c.type + '</div>' +
            '</div>' +
          ''

          return $.create('li', {
            innerHTML: html,
            'aria-selected': 'false'
          })
        }
      },
      renderDefaultDropdown() {
        var me = this

        this.ac.data = function (item, input) {
          return item
        }

        this.ac.filter = function(text, input) {
          var str = _.get(me.hint, 'current_word', '')
          return Awesomplete.FILTER_CONTAINS(text, str.match(/[^ ]*$/)[0])
        },

        this.ac.item = function(text, input) {
          var str = _.get(me.hint, 'current_word', '')
          var html = input === '' ? text : text.replace(RegExp($.regExpEscape(str.trim()), 'gi'), '<mark>$&</mark>')

          return $.create('li', {
            innerHTML: html,
            'aria-selected': 'false'
          })
        }
      }
      /* -- END AWESOMPLETE METHODS -- */
    }
  }
</script>
