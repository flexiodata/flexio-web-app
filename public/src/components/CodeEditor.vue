<template>
  <CodeMirror
    ref="editor"
    :value="value"
    :options="opts"
    @input="onChange"
    v-bind="$attrs"
  />
</template>

<script>
  // vue-codemirror includes
  import { codemirror } from 'vue-codemirror'
  import 'codemirror/lib/codemirror.css'
  import {} from 'codemirror/mode/javascript/javascript'
  import {} from 'codemirror/mode/yaml/yaml'
  import {} from 'codemirror/mode/python/python'
  //import {} from 'codemirror/mode/css/css'
  //import {} from 'codemirror/mode/xml/xml'
  //import {} from 'codemirror/mode/htmlmixed/htmlmixed'

  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: String,
        required: true
      },
      lang: {
        type: String,
        default: 'javascript'
      },
      options: {
        type: Object,
        default: () => { return {} }
      }
    },
    components: {
      CodeMirror: codemirror
    },
    computed: {
      mode() {
        return this.lang == 'html' ? 'htmlmixed' : this.lang
      },
      default_opts() {
        return {
          minRows: 16,
          maxRows: -1,
          /*
          minHeight: number|string, // overrides minRows
          maxHeight: number|string, // overrides maxRows
          */
          lineNumbers: true,
          mode: this.mode,
          extraKeys: {
            // indent with 4 spaces for python and 2 spaces for all other languages
            Tab: function(cm) {
              //var spaces = Array(cm.getOption('indentUnit') + 1).join(' ')
              var mode = cm.getOption('mode')
              var spaces = mode == 'python' ? '    ' : '  '
              cm.replaceSelection(spaces)
            }
          }
        }
      },
      opts() {
        return _.assign({}, this.default_opts, this.options)
      }
    },
    mounted() {
      this.updateMinMaxHeight()
    },
    methods: {
      getMinHeight() {
        // `minHeight` overrides `minRows`
        var min_h = this.opts.minHeight || (this.opts.minRows * 14) + 8

        // default to pixels if only a number is provided
        return _.isNumber(min_h) ? min_h + 'px' : min_h
      },
      getMaxHeight() {
        if (!_.isNil(this.opts.maxHeight) && this.opts.maxRows < 0)
          return undefined

        // `maxHeight` overrides `maxRows`
        var max_h = this.opts.maxHeight || (this.opts.maxRows * 14) + 8

        // default to pixels if only a number is provided
        return _.isNumber(max_h) ? max_h + 'px' : max_h
      },
      updateMinMaxHeight() {
        var scroller = this.$refs.editor.codemirror.getScrollerElement()
        scroller.style.minHeight = this.getMinHeight()
        scroller.style.maxHeight = this.getMaxHeight()
      },
      onChange(value) {
        this.$emit('input', value)
      }
    }
  }
</script>
