<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showTitle">
        <span class="flex-fill f4 lh-title">{{our_title}}</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
      <div
        class="flex flex-row"
        :class="showTitle ? 'mt2 pt2 bt b--black-10' : ''"
        v-if="has_connection"
      >
        <ServiceIcon class="flex-none mt1 br2 square-5" :type="ctype" :url="url" :empty-cls="''" />
        <div class="flex-fill flex flex-column" style="margin-left: 12px">
          <div class="f4 fw6 lh-title">{{service_name}}</div>
          <div class="f6 fw4 mt1">{{service_description}}</div>
          <div class="flex flex-row items-center">
            <el-tag
              class="hint--top"
              style="margin-top: 6px"
              size="medium"
              aria-label="Storage connection"
              v-if="is_storage"
            >
              <div class="flex flex-row items-center">
                <i class="db material-icons" style="font-size: 14px">layers</i>
                <span class="f8" style="margin-left: 3px">Storage</span>
              </div>
            </el-tag>
            <el-tag
              class="hint--top"
              style="margin-top: 6px"
              size="medium"
              aria-label="Email connection"
              v-if="is_email"
            >
              <div class="flex flex-row items-center">
                <i class="db material-icons" style="font-size: 14px">email</i>
                <span class="f8" style="margin-left: 3px">Email</span>
              </div>
            </el-tag>
          </div>
        </div>
        <div v-if="showSteps && mode != 'edit'">
          <div
            class="flex flex-row items-center f5 fw6 hover-blue pointer"
            title="Back to Step 1"
            @click="reset()"
          >
            <i class="material-icons">chevron_left</i>
            <span>Step 2 of 2</span>
          </div>
        </div>
      </div>
    </div>

    <div>
      <ServiceList
        :filter-by="filterBy"
        @item-activate="createPendingConnection"
        v-show="!has_connection"
      />
      <div v-if="has_connection">
        <el-form
          ref="form"
          class="el-form--compact el-form__label-tiny relative"
          label-position="top"
          :model="edit_connection"
          :rules="rules"
          @validate="onValidateItem"
        >
          <div class="flex flex-row">
            <el-form-item
              class="flex-fill mr3"
              key="name"
              prop="name"
            >
              <template slot="label">
                Name
                <span class="lh-1 hint--top" aria-label="The name of your connection">
                  <i class="el-icon-info blue"></i>
                </span>
              </template>
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
              prop="alias"
            >
              <template slot="label">
                Alias
                <span class="lh-1 hint--top hint--large" aria-label="A unique identifier that can be used to reference this connection in a pipe definition, instead of directly referencing it by its ID">
                  <i class="el-icon-info blue"></i>
                </span>
              </template>
              <el-input
                placeholder="Alias"
                autocomplete="off"
                spellcheck="false"
                v-model="edit_connection.alias"
              />
            </el-form-item>
          </div>

          <el-form-item
            key="description"
            prop="description"
          >
            <template slot="label">
              Description
              <span class="lh-1 hint--top" aria-label="A description of your connection">
                <i class="el-icon-info blue"></i>
              </span>
            </template>
            <el-input
              type="textarea"
              placeholder="Description"
              :rows="2"
              v-model="edit_connection.description"
            />
          </el-form-item>
        </el-form>

        <ConnectionInfoPanel
          ref="connection-info-panel"
          :connection-info.sync="connection_info"
          :form-errors.sync="connection_info_form_errors"
          v-if="is_http"
        />

        <div
          class="mt3 pa3 ba b--black-10 br2"
          v-else
        >
          <div class="flex flex-row items-center mb3 lh-copy ttu fw6 f6">
            <i class="material-icons mr1 f4">lock</i> Authentication
          </div>
          <ConnectionAuthenticationPanel
            ref="connection-authentication-panel"
            :connection="edit_connection"
            :mode="mode"
            @change="updateConnection"
          />
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
  import MixinConnection from './mixins/connection'
  import MixinValidation from './mixins/validation'

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
        headers: {},
        data: {}
      }
    }
  }

  export default {
    props: {
      title: {
        type: String,
        default: ''
      },
      showHeader: {
        type: Boolean,
        default: true
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      showTitle: {
        type: Boolean,
        default: true
      },
      showSteps: {
        type: Boolean,
        default: true
      },
      connection: {
        type: Object,
        default: () => { return {} }
      },
      filterBy: {
        type: Function
      },
      mode: {
        type: String,
        default: 'add'
      }
    },
    mixins: [MixinConnection, MixinValidation],
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
        orig_connection: _.assign({}, defaultAttrs(), this.connection),
        edit_connection: _.assign({}, defaultAttrs(), this.connection),
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' }
          ],
          alias: [
            { validator: this.formValidateAlias }
          ]
        },
        form_errors: {},
        connection_info_form_errors: {}
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
      url() {
        return _.get(this, 'orig_connection.connection_info.url', '')
      },
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      is_http() {
        return this.ctype == ctypes.CONNECTION_TYPE_HTTP
      },
      is_oauth() {
        return this.$_Connection_isOauth(this.ctype)
      },
      is_storage() {
        return this.$_Connection_isStorage(this.ctype)
      },
      is_email() {
        return this.$_Connection_isEmail(this.ctype)
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
      has_http_errors() {
        return _.keys(this.connection_info_form_errors).length > 0
      },
      has_errors() {
        if (this.is_http && this.has_http_errors) {
          return true
        }

        if (this.is_oauth && !this.is_connected) {
          return true
        }

        return _.keys(this.form_errors).length > 0
      },
      connection_info: {
        get() {
          return _.get(this.edit_connection, 'connection_info', {})
        },
        set(value) {
          this.edit_connection = _.assign({}, this.edit_connection, { connection_info: value })
        }
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

          var auth_panel = this.$refs['connection-authentication-panel']
          var info_panel = this.$refs['connection-info-panel']

          if (auth_panel) {
            auth_panel.validate((valid2) => {
              if (!valid2) {
                return
              }

              // there are no errors in the form; do the submit
              this.$emit('submit', this.edit_connection)
            })
          } else if (info_panel) {
            info_panel.validate((valid3) => {
              if (!valid3) {
                return
              }

              // there are no errors in the form; do the submit
              this.$emit('submit', this.edit_connection)
            })
          } else {
            // there are no errors in the form; do the submit
            this.$emit('submit', this.edit_connection)
          }
        })
      },
      reset(attrs) {
        this.orig_connection = _.assign({}, defaultAttrs(), attrs)
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

        this.$_Validation_validateAlias(OBJECT_TYPE_CONNECTION, value, (response, errors) => {
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
          var service_name = this.$_Connection_getServiceName(connection)
          var service_slug = util.slugify(service_name)

          // create a default alias
          connection.alias = 'my-' + service_slug
        }

        // we have to do this to force watcher validation
        this.$nextTick(() => {
          this.orig_connection = _.cloneDeep(connection)
          this.edit_connection = _.cloneDeep(connection)
        })
      },
      updateConnection(attrs) {
        var connection_info = _.get(attrs, 'connection_info', {})
        connection_info = _.omitBy(connection_info, (val, key) => { return val == '*****' })

        var update_attrs = _.assign({}, attrs, { connection_info })
        this.edit_connection = _.assign({}, this.edit_connection, update_attrs)
      },
      onValidateItem(key, valid) {
        var errors = _.assign({}, this.form_errors)
        if (valid) {
          errors = _.omit(errors, [key])
        } else {
          errors[key] = true
        }
        this.form_errors = _.assign({}, errors)
      },
    }
  }
</script>
