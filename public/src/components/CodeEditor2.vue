<template>
  <CodeMirror
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
  import {} from 'codemirror/mode/css/css'
  import {} from 'codemirror/mode/javascript/javascript'
  import {} from 'codemirror/mode/xml/xml'
  import {} from 'codemirror/mode/htmlmixed/htmlmixed'
  import {} from 'codemirror/mode/python/python'

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
    methods: {
      onChange(value) {
        this.$emit('input', value)
      }
    }
  }
</script>
