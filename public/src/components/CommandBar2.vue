<template>
  <div>
    <div class="flex flex-row items-stretch relative bg-white">
      <div
        class="flex-fill flex flex-row items-stretch"
        @click="focus"
      >
        <autocomplete
          ref="input"
          class="flex-fill pv1 ph2 ba b--black-10"
          placeholder="Type a command..."
          input-class="db input-reset border-box outline-0 bn pv1 mh0 max-h3 f6 code w-100"
          :val="cmd_text"
          @change="onCommandChange"
          @revert="onCommandRevert"
          @save="onCommandSave"
        ></autocomplete>
      </div>
      <pre
        class="flex-fill border-box outline-0 ma0 pv1 ph1 max-h5 bl bt bb bw1 b--black-20 f6 overflow-auto"
        @click="focus"
        @keydown.esc="revertChanges"
        v-if="show_json"
      >{{cmd_json}}</pre>
      <btn
        btn-square
        class="ttu f6 b bt bb bl bw1 b--black-20 bg-black-10 black-60 pv2 ph2"
        :disabled="!isInserting && !is_changed"
        @click.stop="revertChanges"
        v-if="show_cancel_save_buttons && is_changed"
      >Cancel</btn>
      <btn
        btn-square
        btn-primary
        class="ttu f6 b pv2 ph3 ba bw1 br2 br--right"
        :disabled="!isInserting && !is_changed"
        @click.stop="saveChanges"
        v-if="show_cancel_save_buttons && is_changed"
      >Save</btn>
    </div>
  </div>
</template>

<script>
  import * as types from '../constants/task-type'
  import { HOSTNAME } from '../constants/common'
  import parser from '../utils/parser'
  import Btn from './Btn.vue'
  import Autocomplete from './Autocomplete.vue'

  export default {
    props: {
      'orig-json': {
        default: () => { return {} },
        type: Object
      },
      'task-json': {
        default: () => { return {} },
        type: Object
      },
      'show-cancel-save-buttons': {
        default: false,
        type: Boolean
      }
    },
    components: {
      Btn,
      Autocomplete
    },
    data() {
      return {
        show_json: false,
        show_plus_button: _.defaultTo(this.showPlusButton, true),
        show_cancel_save_buttons: _.defaultTo(this.showCancelSaveButtons, true),
        show_examples: _.defaultTo(this.showExamples, true),
        cmd_text: '',
        orig_cmd_text: ''
      }
    },
    watch: {
      taskJson: function(val, old_val) {
        this.initFromTaskJson(val)
      }
    },
    computed: {
      is_changed() {
        return this.cmd_text != this.orig_cmd_text
      },
      cmd_json() {
        return this.is_changed ? parser.toJSON(_.defaultTo(this.cmd_text), '') : this.origJson
      },
      task_type() {
        return _.get(this.cmd_json, 'type', '')
      }
    },
    mounted() {
      this.initFromTaskJson(this.taskJson)
    },
    methods: {
      focus() {
        this.$refs['input'].focus()
      },

      toggleExamples() {
        this.show_examples = !this.show_examples
      },

      toggleJson() {
        this.show_json = !this.show_json
      },

      initFromTaskJson(json) {
        var parser_json = parser.toCmdbar(json)
        var cmd_text = _.defaultTo(parser_json, '')

        this.orig_cmd_text = cmd_text
        this.setCmdText(cmd_text)
      },

      revertChanges() {
        this.initFromTaskJson(this.origJson)
        this.$emit('cancel')
      },

      saveChanges() {
        var attrs = _.assign({}, this.cmd_json)
        attrs.eid = _.get(this.origJson, 'eid')
        this.$emit('save', attrs)
      },

      setCmdText(cmd_text) {
        if (!_.isString(cmd_text))
          return

        var end_idx = cmd_text.indexOf(' code:')
        if (this.task_type == types.TASK_TYPE_EXECUTE && end_idx != -1)
          this.cmd_text = cmd_text.substring(0, end_idx)
           else
          this.cmd_text = cmd_text
      },

      onCommandChange(val) {
        this.setCmdText(val)
      },

      onCommandRevert(val) {
        this.revertChanges()
      },

      onCommandSave(val) {
        if (this.is_changed)
          this.saveChanges()
      }
    }
  }
</script>
