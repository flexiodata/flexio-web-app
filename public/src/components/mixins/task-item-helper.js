// common helpers for task items (requires that the calling object has
// an `item` computed property or provides their own `task` computed property)

import * as types from '../../constants/task-type'
import * as tasks from '../../constants/task-info'
import { VARIABLE_REGEX } from '../../constants/common'

export default {
  computed: {
    task() {
      return _.get(this, 'item', {})
    },
    task_icon() {
      return _.result(this, 'tinfo.icon', 'build')
    },
    task_bg_color() {
      return _.result(this, 'tinfo.bg_color', '')
    },
    ctype() {
      return _.get(this, 'task.metadata.connection_type', '')
    },
    is_input_task() {
      return _.get(this, 'task.type') == types.TASK_OP_INPUT
    },
    is_output_task() {
      return _.get(this, 'task.type') == types.TASK_OP_OUTPUT
    },
    show_connection_icon() {
      if (this.ctype.length == 0)
        return false

      switch (_.get(this, 'task.type'))
      {
        case types.TASK_OP_INPUT:
        case types.TASK_OP_OUTPUT:
          return true
      }

      return false
    },
    display_name() {
      var name = _.get(this, 'task.name', '')
      var default_name = _.result(this, 'tinfo.name', 'Untitled Step')
      return name.length > 0 ? name : default_name
    },
    bg_color() {
      if (this.task_bg_color.length > 0)
        return this.task_bg_color

      // default
      return 'bg-task-gray'
    },
    has_variables() {
      try {
        var str = JSON.stringify(this.task)
        return str.match(VARIABLE_REGEX) ? true : false
      } catch(e) {
        return false
      }
    }
  },
  methods: {
    tinfo() {
      return _.find(tasks, { type: _.get(this.task, 'type') })
    }
  }
}
