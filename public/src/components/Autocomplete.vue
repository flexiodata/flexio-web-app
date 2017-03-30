<template>
  <div>
    <textarea
      title
      ref="textarea"
      class="awesomeplete resize-none overflow-y-auto"
      spellcheck="false"
      data-multiple
      :placeholder="placeholder"
      :class="inputClass"
      @keydown.esc="revert"
      @keydown.enter.prevent.stop="save"
      @keydown="onKeydown"
      @keyup="onKeyup"
      @mouseup="onMouseup"
      @focus="onFocus"
      @blur="onBlur"
      v-model="text"
    ></textarea>
  </div>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import Awesomplete from './experimental/Awesomplete2'
  import autosize from 'autosize'
  import parser from '../utils/parser'

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

  export default {
    props: {
      'val': {},
      'placeholder': {},
      'input-class': {},
      'connections': {},
      'columns': {},
      'autosize': { default: true }
    },
    watch: {
      val(val, old_val) {
        this.text = val
      },
      text(val, old_val) {
        this.updateTextareaHeight()
        this.$emit('change', this.text)
      }
    },
    data() {
      return {
        text: this.val,
        dropdown_open: false,
        autosize_initialized: false
      }
    },
    mounted() {
      var me = this

      this.$nextTick(() => {
        this.ta = this.$refs['textarea']
        this.ta.setAttribute('rows', '1')

        if (this.autosize)
        {
          autosize(this.ta)
          this.autosize_initialized = true
        }

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
            var before = this.input.value.match(/^.+ \s*|/)[0]

            if (me.hint.type == 'columns')
              me.text = before + text + ', '
               else if (me.hint.type == 'connections')
              me.text = before + text + ' '
               else
              me.text = before + text + ' '

            // show the dropdown for the next token
            me.$nextTick(() => {
              me.updateDropdown()
            })
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
    beforeDestroy() {
      if (this.autosize_initialized)
        autosize.destroy(this.ta)

      this.ta.removeEventListener('awesomplete-open', this.onDropdownOpen)
      this.ta.removeEventListener('awesomplete-close', this.onDropdownClose)
    },
    methods: {
      focus() {
        this.ta.focus()
      },
      // debounce for now -- this function is slow in FF...
      updateTextareaHeight: _.debounce(function() {
        if (this.autosize_initialized)
          autosize.update(this.ta)
      }, 200),
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
      },
      hideDropdown() {
        this.ac.close()
      },
      updateDropdown(char_idx) {
        if (_.isNil(this.ac))
          return

        /*
        var caret_idx = tahelper.getCaretIndex()

        this.hint = parser.getHints(this.text, caret_idx, {
          connections: this.connections,
          columns: this.columns
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

          var pos = tahelper.getCharCoordinates(char_idx)
          this.ac.ul.style.top = (pos.top + 20)+'px'
          this.ac.ul.style.left = (pos.left)+'px'
        }
        */

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
      },
      revert() {
        if (_.isNil(this.ac))
          return

        // if the dropdown is open, close it
        if (this.dropdown_open)
        {
          this.hideDropdown()
        }
         else
        {
          this.text = this.val
          this.$emit('revert', this.text)
        }
      },
      save(evt) {
        if (!this.dropdown_open || evt.ctrlKey === true)
        {
          this.hideDropdown()
          this.$emit('save', this.text)
        }
      },
      onKeydown(evt) {
        // show the dropdown
        if (evt.code == 'Space' && evt.ctrlKey === true)
          setTimeout(() => { this.updateDropdown() }, 10)
      },
      onKeyup(evt) {
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
      },
      onMouseup() {
        this.updateDropdown()
      },
      onFocus() {
      },
      onBlur() {
        this.hideDropdown()
      }
    }
  }
</script>
