<template>
  <div class="flex flex-column">
    <div class="flex-fill flex flex-row items-stretch relative">
      <textarea
        ref="textarea"
        spellcheck="false"
        v-model.trim="code_text"
      ></textarea>
    </div>
  </div>
</template>

<script>
  import CodeMirror from 'codemirror'
  import {} from 'codemirror/mode/javascript/javascript'
  import {} from 'codemirror/mode/python/python'

  export default {
    props: {
      'val': {
        default: ''
      },
      'lang': {
        default: 'python'
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    data() {
      return {
        code_text: '',
        editor: null
      }
    },
    created() {
      this.code_text = this.val
    },
    mounted() {
      var opts = _.assign({
        lineNumbers: true,
        mode: this.lang
      }, this.options)

      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      this.editor.focus()

      this.editor.on('change', (cm) => {
        this.code_text = cm.getValue()
        this.$emit('change', this.code_text)
      })
    },
    methods: {
      setValue(val) {
        this.code_text = val
        this.editor.setValue(val)
      },
      reset() {
        this.code_text = this.val
        this.editor.setValue(this.val)
      }
    }
  }
</script>
