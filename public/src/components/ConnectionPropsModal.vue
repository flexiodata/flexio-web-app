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
      <div class="flex flex-row mt1 pt2 bt b--black-10" v-if="has_connection">
        <connection-icon :type="ctype" class="flex-none dib v-top br2 fx-square-4" style="max-height: 3rem"></connection-icon>
        <div class="flex-fill flex flex-column ml2">
          <div class="mid-gray f4 fw6">{{service_name}}</div>
          <div class="mid-gray f6 fw4 mt2">{{service_description}}</div>
        </div>
        <div class="mid-gray" v-if="mode != 'edit'">
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
        @item-activate="createPendingConnection"
      >
      </service-list>
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
              aria-label="When adding an input or output from the Flex.io command bar, a connection can be referenced using its object ID or via an alias created here. Aliases are unique across the app, so we recommend prefixing your alias with your username (e.g., username-foo)."
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
        <connection-configure-panel
          :connection="connection"
          :mode="mode"
          @disconnect="tryDisconnect"
          @test="tryTest"
          @connect="tryOauthConnect"
          @change="updateConnection"
        ></connection-configure-panel>
      </div>
    </div>

    <div slot="footer" class="flex flex-row items-end" v-show="has_connection">
      <btn btn-md class="b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="b ttu blue" @click="submit()">{{submit_label}}</btn>
    </div>
  </ui-modal>
</template>

<script>
  import * as types from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import { HOSTNAME } from '../constants/common'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapGetters } from 'vuex'
  import api from '../api'
  import Btn from './Btn.vue'
  import ServiceList from './ServiceList.vue'
  import ConnectionIcon from './ConnectionIcon.vue'
  import ConnectionConfigurePanel from './ConnectionConfigurePanel.vue'
  import oauthPopup from './mixins/oauth-popup'

  const DEFAULT_ATTRS = {
    eid: null,
    eid_status: OBJECT_STATUS_PENDING,
    name: '',
    description: '',
    connection_type: ''
  }

  export default {
    props: ['open-from', 'close-to', 'project-eid'],
    mixins: [oauthPopup],
    components: {
      Btn,
      ServiceList,
      ConnectionIcon,
      ConnectionConfigurePanel
    },
    watch: {
      'connection.ename': function(val, old_val) { this.validate() }
    },
    data() {
      return {
        is_open: false,
        mode: 'add',
        ss_errors: {},
        connection: _.extend({}, DEFAULT_ATTRS),
        original_connection: _.extend({}, DEFAULT_ATTRS)
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
      oauth_url() {
        var eid =  _.get(this, 'connection.eid', '')
        var base_url = 'https://'+HOSTNAME+'/a/connectionauth'

        switch (this.ctype)
        {
          case types.CONNECTION_TYPE_DROPBOX:      return base_url+'?service=dropbox&eid='+eid
          case types.CONNECTION_TYPE_GOOGLEDRIVE:  return base_url+'?service=googledrive&eid='+eid
          case types.CONNECTION_TYPE_GOOGLESHEETS: return base_url+'?service=googlesheets&eid='+eid
        }

        return ''
      },
      title() {
        return this.mode == 'edit'
          ? 'Edit "' + _.get(this.original_connection, 'name') + '" Connection'
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

          this.$emit('submit', this.connection, this)
        })
      },
      reset(attrs) {
        this.ss_errors = {}
        this.connection = _.extend({}, DEFAULT_ATTRS, attrs)
        this.original_connection = _.extend({}, DEFAULT_ATTRS, attrs)
      },
      onHide() {
        this.reset()
        this.is_open = false
      },
      validate: _.debounce(function(validate_key) {
        var validate_attrs = [{
          key: 'ename',
          value: _.get(this.connection, 'ename', ''),
          type: 'ename'
        }]

        api.validate({ attrs: validate_attrs }).then((response) => {
          var errors = _.keyBy(response.body, 'key')
          this.ss_errors = _.get(this.connection, 'ename', '').length > 0 && _.size(errors) > 0 ? _.assign({}, errors) : _.assign({})
        }, (response) => {
          // error callback
        })
      }, 300),
      createPendingConnection(item) {
        var me = this
        var attrs = _.extend({}, this.connection, {
          name: item.service_name,
          connection_type: item.connection_type,
          parent_eid: this.projectEid
        })

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            me.updateConnection(response.body)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      updateConnection(info) {
        this.connection = _.assign({}, this.connection, info)
      },
      tryDisconnect(attrs) {
        var me = this
        var eid = attrs.eid

        // disconnect from this connection (oauth only)
        this.$store.dispatch('disconnectConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            me.updateConnection(response.body)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryTest(attrs) {
        var me = this
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'ename', 'description', 'token', 'host', 'port', 'username', 'password', 'database'])

        // update the connection
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            // test the connection
            me.$store.dispatch('testConnection', { eid, attrs }).then(response => {
              if (response.ok)
              {
                me.updateConnection(_.omit(response.body, ['name', 'ename', 'description', 'token', 'host', 'port', 'username', 'password', 'database']))
              }
               else
              {
                // TODO: add error handling
              }
            })
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryOauthConnect() {
        var me = this
        var eid = this.eid

        this.oauthPopup(this.oauth_url, (params) => {
          // TODO: handle 'code' and 'state' and 'error' here...

          // for now, re-fetch the connection to update its state
          this.$store.dispatch('fetchConnection', { eid }).then(response => {
            if (response.ok)
            {
              me.updateConnection(_.omit(response.body, ['name', 'ename', 'description']))
            }
             else
            {
              // TODO: add error handling
            }
          })
        })
      }
    }
  }
</script>
