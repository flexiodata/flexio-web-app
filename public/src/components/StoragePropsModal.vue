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
      <div class="flex flex-row mt2 pt2 bt b--black-10" v-if="has_connection">
        <service-icon :type="ctype" class="flex-none dib v-top br2 square-4" style="max-height: 3rem"></service-icon>
        <div class="flex-fill flex flex-column ml2">
          <div class="mid-gray f4 fw6">{{service_name}}</div>
          <div class="mid-gray f6 fw4 mt1">{{service_description}}</div>
        </div>
        <div class="mid-gray" v-if="showSteps && mode != 'edit'">
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
    </div>

    <div v-if="is_open">
      <service-list v-show="!has_connection"
        filter-items="storage"
        @item-activate="createPendingConnection"
      ></service-list>
      <div v-if="has_connection">
        <form novalidate @submit.prevent="submit">
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
                v-model="connection.name"
                v-validate
                data-vv-name="name"
                data-vv-value-path="connection.name"
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
                v-model="connection.ename"
              ></ui-textbox>
            </div>
            <div
              class="hint--bottom-left hint--large cursor-default"
              aria-label="Connections can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API. Aliases are unique across the app, so we recommend prefixing your alias with your username (e.g., username-foo)."
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
            v-model="connection.description"
          ></ui-textbox>
        </form>

        <ui-collapsible class="ui-collapsible--sm" style="margin: 1.5rem 0 0 0" title="Authentication" open disable-ripple>
          <connection-configure-panel
            :connection="connection"
            :mode="mode"
            @change="updateConnection"
          ></connection-configure-panel>
        </ui-collapsible>
      </div>
    </div>

    <div slot="footer" class="flex flex-row items-end" v-show="has_connection">
      <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="b ttu blue" @click="submit()">{{submit_label}}</btn>
    </div>
  </ui-modal>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import * as mtypes from '../constants/member-type'
  import * as atypes from '../constants/action-type'
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import api from '../api'
  import Btn from './Btn.vue'
  import ServiceList from './ServiceList.vue'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionConfigurePanel from './ConnectionConfigurePanel.vue'
  import RightsList from './RightsList.vue'
  import Validation from './mixins/validation'

  const defaultRights = () => {
    return {
      [mtypes.MEMBER_TYPE_OWNER]: {
        [atypes.ACTION_TYPE_READ]: true,
        [atypes.ACTION_TYPE_WRITE]: true,
        [atypes.ACTION_TYPE_EXECUTE]: true,
        [atypes.ACTION_TYPE_DELETE]: true
      },
      [mtypes.MEMBER_TYPE_MEMBER]: {
        [atypes.ACTION_TYPE_READ]: true,
        [atypes.ACTION_TYPE_WRITE]: true,
        [atypes.ACTION_TYPE_EXECUTE]: true,
        [atypes.ACTION_TYPE_DELETE]: false
      },
      [mtypes.MEMBER_TYPE_PUBLIC]: {
        [atypes.ACTION_TYPE_READ]: false,
        [atypes.ACTION_TYPE_WRITE]: false,
        [atypes.ACTION_TYPE_EXECUTE]: false,
        [atypes.ACTION_TYPE_DELETE]: false
      }
    }
  }

  const defaultAttrs = () => {
    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: '',
      description: '',
      connection_type: '',
      connection_info: {},
      rights: defaultRights()
    }
  }

  export default {
    props: {
      'show-steps': {
        default: true,
        type: Boolean
      }
    },
    mixins: [Validation],
    components: {
      Btn,
      ServiceList,
      ServiceIcon,
      ConnectionConfigurePanel,
      RightsList
    },
    watch: {
      'connection.ename': function(val, old_val) {
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
        is_open: false,
        mode: 'add',
        ss_errors: {},
        connection: _.assign({}, defaultAttrs()),
        original_connection: _.assign({}, defaultAttrs())
      }
    },
    computed: {
      eid() {
        return _.get(this, 'connection.eid', '')
      },
      ctype() {
        return _.get(this, 'connection.connection_type', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      service_description() {
        return _.result(this, 'cinfo.service_description', '')
      },
      has_connection() {
        return this.ctype.length > 0
      },
      title() {
        return this.mode == 'edit'
          ? 'Edit "' + _.get(this.original_connection, 'name') + '" Storage'
          : 'New Storage'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create storage'
      },
      active_username() {
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      alias_placeholder() {
        return _.kebabCase('username-my-alias')
      },
      ename_error() {
        if (_.get(this.connection, 'ename') === _.get(this.original_connection, 'ename'))
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
      open(attrs, mode) {
        this.reset(attrs)
        this.mode = _.defaultTo(mode, 'add')
        this.is_open = true
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          var ename = _.get(this.connection, 'ename', '')

          this.validateEname(ename, (response, errors) => {
            this.ss_errors = ename.length > 0 && _.size(errors) > 0
              ? _.assign({}, errors)
              : _.assign({})

            if (this.ename_error.length == 0)
              this.$nextTick(() => { this.$emit('submit', this.connection, this) })
          })
        })
      },
      reset(attrs) {
        this.ss_errors = {}
        this.connection = _.assign({}, defaultAttrs(), attrs)
        this.original_connection = _.assign({}, defaultAttrs(), attrs)
      },
      onHide() {
        this.reset()
        this.is_open = false
      },
      onRightsChanged(rights) {
        this.connection = _.assign({}, this.connection, { rights })
      },
      createPendingConnection(item) {
        var attrs = _.assign({}, this.connection, {
          name: item.service_name,
          connection_type: item.connection_type
        })

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            this.updateConnection(response.body)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      updateConnection(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.connection = _.assign({}, this.connection, update_attrs)
      }
    }
  }
</script>
