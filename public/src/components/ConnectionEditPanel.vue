<template>
  <div class="mid-gray">
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showTitle">
        <span class="flex-fill f4">{{our_title}}</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
      <div
        class="flex flex-row"
        :class="showTitle ? 'mt2 pt2 bt b--black-10' : ''"
        v-if="has_connection"
      >
        <service-icon :type="ctype" class="flex-none dib v-top br2 square-4" />
        <div class="flex-fill flex flex-column ml2">
          <div class="f4 fw6 lh-title">{{service_name}}</div>
          <div class="f6 fw4 mt1">{{service_description}}</div>
        </div>
        <div v-if="showSteps && mode != 'edit'">
          <i class="material-icons v-mid nr1">chevron_left</i>
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

    <div>
      <service-list v-show="!has_connection"
        :omit-items="omitConnections"
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
                v-model="edit_connection.name"
                v-validate
                data-vv-name="name"
                data-vv-value-path="edit_connection.name"
                data-vv-rules="required"
              ></ui-textbox>
            </div>
            <div class="flex-fill">
              <ui-textbox
                autocomplete="off"
                spellcheck="false"
                label="Alias"
                help=" "
                :error="alias_error"
                :invalid="alias_error.length > 0"
                v-model="edit_connection.alias"
              ></ui-textbox>
            </div>
            <div
              class="hint--bottom-left hint--large cursor-default"
              aria-label="Connections can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API."
            >
              <i class="material-icons blue md-24">info</i>
            </div>
          </div>
          <ui-textbox
            label="Description"
            floating-label
            help=" "
            :multi-line="true"
            :rows="1"
            v-model="edit_connection.description"
          ></ui-textbox>
        </form>

        <div class="mt4" v-if="is_http">
          <connection-info-panel
            :connection.sync="edit_connection"
          />
        </div>
        <div class="mt4" v-else>
          <div class="flex flex-row items-center pa2 bg-light-gray br2 br--top mid-gray lh-copy ttu fw6 f6">
            <i class="material-icons mr1 f5">lock</i> Authentication
          </div>
          <div class="pa3 pb4 ba bt-0 b--light-gray br2 br--bottom">
            <connection-authentication-panel
              :connection="edit_connection"
              :mode="mode"
              @change="updateConnection"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="mt4 w-100 flex flex-row justify-end" v-show="showFooter && has_connection">
      <el-button class="ttu b" type="plain" @click="$emit('cancel')">Cancel</el-button>
      <el-button class="ttu b" type="primary" @click="submit" :disabled="mode == 'add' && (has_errors || (is_oauth && !is_connected))">{{submit_label}}</el-button>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { OBJECT_TYPE_CONNECTION } from '../constants/object-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import * as mtypes from '../constants/member-type'
  import * as atypes from '../constants/action-type'
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import api from '../api'
  import ServiceList from './ServiceList.vue'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'
  import ConnectionInfoPanel from './ConnectionInfoPanel.vue'
  import ConnectionInfo from './mixins/connection-info'
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
      alias: '',
      description: '',
      connection_type: '',
      connection_info: {
        method: '',
        url: '',
        auth: 'none',
        username: '',
        password: '',
        token: '',
        access_token: '',
        refresh_token: '',
        expires: '',
        headers: [],
        data: []
      },
      rights: defaultRights()
    }
  }

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'show-header': {
        type: Boolean,
        default: true
      },
      'show-footer': {
        type: Boolean,
        default: true
      },
      'show-title': {
        type: Boolean,
        default: true
      },
      'show-steps': {
        type: Boolean,
        default: true
      },
      'connection': {
        type: Object,
        default: () => { return {} }
      },
      'omit-connections': {
        type: [Array, Function]
      },
      'mode': {
        type: String,
        default: 'add'
      }
    },
    mixins: [ConnectionInfo, Validation],
    components: {
      ServiceList,
      ServiceIcon,
      ConnectionAuthenticationPanel,
      ConnectionInfoPanel
    },
    watch: {
      connection: {
        handler: 'initConnection',
        immediate: true,
        deep: true
      },
      'edit_connection.alias': function(val, old_val) {
        var alias = val

        this.validateAlias(OBJECT_TYPE_CONNECTION, val, (response, errors) => {
          this.ss_errors = alias.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})
        })
      }
    },
    data() {
      return {
        ss_errors: {},
        edit_connection: _.assign({}, defaultAttrs(), this.connection)
      }
    },
    computed: {
      eid() {
        return _.get(this, 'edit_connection.eid', '')
      },
      ctype() {
        return _.get(this, 'edit_connection.connection_type', '')
      },
      cstatus() {
        return _.get(this, 'edit_connection.connection_status', '')
      },
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
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
      is_http() {
        return this.ctype == ctypes.CONNECTION_TYPE_HTTP
      },
      is_oauth() {
        switch (this.ctype)
        {
          case ctypes.CONNECTION_TYPE_BOX:
          case ctypes.CONNECTION_TYPE_DROPBOX:
          case ctypes.CONNECTION_TYPE_GITHUB:
          case ctypes.CONNECTION_TYPE_GOOGLEDRIVE:
          case ctypes.CONNECTION_TYPE_GOOGLESHEETS:
            return true
        }
        return false
      },
      our_title() {
        if (this.title.length > 0)
          return this.title

        return this.mode == 'edit'
          ? 'Edit "' + _.get(this.connection, 'name') + '" Connection'
          : 'New Connection'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create connection'
      },
      active_username() {
        return _.get(this.getActiveUser(), 'username', '')
      },
      alias_error() {
        if (this.mode == 'edit' && _.get(this.edit_connection, 'alias') === _.get(this.connection, 'alias'))
          return ''

        return _.get(this.ss_errors, 'alias.message', '')
      },
      has_client_errors() {
        var errors = _.get(this.errors, 'errors', [])
        return _.size(errors) > 0
      },
      has_errors() {
        return this.has_client_errors || this.alias_error.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          var alias = _.get(this.edit_connection, 'alias', '')

          this.validateAlias(OBJECT_TYPE_CONNECTION, alias, (response, errors) => {
            this.ss_errors = alias.length > 0 && _.size(errors) > 0
              ? _.assign({}, errors)
              : _.assign({})

            if (this.alias_error.length == 0)
              this.$nextTick(() => { this.$emit('submit', this.edit_connection) })
          })
        })
      },
      reset(attrs) {
        this.ss_errors = {}
        this.edit_connection = _.assign({}, defaultAttrs(), attrs)
      },
      createPendingConnection(item) {
        var attrs = _.assign({}, this.edit_connection, {
          eid_status: OBJECT_STATUS_PENDING,
          name: item.service_name,
          connection_type: item.connection_type
        })

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            var connection = _.cloneDeep(response.body)
            var service_slug = item.service_name.trim()
            service_slug = service_slug.replace(/\W/g, '-')
            service_slug = service_slug.replace(/-./g, '-')
            service_slug = service_slug.replace(/\s/g, '-')
            service_slug = service_slug.toLowerCase()

            // create a default alias
            connection.alias = 'my-' + service_slug

            this.updateConnection(connection)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      initConnection() {
        var connection = _.cloneDeep(this.connection)

        if (this.mode == 'add' && _.has(connection, 'connection_type')) {
          var service_name = this.getConnectionServiceName(connection)

          var service_slug = service_name.trim()
          service_slug = service_slug.replace(/\W/g, '-')
          service_slug = service_slug.replace(/-./g, '-')
          service_slug = service_slug.replace(/\s/g, '-')
          service_slug = service_slug.toLowerCase()

          // create a default alias
          connection.alias = 'my-' + service_slug
        }

        // we have to do this to force watcher validation
        this.$nextTick(() => {
          this.edit_connection = _.assign({}, connection)
        })
      },
      updateConnection(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.edit_connection = _.assign({}, this.edit_connection, update_attrs)
      }
    }
  }
</script>
