<template>
  <div class="flex flex-row items-stretch relative">
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver fw6 ttu">Command</div>
      <command-bar
        class="input-reset border-box w-100 h-100 bn outline-0 m0 pa2 code resize-none"
        :val="cmd_text"
        @change="update"
        @revert="clear"
      ></command-bar>
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver fw6 ttu">Command to JSON</div>
      <pre
        class="flex-fill w-100 h-100 outline-0 ma0 ph2 pa2 f6 overflow-auto"
        spellcheck="false"
        style="white-space: pre-wrap"
      >{{cmd_json}}</pre>
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver fw6 ttu">JSON to Command</div>
      <div
        class="flex-fill w-100 h-100 outline-0 ma0 ph2 pa2 f6 code overflow-auto"
        spellcheck="false"
      >{{result_cmd}}</div>
    </div>
  </div>
</template>

<script>
  import parser from '../utils/parser'
  import CommandBar from './CommandBar.vue'

  export default {
    components: {
      CommandBar
    },
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
      },
      update(cmd) {
        this.cmd_text = cmd
      }
    }
  }
</script>
