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
  import {} from 'codemirror/mode/css/css'
  import {} from 'codemirror/mode/javascript/javascript'
  import {} from 'codemirror/mode/xml/xml'
  import {} from 'codemirror/mode/htmlmixed/htmlmixed'
  import {} from 'codemirror/mode/python/python'

  export default {
    props: {
      'val': {
        type: String,
        default: ''
      },
      'lang': {
        type: String,
        default: 'python'
      },
      'update-on-val-change': {
        type: Boolean,
        default: false
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    watch: {
      val: {
        handler: 'updateFromVal',
        immediate: true
      }
    },
    data() {
      return {
        code_text: '',
        editor: null,
        ss_errors: {}
      }
    },
    computed: {
      base64_code() {
        try { return btoa(this.code_text) } catch(e) { return '' }
      }
    },
    created() {
      this.code_text = this.val
    },
    mounted() {
      var lang = this.lang

      var opts = {
        lineNumbers: true,
        mode: this.getLang(),
        extraKeys: {
          // indent with 4 spaces for python and 2 spaces for all other languages
          Tab: function(cm) {
            //var spaces = Array(cm.getOption('indentUnit') + 1).join(' ')
            var spaces = lang == 'python' ? '    ' : '  '
            cm.replaceSelection(spaces)
          }
        }
      }

      if (this.lang == 'application/json')
        opts = _.assign(opts, { theme: 'flexio-json' })

      // apply any options passed to us (override defaults)
      opts = _.assign(opts, this.options)

      this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)
      this.editor.setCursor({ line: 1, ch: 1000000 })

      this.editor.on('change', (cm) => {
        this.code_text = cm.getValue()
        this.$emit('change', this.code_text, this.base64_code)
      })

      // set minimum height
      if (_.isNumber(opts.minHeight))
        this.editor.getScrollerElement().style.minHeight = opts.minHeight + 'px'
    },
    methods: {
      getLang() {
        return this.lang == 'html' ? 'htmlmixed' : this.lang
      },
      setValue(val) {
        this.code_text = val
        if (this.editor)
          this.editor.setValue(val)
      },
      reset() {
        this.code_text = this.val
        if (this.editor)
          this.editor.setValue(this.val)
      },
      updateFromVal() {
        if (this.updateOnValChange) {
          this.setValue(this.val)
        }
      }
    }
  }
</script>
