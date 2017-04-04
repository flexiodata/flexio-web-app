<template>
  <div>
    <div class="flex flex-row items-stretch relative">
      <div class="flex-none flex flex-row items-stretch">
        <a
          class="flex flex-row items-center fw6 f6 pv1 ph2 ba bw1 b--black-10 bg-black-10 black-60 br2 br--left pointer h-100 no-underline"
          ref="dropdownTrigger"
          tabindex="0"
        ><i class="material-icons v-mid">add</i></a>

        <ui-popover
          trigger="dropdownTrigger"
          ref="dropdown"
        >
          <ui-menu
            class="mw-none"
            contain-focus
            has-icons

            :options="[{
              id: 'input-file-chooser',
              label: 'Open input file chooser',
              icon: 'input'
            },{
              id: 'output-file-chooser',
              label: 'Open output file chooser',
              icon: 'input'
            },{
              type: 'divider'
            },{
              id: 'toggle-file-list',
              label: 'Toggle file list',
              icon: 'list'
            }]"

            @select="onDropdownItemClick"
            @close="$refs.dropdown.close()"
          ></ui-menu>
        </ui-popover>
      </div>
      <div
        class="flex-fill flex flex-row items-stretch"
        @click="focus"
      >
        <autocomplete
          ref="input"
          class="flex-fill pv1 ph1 bt bb bw1 b--black-20"
          placeholder="Type a command..."
          input-class="db input-reset border-box outline-0 bn pv1 mh0 max-h3 f6 code w-100"
          :val="cmd_text"
          :connections="connections"
          :columns="columns"
          @change="onCommandChange"
          @revert="onCommandRevert"
          @save="onCommandSave"
        ></autocomplete>
      </div>
      <pre
        class="flex-fill border-box outline-0 ma0 pv1 ph1 max-h5 bl bt bb bw1 b--black-20 f6 overflow-auto"
        @click="focus"
        @keydown.esc="revertChanges"
        v-if="show_json"
      >{{cmd_json}}</pre>
      <btn
        btn-square
        class="ttu f6 b bt bb bl bw1 b--black-20 bg-black-10 black-60 pv2 ph2"
        :disabled="!isInserting && !is_changed"
        @click.stop="revertChanges"
      >Cancel</btn>
      <btn
        btn-square
        btn-primary
        class="ttu f6 b pv2 ph3 ba bw1 br2 br--right"
        :disabled="!isInserting && !is_changed"
        @click.stop="saveChanges"
      >Save</btn>
    </div>
    <div v-if="show_examples" class="black-50 f7 mt1 ml1">
      <span class="code">Example:</span>
      <span class="code">{{code_example}}</span>
      <a
        target="_blank"
        rel="noopener noreferrer"
        class="ml2 pointer hint--top blue no-underline underline-hover"
        aria-label="Visit help docs"
        :href="code_more_link"
      >More...</a>
    </div>
  </div>
</template>

<script>
  import * as types from '../constants/task-type'
  import { HOSTNAME } from '../constants/common'
  import parser from '../utils/parser'
  import Btn from './Btn.vue'
  import Autocomplete from './Autocomplete.vue'

  export default {
    props: ['is-inserting', 'orig-json', 'task-json', 'connections', 'input-columns', 'output-columns'],
    components: {
      Btn,
      Autocomplete
    },
    data() {
      return {
        show_json: false,
        show_examples: true,
        cmd_text: '',
        orig_cmd_text: ''
      }
    },
    watch: {
      taskJson: function(val, old_val) {
        this.initFromTaskJson(val)
      }
    },
    computed: {
      is_changed() {
        return this.isInserting
         || this.cmd_text != this.orig_cmd_text
         || !_.isEqual(this.taskJson, this.origJson)
      },
      cmd_json() {
        return this.is_changed
          ? parser.toJSON(_.defaultTo(this.cmd_text), '') : this.isInserting
          ? {} : this.origJson
      },
      task_type() {
        return _.get(this.cmd_json, 'type', '')
      },
      columns() {
        return this.isInserting ? this.outputColumns : this.inputColumns
      },
      code_example() {
        switch (this.task_type)
        {
          default                            : return 'input from: my-connection file: /path/to/file1, /path/to/file2'
          case types.TASK_TYPE_CALC          : return 'calc value: curdate() as: myfld type: date decimals: 0'
          case types.TASK_TYPE_CONVERT       : return 'convert from: delimited to: table'
          case types.TASK_TYPE_COPY          : return ''
          case types.TASK_TYPE_CUSTOM        : return ''
          case types.TASK_TYPE_DISTINCT      : return ''
          case types.TASK_TYPE_DUPLICATE     : return ''
          case types.TASK_TYPE_EMAIL_SEND    : return ''
          case types.TASK_TYPE_EXECUTE       : return 'execute lang: python code: <use code editor>'
          case types.TASK_TYPE_FIND_REPLACE  : return ''
          case types.TASK_TYPE_FILTER        : return 'filter where: gross_amt > 100'
          case types.TASK_TYPE_GROUP         : return ''
          case types.TASK_TYPE_INPUT         : return 'input from: my-connection file: /path/to/file1, /path/to/file2'
          case types.TASK_TYPE_LIMIT         : return 'limit sample: top value: 50054'
          case types.TASK_TYPE_MERGE         : return 'merge'
          case types.TASK_TYPE_NOP           : return ''
          case types.TASK_TYPE_OUTPUT        : return 'output to: my-connection location: /path/to/folder'
          case types.TASK_TYPE_PROMPT        : return ''
          case types.TASK_TYPE_R             : return ''
          case types.TASK_TYPE_RENAME        : return ''
          case types.TASK_TYPE_RENAME_COLUMN : return 'rename col: first_name => firstname, last_name => lastname'
          case types.TASK_TYPE_SEARCH        : return ''
          case types.TASK_TYPE_SELECT        : return 'select col: [vendor name], trans_date, gross_amt'
          case types.TASK_TYPE_SORT          : return 'sort col: [vendor name]'
          case types.TASK_TYPE_TRANSFORM     : return 'transform col: vend_no, [vendor name] delimiter: ',' case: lower trim: leading'
        }
      },
      code_more_link() {
        var base_url = 'https://'+HOSTNAME+'/docs/web-app/'

        switch (this.task_type)
        {
          default                            : return base_url
          case types.TASK_TYPE_CALC          : return base_url+'#calc'
          case types.TASK_TYPE_CONVERT       : return base_url+'#convert'
          case types.TASK_TYPE_COPY          : return base_url
          case types.TASK_TYPE_CUSTOM        : return base_url
          case types.TASK_TYPE_DISTINCT      : return base_url
          case types.TASK_TYPE_DUPLICATE     : return base_url
          case types.TASK_TYPE_EMAIL_SEND    : return base_url
          case types.TASK_TYPE_EXECUTE       : return base_url+'#execute'
          case types.TASK_TYPE_FIND_REPLACE  : return base_url
          case types.TASK_TYPE_FILTER        : return base_url+'#filter'
          case types.TASK_TYPE_GROUP         : return base_url
          case types.TASK_TYPE_INPUT         : return base_url+'#input'
          case types.TASK_TYPE_LIMIT         : return base_url+'#limit'
          case types.TASK_TYPE_MERGE         : return base_url+'#merge'
          case types.TASK_TYPE_NOP           : return base_url
          case types.TASK_TYPE_OUTPUT        : return base_url+'#output'
          case types.TASK_TYPE_PROMPT        : return base_url
          case types.TASK_TYPE_R             : return base_url
          case types.TASK_TYPE_RENAME        : return base_url+'#rename'
          case types.TASK_TYPE_RENAME_COLUMN : return base_url+'#rename'
          case types.TASK_TYPE_SEARCH        : return base_url
          case types.TASK_TYPE_SELECT        : return base_url+'#select'
          case types.TASK_TYPE_SORT          : return base_url+'#sort'
          case types.TASK_TYPE_TRANSFORM     : return base_url+'#transform'
        }
      }
    },
    mounted() {
      this.initFromTaskJson(this.taskJson)
    },
    methods: {
      focus() {
        this.$refs['input'].focus()
      },

      toggleExamples() {
        this.show_examples = !this.show_examples
      },

      toggleJson() {
        this.show_json = !this.show_json
      },

      openHelpDocs() {
        window.open('https://docs.flex.io', '_blank')
      },

      initFromTaskJson(json) {
        var parser_json = parser.toCmdbar(json)
        var cmd_text = _.defaultTo(parser_json, '')

        // insert a space at the end of the command
        if (cmd_text.length > 0 && cmd_text.lastIndexOf(' ') != cmd_text.length-1)
          cmd_text += ' '

        this.orig_cmd_text = cmd_text
        this.cmd_text = cmd_text
      },

      revertChanges() {
        this.$emit('cancel')
      },

      saveChanges() {
        var attrs = _.assign({}, this.cmd_json)

        if (!this.isInserting)
          attrs.eid = _.get(this.origJson, 'eid')

        this.$emit('save', attrs)
      },

      onCommandChange(val) {
        this.cmd_text = val
      },

      onCommandRevert(val) {
        this.revertChanges()
      },

      onCommandSave(val) {
        if (this.is_changed)
          this.saveChanges()
      },

      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'input-file-chooser':  return this.$emit('show-input-file-chooser')
          case 'output-file-chooser': return this.$emit('show-output-file-chooser')
          case 'toggle-file-list':    return this.$emit('toggle-file-list')
          case 'docs':                return this.openHelpDocs()
          case 'examples':            return this.toggleExamples()
        }
      }
    }
  }
</script>
