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
  import { TASK_TYPE_EXECUTE } from '../constants/task-type'
  import * as connections from '../constants/connection-info'
  import CodeMirror from 'codemirror'
  import {} from '../utils/commandbar-mode'
  import parser from '../utils/parser'

  export default {
    props: {
      'val': {
        default: ''
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    data() {
      return {
        cmd_text: '',
        editor: null
      }
    },
    created() {
      this.cmd_text = this.val
    },
    mounted() {
      var opts = _.assign({
        lineNumbers: false,
        lineWrapping: true,
        mode: 'flexio-commandbar'
      }, this.options)

      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      this.editor.focus()

      this.editor.on('change', (cm) => {
        this.cmd_text = cm.getValue()
        this.$emit('change', this.cmd_text)
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
      }
    }
  }
</script>
