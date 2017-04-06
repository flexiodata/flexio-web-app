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
      'orig-json': {
        default: () => { return {} },
        type: Object
      }
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
      },
      is_task_execute() {
        return this.task_type == TASK_TYPE_EXECUTE
      },
    },
    mounted() {
      var opts = {
        lineNumbers: false,
        lineWrapping: true,
        mode: 'flexio-commandbar'
      }

      this.initFromTaskJson(this.origJson)

      this.$nextTick(() => {
        this.editor = CodeMirror.fromTextArea(this.$refs['textarea'], opts)

        this.editor.on('change', (cm) => {
          var new_text = cm.getValue()
          if (new_text == this.cmd_text)
            return

          this.setCmdText(new_text)

          var attrs = _.assign({}, this.cmd_json)
          attrs.eid = _.get(this.origJson, 'eid')
          this.$emit('change', this.cmd_text, attrs)
        })
      })
    },
    methods: {
      focus() {
        this.$refs['textarea'].focus()
      },

      initFromTaskJson(json) {
        var parser_json = parser.toCmdbar(json)
        var cmd_text = _.defaultTo(parser_json, '')
        this.setCmdText(cmd_text)
      },

      reset() {
        this.initFromTaskJson(this.origJson)
        this.editor.setValue(this.cmd_text)
      },

      revertChanges() {
        this.reset()
        this.$emit('cancel')
      },

      saveChanges() {
        if (this.is_changed)
        {
          var attrs = _.assign({}, this.cmd_json)
          attrs.eid = _.get(this.origJson, 'eid')
          this.$emit('save', attrs)
        }
      },

      setCmdText(cmd_text) {
        if (!_.isString(cmd_text))
          return

        var end_idx = cmd_text.indexOf(' code:')
        this.cmd_text = (this.is_task_execute && end_idx != -1)
          ? cmd_text.substring(0, end_idx)
          : cmd_text
      }
    }
  }
</script>
