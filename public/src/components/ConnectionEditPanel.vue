<template>
  <div>
    <div class="w-100 mb3">
      <div class="flex flex-row items-center" v-if="showHeader">
        <div class="flex-fill">
          <span class="f4">{{our_title}}</span>
        </div>
        <div
          class="pointer f3 lh-solid b child black-30 hover-black-60"
          @click="close"
        >
          &times;
        </div>
      </div>
      <div class="flex flex-row" :class="showHeader ? 'mt2 pt2 bt b--black-10' : ''" v-if="has_connection">
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

    <div>
      <service-list v-show="!has_connection"
        filter-items="storage"
        :prefix-items="prefix_services"
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
                :placeholder="alias_placeholder"
                :error="ename_error"
                :invalid="ename_error.length > 0"
                v-model="edit_connection.ename"
              ></ui-textbox>
            </div>
            <div
              class="hint--bottom-left hint--large cursor-default"
              aria-label="Connections can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API. Aliases are unique across the app, so we recommend prefixing your alias with your username (e.g., username-foo)."
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

        <div v-if="is_http">
          <connection-info-configure-panel
            :connection.sync="edit_connection"
          />
        </div>
        <div v-else>
          <div class="pv2 ph3 bg-black-05 fw6">Authentication</div>
          <div class="pa3 ba bt-0 b--black-05">
            <connection-configure-panel
              :connection="edit_connection"
              :mode="mode"
              @change="updateConnection"
            ></connection-configure-panel>
          </div>
        </div>
      </div>
    </div>

    <div class="pt4 w-100 flex flex-row justify-end" v-show="has_connection">
      <el-button class="ttu b" type="plain" @click="close">Cancel</el-button>
      <el-button class="ttu b" type="primary" @click="submit">{{submit_label}}</el-button>
    </div>
  </div>
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
  import ConnectionInfoConfigurePanel from './ConnectionInfoConfigurePanel.vue'
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
      ename: '',
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
      'show-steps': {
        type: Boolean,
        default: true
      },
      'connection': {
        type: Object,
        default: () => { return {} }
      },
      'mode': {
        type: String,
        default: 'add'
      }
    },
    mixins: [Validation],
    components: {
      Btn,
      ServiceList,
      ServiceIcon,
      ConnectionConfigurePanel,
      ConnectionInfoConfigurePanel,
      RightsList
    },
    watch: {
      'connection': function(val, old_val) {
        this.edit_connection = _.cloneDeep(val)
        this.updateConnection(val)
      },
      'edit_connection.ename': function(val, old_val) {
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
        ss_errors: {},
        edit_connection: _.assign({}, defaultAttrs(), this.connection),
        prefix_services: []//[connections.CONNECTION_INFO_CUSTOMAPI]
      }
    },
    computed: {
      eid() {
        return _.get(this, 'edit_connection.eid', '')
      },
      ctype() {
        return _.get(this, 'edit_connection.connection_type', '')
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
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      alias_placeholder() {
        return _.kebabCase('username-my-alias')
      },
      ename_error() {
        if (this.mode == 'edit' && _.get(this.edit_connection, 'ename') === _.get(this.connection, 'ename'))
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
        this.$emit('open')
      },
      close() {
        this.$emit('close')
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          var ename = _.get(this.edit_connection, 'ename', '')

          this.validateEname(ename, (response, errors) => {
            this.ss_errors = ename.length > 0 && _.size(errors) > 0
              ? _.assign({}, errors)
              : _.assign({})

            if (this.ename_error.length == 0)
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
          name: item.service_name,
          connection_type: item.connection_type
        })

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            var connection = _.cloneDeep(response.body)

            // add username as the alias prefix
            connection.ename = this.active_username.trim() + ' ' + item.service_name.trim()
            connection.ename = connection.ename.toLowerCase().replace(/\s/g, '-')

            this.updateConnection(connection)
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
        this.edit_connection = _.assign({}, this.edit_connection, update_attrs)
      }
    }
  }
</script>
