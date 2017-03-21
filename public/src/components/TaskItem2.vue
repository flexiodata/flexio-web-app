<template>
  <div class="flex flex-row mt4 mb5 mh5">
    <div class="flex-none f4 lh-title mid-gray mr2">{{index+1}}.</div>
    <div class="flex-fill flex-l flex-row-l">
      <div class="flex-none flex flex-row mb3 mb0-l mr2-l">
        <div>
          <div
            class="cursor-default pa2 mr2 br1 white trans-wh tc"
            style="margin-top: 2px"
            :class="[ bg_color ]"
          >
            <i class="db material-icons f3">{{task_icon}}</i>
          </div>
        </div>
        <div class="dib relative hide-child" style="width: 240px">
          <input
            class="dib f4 lh-title mid-gray bn pa0"
            autocomplete="off"
            v-model="display_name"
            v-if="editing_name"
            v-focus
          ></input>
          <div class="dib f4 lh-title mid-gray" @click="editName" v-else>{{display_name}}</div>
          <button class="absolute top-0 right-0 pa1 br1 hover-bg-black-10 hint--bottom-left child" aria-label="Edit name and description">
            <i class="db material-icons md-18 mid-gray">edit</i>
          </button>
          <textarea
            class="f6 fw6 lh-title bn"
            autocomplete="off"
            v-model="description"
            v-if="editing_description"
            v-focus
          ></textarea>
          <div class="f6 fw6 lh-title ba b--transparent" @click="editDescription" v-else>
            <span v-if="description.length > 0">{{description}}</span>
            <span class="black-20" v-else>Add a description</span>
          </div>
        </div>
      </div>
      <code-editor
        class="flex-fill ba b--black-10 mb3 mb0-l mr4-l"
        lang="python"
        :val="command"
      ></code-editor>
      <div class="flex-fill">
        <div class="ba b--black-10 overflow-auto">
            <table class="f7 w-100 mw8 center" cellspacing="0">
              <thead>
                <tr>
                  <th class="fw6 bb b--black-10 tl pv1 ph2 bg-white">Name</th>
                  <th class="fw6 bb b--black-10 tl pv1 ph2 bg-white">Username</th>
                  <th class="fw6 bb b--black-10 tl pv1 ph2 bg-white">Email</th>
                  <th class="fw6 bb b--black-10 tl pv1 ph2 bg-white">ID</th>
                </tr>
              </thead>
              <tbody class="lh-copy">
                <tr>
                  <td class="pv1 ph2 bb b--black-10">Hassan Johnson</td>
                  <td class="pv1 ph2 bb b--black-10">@hassan</td>
                  <td class="pv1 ph2 bb b--black-10">hassan@companywithalongdomain.co</td>
                  <td class="pv1 ph2 bb b--black-10">14419232532474</td>
                </tr>
                <tr>
                  <td class="pv1 ph2 bb b--black-10">Taral Hicks</td>
                  <td class="pv1 ph2 bb b--black-10">@hicks</td>
                  <td class="pv1 ph2 bb b--black-10">taral@companywithalongdomain.co</td>
                  <td class="pv1 ph2 bb b--black-10">72326219423551</td>
                </tr>
                <tr>
                  <td class="pv1 ph2 bb b--black-10">Tyrin Turner</td>
                  <td class="pv1 ph2 bb b--black-10">@tt</td>
                  <td class="pv1 ph2 bb b--black-10">ty@companywithalongdomain.co</td>
                  <td class="pv1 ph2 bb b--black-10">92325170324444</td>
                </tr>
                <tr>
                  <td class="pv1 ph2 bb b--black-10">Oliver Grant</td>
                  <td class="pv1 ph2 bb b--black-10">@oli</td>
                  <td class="pv1 ph2 bb b--black-10">oliverg@companywithalongdomain.co</td>
                  <td class="pv1 ph2 bb b--black-10">71165170352909</td>
                </tr>
                <tr>
                  <td class="pv1 ph2 bb b--black-10">Dean Blanc</td>
                  <td class="pv1 ph2 bb b--black-10">@deanblanc</td>
                  <td class="pv1 ph2 bb b--black-10">dean@companywithalongdomain.co</td>
                  <td class="pv1 ph2 bb b--black-10">71865178111909</td>
                </tr>
              </tbody>
            </table>
          </div>
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
    props: ['item', 'index'],
    components: {
      CodeEditor
    },
    data() {
      return {
        command: this.getCommand(),
        editing_name: false,
        editing_description: false
      }
    },
    computed: {
      name() {
        return _.result(this, 'tinfo.name', 'New Task')
      },
      display_name() {
        var name = _.get(this.item, 'name', '')
        return name.length > 0 ? name : this.name
      },
      description() {
        return _.get(this.item, 'description', '')
      },
      task_icon() {
        return this.icon ? this.icon : _.result(this, 'tinfo.icon', 'build')
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
      editName() {
        this.editing_name = true
      },
      editDescription() {
        this.editing_description = true
      },
      getCommand() {
        return _.defaultTo(parser.toCmdbar(this.item), '')
      }
    }
  }
</script>
