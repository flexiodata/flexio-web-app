<template>
  <div class="flex flex-column">
    <div class="flex-fill flex flex-row items-stretch relative">
      <textarea
        ref="textarea"
        class="input-reset border-box w-100 h-100 bn outline-0 m0 p0 f6 code"
        style="resize: none"
        placeholder="Code goes here"
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
      var me = this
      var opts = _.assign({
        lineNumbers: true,
        mode: this.lang
      }, this.options)

      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      this.editor.focus()

      this.editor.on('change', function(cm) {
        me.code_text = cm.getValue()
        me.$emit('change', me.code_text)
      })
    },
    methods: {
      setValue(val) {
        this.editor.setValue(val)
      }
    }
  }
</script>
