<template>
  <div class="flex flex-column">
    <div class="flex-fill flex flex-row items-stretch relative">
      <div class="flex-fill pa2">
        <textarea
          ref="textarea"
          class="input-reset border-box w-100 h-100 bn outline-0 m0 p0 f6 code resize-none"
          placeholder="Type a command..."
          spellcheck="false"
          v-model.trim="cmd_text"
          @keydown.esc="clear"
        ></textarea>
      </div>
      <div class="flex-fill">
        <pre
          class="flex-fill w-100 h-100 outline-0 ma0 ph2 bl b--black-20 pa2 f6 overflow-auto"
          spellcheck="false"
        >{{cmd_text}}</pre>
      </div>
    </div>
    <div class="flex-none flex flex-row items-center pa1 bt b--black-20">
      <span class="b blue">&gt;&nbsp;</span>
      <div class="flex-fill f6">{{output}}</div>
    </div>
  </div>
</template>

<script>
  import tahelper from './experimental/textarea-helper.js'

  export default {
    data() {
      return {
        cmd_text: '',
        output: ''
      }
    },
    watch: {
      cmd_text() {
        this.output = '' +
          'Position: ' + JSON.stringify(this.getDropdownPosition()) + ' ' +
          'Height: ' + tahelper.getComputedHeight()
      }
    },
    mounted() {
      tahelper.init(this.$refs['textarea'])
    },

    beforeDestroy() {
      tahelper.destroy()
    },
    methods: {
      getDropdownPosition() {
        var caret_pos = tahelper.getCaretCoordinates()
        return caret_pos
      }
    }
  }
</script>
