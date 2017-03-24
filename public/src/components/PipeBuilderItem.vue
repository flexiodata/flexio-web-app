<template>
  <div class="mh5 relative">
    <div class="flex flex-row absolute" style="top: -2rem" v-if="index == 0">
      <div class="pointer mr5 moon-gray hover-blue link tc hint--right" :aria-label="insert_before_tooltip">
        <i class="db material-icons f3">add_circle</i>
      </div>
    </div>

    <div class="flex flex-row mv2" style="margin-left: 6px">
      <div class="flex-none f4 lh-title mid-gray w2 mr1">{{index+1}}.</div>
      <div class="flex-fill">
        <div class="flex-none flex flex-row mb3">
          <div>
            <div
              class="cursor-default pa2 mr2 br1 white trans-wh tc"
              style="margin-top: 3px"
              :class="[ bg_color ]"
            >
              <i class="db material-icons f3">{{task_icon}}</i>
            </div>
          </div>
          <div>
            <textarea
              class="f4 lh-title mid-gray pa1 ba b--black-10"
              autocomplete="off"
              rows="1"
              @keydown.esc="editing_name = false"
              v-model="display_name"
              v-if="editing_name"
              v-focus
            ></textarea>
            <div class="hide-child mid-gray hover-black" v-else>
              <div class="dib f4 lh-title" @click="editing_name = true">{{display_name}}</div>
              <button
                class="pa0 br1 lh-title hint--top child"
                aria-label="Edit name and description"
                v-if="!editing_name && !editing_description"
              ><i class="db material-icons f6">edit</i>
            </div>
            </button>
            <textarea
              class="f6 fw6 lh-title bn"
              autocomplete="off"
              @keydown.esc="editing_description = false"
              v-model="description"
              v-if="editing_description"
              v-focus
            ></textarea>

            <div class="f6 fw6 hide-child mid-gray hover-black" v-else>
              <div class="dib" @click="editing_description = true" v-if="description.length > 0">{{description}}</div>
              <div class="dib black-20" @click="editing_description = true" v-else>Add a description</div>
              <button
                class="pa0 br1 lh-title hint--top child"
                aria-label="Edit name and description"
                v-if="!editing_name && !editing_description"
              ><i class="db material-icons f6">edit</i>
            </div>

            <div class="f6 fw6 lh-title ba b--transparent" @click="editDescription" v-else>
            </div>
          </div>
        </div>
        <div class="bl b--black-10 pl3" style="margin: -1.875rem 0 0 -1.875rem; padding: 1.875rem 0 0 1.875rem">
          <code-editor
            class="flex-fill pa1 ba b--black-10"
            lang="python"
            :val="command"
            :options="{ lineNumbers: false }"
          ></code-editor>
          <div class="flex-fill mt3" v-if="false">
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

  export default {
    props: ['item', 'index', 'active-stream-eid'],
    components: {
      CodeEditor
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
        return this.icon ? this.icon : _.result(this, 'tinfo.icon', 'build')
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
      }
    }
  }
</script>
