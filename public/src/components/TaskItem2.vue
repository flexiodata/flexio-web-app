<template>
  <div class="mh5 relative">
    <div class="flex flex-row absolute" style="top: -2rem; left: -6px" v-if="index == 0">
      <div class="cursor-default mr5 moon-gray hover-blue tc hint--right" :aria-label="insert_before_tooltip">
        <i class="db material-icons f3">add_circle</i>
      </div>
    </div>

    <div class="flex flex-row mv2">
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
            class="flex-fill pa1 ba b--black-10 mb3"
            lang="python"
            :val="command"
            :options="{ lineNumbers: false }"
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
    </div>

    <div class="flex flex-row relative" style="left: -6px">
      <div class="cursor-default mr5 moon-gray hover-blue tc hint--right" :aria-label="insert_after_tooltip">
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
        return 'Insert a new task before task ' + (this.index+1)
      },
      insert_after_tooltip() {
        return 'Insert a new task after task ' + (this.index+1)
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
        console.log(this.item)
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
