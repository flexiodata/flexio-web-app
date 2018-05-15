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
          <div class="f6 fw4">{{service_description}}</div>
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
      <service-list
        @item-activate="createPendingConnection"
        v-show="!has_connection"
      />
      <div v-if="has_connection">
        <el-form
          ref="form"
          class="el-form-compact el-form__label-tiny"
          :model="edit_connection"
          :rules="rules"
        >
          <div class="flex flex-row">
            <el-form-item
              class="flex-fill mr3"
              key="name"
              label="Name"
              prop="name"
            >
              <el-input
                placeholder="Name"
                autocomplete="off"
                :autofocus="true"
                v-model="edit_connection.name"
              />
            </el-form-item>

            <el-form-item
              class="flex-fill"
              key="alias"
              label="Alias"
              prop="alias"
            >
              <el-input
                placeholder="Alias"
                autocomplete="off"
                spellcheck="false"
                v-model="edit_connection.alias"
              >
                <span
                  slot="suffix"
                  class="h-100 hint--bottom-left hint--large cursor-default"
                  aria-label="Connections can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API."
                >
                  <i class="material-icons md-24 blue v-mid">info</i>
                </span>
              </el-input>
            </el-form-item>
          </div>

          <el-form-item
            key="description"
            label="Description"
            prop="description"
          >
            <el-input
              type="textarea"
              placeholder="Description"
              :rows="2"
              v-model="edit_connection.description"
            />
          </el-form-item>
        </el-form>

        <connection-info-panel
          :connection.sync="edit_connection"
          v-if="is_http"
        />

        <div
          class="mt4"
          v-else
        >
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
      <el-button
        class="ttu b"
        @click="$emit('cancel')"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu b"
        type="primary"
        @click="submit"
        :disabled="has_errors"
      >
        {{submit_label}}
      </el-button>
    </div>
  </div>
</template>

<script>
  import { OBJECT_TYPE_CONNECTION } from '../constants/object-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import util from '../utils'
  import ServiceList from './ServiceList.vue'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'
  import ConnectionInfoPanel from './ConnectionInfoPanel.vue'
  import ConnectionInfo from './mixins/connection-info'
  import Validation from './mixins/validation'

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
      }
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
      eid() {
        this.$nextTick(() => {
          if (this.$refs.form) {
            this.$refs.form.validateField('alias')
          }
        })
      }
    },
    data() {
      return {
        edit_connection: _.assign({}, defaultAttrs(), this.connection),
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' }
          ],
          alias: [
            { validator: this.formValidateAlias }
          ]
        }
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
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      service_description() {
        return _.result(this, 'cinfo.service_description', '')
      },
      has_connection() {
        return this.ctype.length > 0
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit'
          ? 'Edit "' + _.get(this.connection, 'name') + '" Connection'
          : 'New Connection'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create connection'
      },
      has_errors() {
        if (this.is_oauth && !this.is_connected)
          return true

        return false
      }
    },
    mounted() {
      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.validateField('alias')
        }
      })
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      submit() {
        this.$refs.form.validate((valid) => {
          if (!valid)
            return

          // there are no errors in the form; do the submit
          this.$emit('submit', this.edit_connection)
        })
      },
      reset(attrs) {
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
            var service_slug = util.slugify(item.service_name)

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
      formValidateAlias(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        if (this.mode == 'edit' && value == _.get(this.connection, 'alias', '')) {
          callback()
          return
        }

        this.validateAlias(OBJECT_TYPE_CONNECTION, value, (response, errors) => {
          var message = _.get(errors, 'alias.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      initConnection() {
        var connection = _.cloneDeep(this.connection)

        if (this.mode == 'add' && _.has(connection, 'connection_type')) {
          var service_name = this.getConnectionServiceName(connection)
          var service_slug = util.slugify(service_name)

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
