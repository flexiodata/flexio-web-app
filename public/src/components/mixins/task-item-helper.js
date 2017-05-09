// common helpers for task items (requires that the calling object has
// an `item` computed property or provides their own `task` computed property)

import * as types from '../../constants/task-type'
import * as tasks from '../../constants/task-info'

export default {
  computed: {
    task() {
      return _.get(this, 'item', {})
    },
    task_icon() {
      return _.result(this, 'tinfo.icon', 'build')
    },
    task_icon() {
      return _.result(this, 'tinfo.icon', 'build')
    },
    ctype() {
      return _.get(this, 'task.metadata.connection_type', '')
    },
    is_input_task() {
      return _.get(this, 'task.type') == types.TASK_TYPE_INPUT
    },
    is_output_task() {
      return _.get(this, 'task.type') == types.TASK_TYPE_OUTPUT
    },
    show_connection_icon() {
      if (this.ctype.length == 0)
        return false

      switch (_.get(this, 'task.type'))
      {
        case types.TASK_TYPE_INPUT:
        case types.TASK_TYPE_OUTPUT:
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
      switch (_.get(this, 'task.type'))
      {
        // blue tiles
        case types.TASK_TYPE_INPUT:
        case types.TASK_TYPE_COMMENT:
        case types.TASK_TYPE_CONVERT:
        case types.TASK_TYPE_EMAIL_SEND:
        case types.TASK_TYPE_OUTPUT:
        case types.TASK_TYPE_PROMPT:
          return 'bg-task-blue'

        case types.TASK_TYPE_EXECUTE:
          return 'bg-task-purple'

        // green tiles
        case types.TASK_TYPE_CALC:
        case types.TASK_TYPE_DISTINCT:
        case types.TASK_TYPE_DUPLICATE:
        case types.TASK_TYPE_FILTER:
        case types.TASK_TYPE_GROUP:
        case types.TASK_TYPE_LIMIT:
        case types.TASK_TYPE_MERGE:
        case types.TASK_TYPE_SEARCH:
        case types.TASK_TYPE_SORT:
          return 'bg-task-green'

        // orange tiles
        case types.TASK_TYPE_COPY:
        case types.TASK_TYPE_CUSTOM:
        case types.TASK_TYPE_FIND_REPLACE:
        case types.TASK_TYPE_NOP:
        case types.TASK_TYPE_RENAME:
        case types.TASK_TYPE_SELECT:
        case types.TASK_TYPE_TRANSFORM:
          return 'bg-task-orange'
      }

      // default
      return 'bg-task-gray'
    }
  },
  methods: {
    tinfo() {
      return _.find(tasks, { type: _.get(this.task, 'type') })
    }
  }
}
