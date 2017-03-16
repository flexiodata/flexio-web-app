<template>
  <ui-modal
    ref="dialog"
    remove-close-button
    dismiss-on="close-button"
    size="large"
    @hide="onHide"
  >
    <div slot="header" class="w-100">
      <div class="flex flex-row items-center">
        <div class="flex-fill">
          <span class="f4">{{title}}</span>
        </div>
        <div
          class="pointer f3 lh-solid b child black-30 hover-black-60"
          @click="close"
        >
          &times;
        </div>
      </div>
      <div v-if="show_file_chooser">
        <div class="flex flex-row mt1 mb3 pt2 bt b--black-10">
          <div class="flex-fill flex flex-row items-center">
            <connection-icon :type="ctype" class="dib v-top br2 fx-square-3"></connection-icon>
            <div class="ml2 mid-gray f5 fw6">{{cname}}</div>
          </div>
          <div class="mid-gray" v-if="mode != 'edit-pipe'">
            <i class="material-icons fw6 v-mid">chevron_left</i>
            <div
              class="dib f5 fw6 underline-hover pointer v-mid"
              title="Back to Step 1"
              @click="reset()"
            >
              Step 2 of 2
            </div>
          </div>
        </div>
        <file-explorer-bar
          class="fw4 f6 ba b--black-20 mb2 css-explorer-bar"
          :connection="connection"
          :path="connection_path"
          @open-folder="openFolder"
          v-if="file_chooser_mode == 'filechooser'"
        >
        </file-explorer-bar>
      </div>
    </div>

    <div v-if="show_file_chooser" class="relative">
      <file-chooser-list
        ref="filechooser"
        class="min-h5 max-h5"
        :connection="connection"
        :path="connection_path"
        :folders-only="mode == 'choose-output'"
        :allow-multiple="mode != 'choose-output'"
        @open-folder="openFolder"
        @selection-change="updateItems"
        v-if="file_chooser_mode == 'filechooser'"
      ></file-chooser-list>
      <url-input-list
        ref="urlinputlist"
        class="ba b--black-20 pa1 min-h5 max-h5"
        @selection-change="updateItems"
        v-if="file_chooser_mode == 'textentry'"
      ></url-input-list>
    </div>
    <div v-else>
      <form
        novalidate
        @submit.prevent="submit"
        v-if="mode != 'choose-input' && mode != 'choose-output'"
      >
        <div class="flex flex-row items-center">
          <div class="flex-fill mr4">
            <ui-textbox
              autocomplete="off"
              label="Name"
              floating-label
              help=" "
              required
              v-deferred-focus
              :error="errors.first('name')"
              :invalid="errors.has('name')"
              v-model="pipe.name"
              v-validate
              data-vv-name="name"
              data-vv-value-path="pipe.name"
              data-vv-rules="required"
            ></ui-textbox>
          </div>
          <div class="flex-fill">
            <ui-textbox
              autocomplete="off"
              label="Alias"
              help=" "
              :placeholder="alias_placeholder"
              :error="ename_error"
              :invalid="ename_error.length > 0"
              v-model="pipe.ename"
            ></ui-textbox>
          </div>
          <div
            class="hint--bottom-left hint--large cursor-default"
            aria-label="When using the Flex.io command line interface (CLI) or API, pipes may be referenced either via their object ID or via an alias created here. Aliases are unique across the app, so we recommend prefixing your username to the alias (e.g., username-foo)."
          >
            <i class="material-icons blue md-18">info</i>
          </div>
        </div>
        <ui-textbox
          label="Description"
          floating-label
          help=" "
          :multi-line="true"
          :rows="1"
          v-model="pipe.description"
        ></ui-textbox>
      </form>
      <div v-if="mode != 'edit-pipe'">
        <div
          class="fw6 mt4 mb1 mid-gray"
          v-if="mode == 'create-pipe' || mode == 'edit-pipe'"
        >Select Input</div>
        <div class="relative min-h4">
          <connection-chooser-list
            :project-eid="projectEid"
            :connection-type="ctype"
            :show-add="true"
            :mode="connection_list_mode"
            :show-blank-pipe="mode == 'create-pipe'"
            @add="addConnection"
            @item-activate="setConnection"
          >
          </connection-chooser-list>
        </div>
      </div>
    </div>

    <div slot="footer" class="w-100">
      <div class="flex-fill flex flex-column">
        <div class="flex-none flex flex-row items-center mh3 mb3" v-if="show_file_chooser && mode == 'choose-output'">
          <div class="flex-none f6 fw6 mr2">Output folder</div>
          <input
            type="text"
            class="flex-fll input-reset ba b--black-20 focus-b--transparent focus-outline focus-o--blue ph2 pv1 f6 w-100"
            placeholder="Output directory"
            v-model="connection_path"
          >
        </div>
        <div class="flex flex-row">
          <div class="flex-fill">&nbsp;</div>
          <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
          <btn btn-md class="b ttu blue" @click="submit()">{{submit_label}}</btn>
        </div>
      </div>
    </div>
  </ui-modal>
</template>

<script>
  import {
    CONNECTION_TYPE_BLANK_PIPE,
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS,
    CONNECTION_TYPE_MYSQL,
    CONNECTION_TYPE_POSTGRES
  } from '../constants/connection-type'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import * as connections from '../constants/connection-info'
  import { mapGetters } from 'vuex'
  import api from '../api'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import FileExplorerBar from './FileExplorerBar.vue'
  import FileChooserList from './FileChooserList.vue'
  import UrlInputList from './UrlInputList.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: 'New Pipe',
      ename: '',
      description: ''
    }
  }

  export default {
    props: ['open-from', 'close-to', 'project-eid'],
    components: {
      Btn,
      ConnectionIcon,
      ConnectionChooserList,
      FileExplorerBar,
      FileChooserList,
      UrlInputList
    },
    watch: {
      'pipe.ename': function(val, old_val) { this.validate() }
    },
    data() {
      return {
        mode: 'edit-pipe',
        connection: {},
        connection_path: '',
        items: [],
        ss_errors: {},
        pipe: _.extend({}, defaultAttrs()),
        original_pipe: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      has_eid() {
        return _.get(this.pipe, 'eid') !== null
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      ctype() {
        return _.get(this.connection, 'connection_type', CONNECTION_TYPE_BLANK_PIPE)
      },
      cname() {
        return _.get(this.connection, 'name', this.service_name)
      },
      has_connection() {
        return this.ctype.length > 0 && this.ctype != CONNECTION_TYPE_BLANK_PIPE
      },
      is_database_connection() {
        return _.includes([CONNECTION_TYPE_MYSQL, CONNECTION_TYPE_POSTGRES], this.ctype)
      },
      connection_list_mode() {
        return this.mode == 'choose-output' ? 'output' : 'input'
      },
      show_file_chooser() {
        if (!this.has_connection)
          return false

        if (this.connection_list_mode == 'output' && this.is_database_connection)
          return false

        return true
      },
      file_chooser_mode() {
        switch (this.ctype)
        {
          case CONNECTION_TYPE_HTTP: return 'textentry'
          case CONNECTION_TYPE_RSS:  return 'textentry'
        }

        return 'filechooser'
      },
      title() {
        return this.mode == 'choose-output'
          ? 'Output File Chooser' : this.mode == 'choose-input'
          ? 'Input File Chooser' : this.mode == 'edit-pipe'
          ? 'Edit "' + _.get(this.original_pipe, 'name') + '" Pipe'
          : 'New Pipe'
      },
      submit_label() {
        return this.mode == 'choose-output'
          ? 'Done' : this.mode == 'choose-input'
          ? 'Done' : this.mode == 'edit-pipe'
          ? 'Save changes'
          : 'Create pipe'
      },
      active_username() {
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      alias_placeholder() {
        return _.kebabCase('username-my-alias')
      },
      ename_error() {
        if (_.get(this.pipe, 'ename') === _.get(this.original_pipe, 'ename'))
          return ''

        return _.get(this.ss_errors, 'ename.message', '')
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      open(attrs) {
        this.reset(attrs)
        this.mode = _.get(attrs, 'mode', this.has_eid ? 'edit-pipe' : 'create-pipe'),
        this.$refs['dialog'].open()
        return this
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          // if we're showing the url input list and it's being edited, finish the edit
          var input_list = this.$refs['urlinputlist']
          if (input_list)
            input_list.finishEdit()

          var pipe = _.assign({}, this.pipe)

          if (this.has_connection)
          {
            var task = {
              type: this.mode == 'choose-output' ? TASK_TYPE_OUTPUT : TASK_TYPE_INPUT
            }

            if (task.type == TASK_TYPE_INPUT)
            {
              task.params = {
                items: _.map(this.items, (f) => { return _.pick(f, ['path']) })
              }
            }
             else if (task.type == TASK_TYPE_OUTPUT)
            {
              if (!this.is_database_connection)
                _.set(task, 'params.location', this.connection_path)
            }

            pipe.task = [task]

            var identifier = _.get(this.connection, 'ename', '')
            identifier = identifier.length > 0 ? identifier : _.get(this.connection, 'eid', '')

            // set connection identifier (if it exists)
            if (identifier.length > 0)
              _.set(pipe, 'task[0].params.connection', identifier)

            // set task metadata
            _.set(pipe, 'task[0].metadata.connection_type', _.get(this.connection, 'connection_type', ''))
          }

          this.$nextTick(() => { this.$emit('submit', _.omit(pipe, ['mode']), this) })
        })
      },
      reset(attrs) {
        this.ss_errors = {}
        this.connection = {}
        this.connection_path = ''
        this.pipe = _.extend({}, defaultAttrs(), attrs)
        this.original_pipe = _.extend({}, defaultAttrs(), attrs)
      },
      onHide() {
        this.reset()
      },
      validate: _.debounce(function(validate_key) {
        var validate_attrs = [{
          key: 'ename',
          value: _.get(this.pipe, 'ename', ''),
          type: 'ename'
        }]

        api.validate({ attrs: validate_attrs }).then((response) => {
          var errors = _.keyBy(response.body, 'key')
          this.ss_errors = _.get(this.pipe, 'ename', '').length > 0 && _.size(errors) > 0 ? _.assign({}, errors) : _.assign({})
        }, (response) => {
          // error callback
        })
      }, 300),
      addConnection() {
        this.$emit('add-connection')
      },
      setConnection(item) {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          this.connection = _.assign({}, item)
          this.connection_path = '/'
          this.items = []
        })
      },
      openFolder(path) {
        this.connection_path = _.defaultTo(path, '/')
        this.items = []
      },
      updateItems(items) {
        this.items = items
      }
    }
  }
</script>

<style scoped>
  .css-explorer-bar {
    padding: 0.25rem 0.375rem;
  }
</style>
