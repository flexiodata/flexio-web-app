<template>
  <div class="flex flex-row items-stretch relative">
    <div class="flex-fill">
      <textarea type="text"
        class="input-reset border-box w-100 h-100 bn outline-0 m0 pa2 f6 code"
        style="resize: none"
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
      >{{cmd_json}}</pre>
    </div>
    <div class="flex-fill">
      <pre
        class="flex-fill w-100 h-100 outline-0 ma0 ph2 bl b--black-20 pa2 f6 overflow-auto"
        spellcheck="false"
      >{{result_cmd}}</pre>
    </div>
  </div>
</template>

<script>
  import parser from '../utils/parser'

  export default {
    data() {
      return {
        cmd_text: ''
      }
    },
    computed: {
      cmd_json() {
        return parser.toJSON(this.cmd_text)
      },
      result_cmd() {
        return parser.toCmdbar(this.cmd_json)
      }
    },
    methods: {
      clear() {
        this.cmd_text = ''
      }
    }
  }
</script>
