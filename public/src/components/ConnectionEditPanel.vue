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
        <ServiceIcon class="flex-none mt1 br2 square-4" :type="ctype" :url="url" :empty-cls="''" />
        <div class="flex-fill flex flex-column" style="margin-left: 12px">
          <div class="f4 fw6 lh-title">{{service_name}}</div>
          <div class="f6 fw4 mt1">{{service_description}}</div>
          <div class="flex flex-row items-center" v-if="false">
            <el-tag
              class="hint--top"
              style="margin-top: 6px"
              size="small"
              aria-label="Storage connection"
              v-if="is_storage"
            >
              <div class="flex flex-row items-center cursor-default">
                <i class="db material-icons" style="font-size: 14px">layers</i>
                <span class="f8" style="margin-left: 3px">Storage</span>
              </div>
            </el-tag>
            <el-tag
              class="hint--top"
              style="margin-top: 6px"
              size="small"
              aria-label="Email connection"
              v-if="is_email"
            >
              <div class="flex flex-row items-center cursor-default">
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
      <div class="ph3 ph4-l" v-if="has_connection">
        <div class="mb2 lh-copy ttu fw6 f6">Connection Properties</div>
        <el-form
          ref="form"
          class="el-form--compact el-form__label-tiny relative"
          label-position="top"
          :model="edit_connection"
          :rules="rules"
          @validate="onValidateItem"
        >
          <el-form-item
            key="name"
            prop="name"
          >
            <template slot="label">
              <span>Name</span>
              <span class="lh-1 hint--top hint--large" aria-label="A unique identifier that can be used to reference this connection in a pipe">
                <i class="el-icon-info blue"></i>
              </span>
            </template>
            <el-input
              placeholder="Enter name"
              autocomplete="off"
              spellcheck="false"
              :autofocus="true"
              v-model="edit_connection.name"
            />
          </el-form-item>

          <el-form-item
            key="description"
            prop="description"
            label="Description"
          >
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

        <div class="mt4" v-else-if="is_keyring">
          <div class="mb2 lh-copy ttu fw6 f6">Keypair Values</div>
          <KeypairList
            class="pa4 ba b--black-10 br2"
            :header="{ key: 'Key', val: 'Value' }"
            v-model="connection_info"
          />
        </div>

        <div class="mt4" v-else>
          <div class="mb2 lh-copy ttu fw6 f6">Authentication</div>
          <ConnectionAuthenticationPanel
            ref="connection-authentication-panel"
            class="pa4 ba b--black-10 br2"
            :connection="edit_connection"
            :mode="mode"
            @change="updateConnection"
          />
        </div>
      </div>
    </div>

    <div class="mt4 w-100 flex flex-row justify-end" v-show="showFooter && has_connection">
      <el-button
        class="ttu fw6"
        @click="$emit('cancel')"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
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
  import randomstring from 'randomstring'
  import { mapState } from 'vuex'
  import { OBJECT_TYPE_CONNECTION } from '../constants/object-type'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import { slugify } from '@/utils'
  import ServiceList from '@comp/ServiceList'
  import ServiceIcon from '@comp/ServiceIcon'
  import KeypairList from '@comp/KeypairList'
  import ConnectionAuthenticationPanel from '@comp/ConnectionAuthenticationPanel'
  import ConnectionInfoPanel from '@comp/ConnectionInfoPanel'
  import MixinConnection from '@comp/mixins/connection'
  import MixinValidation from '@comp/mixins/validation'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const defaultAttrs = (ctype) => {
    var connection_info = ctype === ctypes.CONNECTION_TYPE_KEYRING ? {} : {
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

    var suffix = getNameSuffix()

    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: `connection-${suffix}`,
      short_description: '',
      description: '',
      connection_type: '',
      connection_info
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
        default: 'add' // 'add' or 'edit'
      }
    },
    mixins: [MixinConnection, MixinValidation],
    components: {
      ServiceList,
      ServiceIcon,
      KeypairList,
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
            this.$refs.form.validateField('name')
          }
        })
      }
    },
    data() {
      var ctype = this.connection.connection_type

      return {
        orig_connection: _.assign({}, defaultAttrs(ctype), this.connection),
        edit_connection: _.assign({}, defaultAttrs(ctype), this.connection),
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' },
            { validator: this.formValidateName }
          ]
        },
        form_errors: {},
        connection_info_form_errors: {}
      }
    },
    computed: {
      ...mapState([
        'active_team_identifier'
      ]),
      eid() {
        return _.get(this.edit_connection, 'eid', '')
      },
      ctype() {
        return _.get(this.edit_connection, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.edit_connection, 'connection_status', '')
      },
      cname() {
        return _.get(this.orig_connection, 'name', '')
      },
      url() {
        return _.get(this.orig_connection, 'connection_info.url', '')
      },
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      is_http() {
        return this.ctype == ctypes.CONNECTION_TYPE_HTTP
      },
      is_keyring() {
        return this.ctype == ctypes.CONNECTION_TYPE_KEYRING
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

        return this.mode == 'edit' ? `Edit "${this.cname}" Connection` : 'New Connection'
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
          this.$refs.form.validateField('name')
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
        var ctype = _.get(attrs, 'connection_type')
        this.orig_connection = _.assign({}, defaultAttrs(ctype), attrs)
        this.edit_connection = _.assign({}, defaultAttrs(ctype), attrs)
      },
      createPendingConnection(item) {
        var ctype = item.connection_type
        var service_slug = slugify(item.service_name)
        var attrs = _.assign({}, defaultAttrs(ctype), {
          eid_status: OBJECT_STATUS_PENDING,
          name: `${service_slug}-` + getNameSuffix(16),
          short_description: item.service_name,
          connection_type: ctype
        })

        this.$store.dispatch('v2_action_createConnection', { attrs }).then(response => {
          var connection = _.cloneDeep(response.data)
          connection.name = `${service_slug}-` + getNameSuffix(4),
          this.updateConnection(connection)
        }).catch(error => {
          // TODO: add error handling?
        })
      },
      formValidateName(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        if (this.mode == 'edit' && value == _.get(this.connection, 'name', '')) {
          callback()
          return
        }

        this.$_Validation_validateName(this.active_team_identifier, OBJECT_TYPE_CONNECTION, value, (response, errors) => {
          var message = _.get(errors, 'name.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      initConnection() {
        var connection = _.cloneDeep(this.connection)

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
