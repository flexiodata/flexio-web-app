<template>
  <article>
    <div class="f7 lh-title mb2">{{title}}</div>
    <ui-progress-linear
      :type="type"
      :progress="pct"
      v-show="is_running || is_completed"
    ></ui-progress-linear>
  </article>
</template>

<script>
  import * as types from '../constants/task-type'
  import * as tasks from '../constants/task-info'
  import * as process from '../constants/process'

  export default {
    props: ['item'],
    computed: {
      task_action_str() {
        switch (_.get(this.item, 'task_type', ''))
        {
          default                            : return 'Initializing'
          case types.TASK_TYPE_CALC          : return 'Adding calculated columns'
          case types.TASK_TYPE_CONVERT       : return 'Converting files'
          case types.TASK_TYPE_COPY          : return 'Copying files'
          case types.TASK_TYPE_CUSTOM        : return 'Processing'
          case types.TASK_TYPE_DISTINCT      : return 'Removing duplicates'
          case types.TASK_TYPE_DUPLICATE     : return 'Identifying duplicates'
          case types.TASK_TYPE_EMAIL_SEND    : return 'Sending emails'
          case types.TASK_TYPE_EXECUTE       : return 'Executing code'
          case types.TASK_TYPE_FIND_REPLACE  : return 'Finding and replacing values'
          case types.TASK_TYPE_FILTER        : return 'Filtering values'
          case types.TASK_TYPE_GROUP         : return 'Grouping rows'
          case types.TASK_TYPE_INPUT         : return 'Importing files'
          case types.TASK_TYPE_LIMIT         : return 'Limiting rows'
          case types.TASK_TYPE_MERGE         : return 'Merging files'
          case types.TASK_TYPE_NOP           : return 'Processing'
          case types.TASK_TYPE_OUTPUT        : return 'Exporting files'
          case types.TASK_TYPE_PROMPT        : return 'Gathering information'
          case types.TASK_TYPE_R             : return 'Running "R" script'
          case types.TASK_TYPE_RENAME        : return 'Renaming files'
          case types.TASK_TYPE_RENAME_COLUMN : return 'Renaming columns'
          case types.TASK_TYPE_SEARCH        : return 'Searching for values'
          case types.TASK_TYPE_SELECT        : return 'Selecting columns'
          case types.TASK_TYPE_SORT          : return 'Sorting rows'
          case types.TASK_TYPE_TRANSFORM     : return 'Transforming values'
        }
      },
      title() {
        var s = this.task_action_str

        switch (_.get(this.item, 'process_status'))
        {
          case process.PROCESS_STATUS_PENDING:   return s + '... ' + 'Waiting'
          case process.PROCESS_STATUS_WAITING:   return s + '... ' + 'Waiting'
          case process.PROCESS_STATUS_RUNNING:   return s + '... ' + 'Running'
          case process.PROCESS_STATUS_CANCELLED: return s + '... ' + 'Canceled'
          case process.PROCESS_STATUS_PAUSED:    return s + '... ' + 'Paused'
          case process.PROCESS_STATUS_FAILED:    return s + '... ' + 'Failed'
          case process.PROCESS_STATUS_COMPLETED: return s + '... ' + 'Completed'
        }

        return s
      },
      is_running() {
        return _.get(this.item, 'process_status') == process.PROCESS_STATUS_RUNNING
      },
      is_completed() {
        return _.get(this.item, 'process_status') == process.PROCESS_STATUS_COMPLETED
      },
      type() {
        return this.is_running ? 'indeterminate' : 'determinate'
      },
      pct() {
        return this.is_completed ? 100 : 0
      }
    }
  }
</script>
