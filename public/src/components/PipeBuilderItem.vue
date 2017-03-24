<template>
  <div class="mh5 relative">
    <div class="flex flex-row absolute" style="top: -2rem" v-if="index == 0 && false">
      <div class="pointer mr5 moon-gray hover-blue link tc hint--right" :aria-label="insert_before_tooltip">
        <i class="db material-icons f3">add_circle</i>
      </div>
    </div>

    <div class="flex flex-row mv2" style="margin-left: 6px">
      <div class="flex-none f4 lh-title mid-gray w2 mr1">{{index+1}}.</div>
      <div class="flex-fill">
        <div class="flex-none flex flex-row mb2">
          <div
            class="cursor-default pa2 mr2 br1 white trans-wh tc"
            style="margin-top: 2px"
            :class="[ bg_color ]"
          >
            <i class="db material-icons f3">{{task_icon}}</i>
          </div>
          <div>
            <inline-edit-text
              class="f5 lh-copy"
              input-key="name"
              :val="display_name"
              @save="editTaskSingleton">
            </inline-edit-text>
            <inline-edit-text
              class="f7 lh-title gray"
              placeholder="Add a description"
              placeholder-cls="fw6 black-20 hover-black-40"
              input-key="description"
              :val="description"
              @save="editTaskSingleton">
            </inline-edit-text>
          </div>
        </div>
        <div class="bl b--black-10 pl3" style="margin: -1.375rem 0 0 -1.875rem; padding: 1.375rem 0 0 1.875rem">
          <div class="pb2">
            <code-editor
              class="pa1 ba b--black-10"
              lang="python"
              :val="command"
              :options="{ lineNumbers: false }"
            ></code-editor>
          </div>
          <div v-if="false">
            &nbsp;
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-row">
      <div class="pointer mr5 moon-gray hover-blue link tc hint--right" :aria-label="insert_after_tooltip">
        <i class="db material-icons f3">add_circle</i>
      </div>
    </div>
  </div>
</template>

<script>
  import * as types from '../constants/task-type'
  import * as tasks from '../constants/task-info'
  import parser from '../utils/parser'
  import CodeEditor from './CodeEditor.vue'
  import InlineEditText from './InlineEditText.vue'

  export default {
    props: ['pipe-eid', 'item', 'index', 'active-stream-eid'],
    components: {
      CodeEditor,
      InlineEditText
    },
    data() {
      return {
        display_name: this.getDisplayName(),
        description: this.getDescription(),
        command: this.getCommand(),
        editing_name: false,
        editing_description: false
      }
    },
    computed: {
      task_icon() {
        return _.result(this, 'tinfo.icon', 'build')
      },
      insert_before_tooltip() {
        return 'Insert a new step before step ' + (this.index+1)
      },
      insert_after_tooltip() {
        return 'Insert a new step after step ' + (this.index+1)
      },
      bg_color() {
        switch (_.get(this.item, 'type'))
        {
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
      getDefaultName() {
        return _.result(this, 'tinfo.name', 'New Task')
      },
      getDisplayName() {
        var name = _.get(this.item, 'name', '')
        return name.length > 0 ? name : this.getDefaultName()
      },
      getDescription() {
        return _.get(this.item, 'description', '')
      },
      getCommand() {
        return _.defaultTo(parser.toCmdbar(this.item), '')
      },
      editTaskSingleton(attrs, input, task_attrs) {
        var eid = this.pipeEid
        var task_eid = _.get(this.item, 'eid', '')
        var attrs = _.assign({}, this.item, attrs)
        this.$store.dispatch('updatePipeTask', { eid, task_eid, attrs })
        input.endEdit()
      }
    }
  }
</script>
