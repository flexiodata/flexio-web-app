<template>
  <div class="flex flex-row mv4 mh5">
    <div>
      <div class="cursor-default pa2 mr2 br1 white trans-wh tc" :class="[ bg_color ]">
        <i class="db material-icons f3">{{task_icon}}</i>
      </div>
    </div>
    <div>
      <input
        class="f4 lh-title"
        autocomplete="off"
        v-model="task_name"
        v-if="editing_name"
        v-focus
      ></input>
      <div class="f4 lh-title ba b--transparent" @click="editName" v-else>{{task_name}}</div>
      <textarea
        class="f6 fw6 lh-title bn"
        autocomplete="off"
        v-model="task_description"
        v-if="editing_description"
        v-focus
      ></textarea>
      <div class="f6 fw6 lh-title ba b--transparent" @click="editDescription" v-else>
        <span v-if="task_description.length > 0">{{task_description}}</span>
        <span class="black-30" v-else>Add a description</span>
      </div>
    </div>
  </div>
</template>

<script>
  import * as types from '../constants/task-type'
  import * as tasks from '../constants/task-info'

  export default {
    props: {
      'item': {}
    },
    data() {
      return {
        editing_name: false,
        editing_description: false
      }
    },
    computed: {
      task_name() {
        var name = _.get(this.item, 'name', '')

        return this.name
          ? this.name : name.length > 0
          ? name : _.result(this, 'tinfo.name', 'New Task')
      },
      task_description() {
        return _.get(this.item, 'description', '')
      },
      task_icon() {
        return this.icon ? this.icon : _.result(this, 'tinfo.icon', 'build')
      },
      bg_color() {
        if (this.isGhost)
          return 'bg-white'

        if (this.isTaskAdd)
          return 'bg-black-20'

        switch (_.get(this.item, 'type')) {
          // blue tiles
          case types.TASK_TYPE_INPUT:
          case types.TASK_TYPE_CONVERT:
          case types.TASK_TYPE_EMAIL_SEND:
          case types.TASK_TYPE_OUTPUT:
          case types.TASK_TYPE_PROMPT:
          case types.TASK_TYPE_RENAME:
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
          case types.TASK_TYPE_RENAME_COLUMN:
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
        return _.find(tasks, { type: _.get(this.item, 'type') })
      },
      editName() {
        this.editing_name = true
      },
      editDescription() {
        this.editing_description = true
      }
    }
  }
</script>
