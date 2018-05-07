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

  const DEFAULT_OPTIONS = {
    lineNumbers: true,
    mode: 'javascript'/*,
    extraKeys: {
      // indent with 4 spaces for python and 2 spaces for all other languages
      Tab: function(cm) {
        //var spaces = Array(cm.getOption('indentUnit') + 1).join(' ')
        var spaces = lang == 'python' ? '    ' : '  '
        cm.replaceSelection(spaces)
      }
    }*/
  }

  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: String,
        required: true
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
      opts() {
        return _.assign({}, DEFAULT_OPTIONS, this.options)
      }
    },
    methods: {
      onChange(value) {
        this.$emit('input', value)
      }
    }
  }
</script>
