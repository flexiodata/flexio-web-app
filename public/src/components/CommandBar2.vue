<template>
  <div @click="focus">
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
      }
    },
    components: {
      Btn,
      Autocomplete
    },
    data() {
      return {
        cmd_text: ''
      }
    },
    watch: {
      origJson: function(val, old_val) {
        this.initFromTaskJson(val)
      }
    },
    computed: {
      orig_cmd() {
        var parser_json = parser.toCmdbar(this.origJson)
        return _.defaultTo(parser_json, '')
      },
      is_changed() {
        return this.cmd_text != this.orig_cmd
      },
      cmd_json() {
        return this.is_changed ? parser.toJSON(_.defaultTo(this.cmd_text), '') : this.origJson
      },
      task_type() {
        return _.get(this.cmd_json, 'type', '')
      }
    },
    mounted() {
      this.initFromTaskJson(this.origJson)
    },
    methods: {
      focus() {
        this.$refs['input'].focus()
      },

      initFromTaskJson(json) {
        var parser_json = parser.toCmdbar(json)
        var cmd_text = _.defaultTo(parser_json, '')
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

        var attrs = _.assign({}, this.cmd_json)
        attrs.eid = _.get(this.origJson, 'eid')
        this.$emit('change', val, attrs)
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
