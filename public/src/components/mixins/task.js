// common helpers for task items (requires that the calling object has
// an `item` computed property or provides their own `task` computed property)

import * as ops from '../../constants/task-op'
import * as tasks from '../../constants/task-info'
import { VARIABLE_REGEX } from '../../constants/common'

export default {
  computed: {
    $_Task_item() {
      return _.get(this, 'item', {})
    },
    $_Task_icon() {
      return _.result(this, '$_Task_tinfo.icon', 'build')
    },
    $_Task_bg_color() {
      var color = _.result(this, '$_Task_tinfo.bg_color', '')

      if (color.length > 0) {
        return color
      }

      // default
      return 'bg-task-gray'
    },
    $_Task_display_name() {
      var name = _.get(this, '$_Task_item.name', '')
      var default_name = _.result(this, '$_Task_tinfo.name', 'Untitled Step')
      return name.length > 0 ? name : default_name
    }
  },
  methods: {
    $_Task_tinfo() {
      return _.find(tasks, { op: _.get(this.task, 'op') })
    }
  }
}
