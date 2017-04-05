<template>
  <div>
    <textarea
      ref="textarea"
      class="w-100 h-100 bn resize-none"
      spellcheck="false"
      :placeholder="placeholder"
      :class="inputClass"
      @keydown.esc="revert"
      @keydown.enter.prevent.stop="save"
      v-model.trim="text"
    ></textarea>
  </div>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import CodeMirror from 'codemirror'
  import {} from '../utils/commandbar-mode'

  export default {
    props: {
      'val': {},
      'placeholder': {},
      'input-class': {},
      'connections': {},
      'columns': {}
    },
    watch: {
      val(val, old_val) {
        this.text = val
      },
      text(val, old_val) {
        this.$emit('change', this.text)
      }
    },
    data() {
      return {
        text: this.val,
        editor: null
      }
    },
    mounted() {
      var opts = {
        lineNumbers: false,
        lineWrapping: true,
        mode: 'flexio-commandbar'
      }

      this.text = this.val

      this.$nextTick(() => {
        this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)

        this.editor.on('change', (cm) => {
          this.text = cm.getValue()
          this.$emit('change', this.text)
        })
      })
    },
    methods: {
      focus() {
        this.$refs['textarea'].focus()
      },

      revert() {
        if (_.isNil(this.ac))
          return

        this.text = this.val
        this.$emit('revert', this.text)
      },

      save(evt) {
        this.$emit('save', this.text)
      }
    }
  }
</script>
