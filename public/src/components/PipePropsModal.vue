<template>
  <ui-modal
    ref="dialog"
    dismiss-on="close-button"
    @open="onOpen"
    @hide="onHide"
  >
    <div slot="header" class="w-100">
      <span class="f4">{{title}}</span>
    </div>

    <div>
      <form
        novalidate
        @submit.prevent="submit"
      >
        <div class="flex flex-column flex-row-ns items-center-ns">
          <div class="flex-fill mr4-ns">
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
          <div class="flex-fill flex flex-row items-center">
            <ui-textbox
              class="flex-fill"
              autocomplete="off"
              label="Alias"
              help=" "
              :placeholder="alias_placeholder"
              :error="ename_error"
              :invalid="ename_error.length > 0"
              v-model="pipe.ename"
            ></ui-textbox>
            <div
              class="hint--bottom-left hint--large cursor-default"
              aria-label="When using the Flex.io command line interface (CLI) or API, pipes may be referenced either via their object ID or via an alias created here. Aliases are unique across the app, so we recommend prefixing your username to the alias (e.g., username-foo)."
            >
              <i class="material-icons blue md-18">info</i>
            </div>
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

      <ui-collapsible class="mt4 ui-collapsible--sm" title="Permissions" open disable-ripple v-if="show_permissions">
        <rights-list :object="pipe" @change="onRightsChanged"></rights-list>
      </ui-collapsible>
    </div>

    <div slot="footer" class="flex flex-row w-100">
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="b ttu blue" @click="submit()">{{submit_label}}</btn>
    </div>
  </ui-modal>
</template>

<script>
  import { mapGetters } from 'vuex'
  import {
    CONNECTION_TYPE_BLANK_PIPE,
    CONNECTION_TYPE_HTTP,
    CONNECTION_TYPE_RSS,
    CONNECTION_TYPE_MYSQL,
    CONNECTION_TYPE_POSTGRES
  } from '../constants/connection-type'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import ConnectionIcon from './ConnectionIcon.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import FileExplorerBar from './FileExplorerBar.vue'
  import FileChooserList from './FileChooserList.vue'
  import UrlInputList from './UrlInputList.vue'
  import RightsList from './RightsList.vue'
  import Validation from './mixins/validation'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: 'New Pipe',
      ename: '',
      description: ''
    }
  }

  export default {
    props: ['project-eid'],
    mixins: [Validation],
    components: {
      Btn,
      ConnectionIcon,
      ConnectionChooserList,
      FileExplorerBar,
      FileChooserList,
      UrlInputList,
      RightsList
    },
    watch: {
      'pipe.ename': function(val, old_val) {
        var ename = val

        this.validateEname(val, (response, errors) => {
          this.ss_errors = ename.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})
        })
      }
    },
    data() {
      return {
        mode: 'edit-pipe',
        show_permissions: false,
        ss_errors: {},
        pipe: _.extend({}, defaultAttrs()),
        original_pipe: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      has_eid() {
        return _.get(this.pipe, 'eid') !== null
      },
      pipe_name() {
        return _.get(this.original_pipe, 'name', '')
      },
      title() {
        return this.mode == 'edit-pipe'
          ? 'Edit "' + this.pipe_name + '" Pipe'
          : 'New Pipe'
      },
      submit_label() {
        return this.mode == 'edit-pipe'
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

          var ename = _.get(this.pipe, 'ename', '')

          this.validateEname(ename, (response, errors) => {
            this.ss_errors = ename.length > 0 && _.size(errors) > 0
              ? _.assign({}, errors)
              : _.assign({})

            if (this.ename_error.length == 0)
              this.$nextTick(() => { this.$emit('submit', this.pipe, this) })
          })
        })
      },
      reset(attrs) {
        this.ss_errors = {}
        this.pipe = _.extend({}, defaultAttrs(), attrs)
        this.original_pipe = _.extend({}, defaultAttrs(), attrs)
      },
      onOpen() {
        this.$nextTick(() => { this.show_permissions = true })
      },
      onHide() {
        this.reset()
        this.show_permissions = false
      },
      onRightsChanged(rights) {
        this.pipe = _.assign({}, this.pipe, { rights })
      }
    }
  }
</script>
