<template>
  <div v-if="is_oauth">
    <div v-if="!is_connected">
      <p class="tc">To use this connection, you must first connect {{service_name}} to Flex.io.</p>
      <div class="mv3 tc">
        <el-button
          class="ttu fw6"
          type="primary"
          @click="onConnectClick"
        >
          Connect to your {{service_name}} account
        </el-button>
      </div>
    </div>

    <div v-else>
      <div class="flex flex-row items-center justify-center lh-copy">
        <i class="el-icon-success dark-green f3 mr2"></i>
        <span class="dn dib-ns dark-green f4">You are connected to {{service_name}}!</span>
      </div>
      <div class="mv3 tc">
        <el-button
          class="ttu fw6"
          size="small"
          plain
          @click="onDisconnectClick"
          v-if="is_connected"
        >
          Disconnect from {{service_name}}
        </el-button>
      </div>
    </div>

    <div
      v-show="is_connected"
      v-if="is_github"
    >
      <TextSeparator class="mt4 mb3 lh-copy ttu fw6 f6">Additional configuration</TextSeparator>
      <div class="center mw6">
        <p>Enter the URL of the GitHub repository to which you'd like to connect.</p>
        <el-form
          ref="form"
          class="el-form--cozy el-form__label-tiny"
          label-position="top"
          :model="$data"
          :rules="rules"
          @submit.prevent.native
        >
          <el-form-item
            key="github_url"
            prop="github_url"
            label="Repository URL"
          >
            <el-input
              placeholder="flexiodata/functions-currency"
              spellcheck="false"
              v-model="github_url"
            >
              <div slot="prepend">https://github.com/</div>
            </el-input>
          </el-form-item>
        </el-form>
      </div>
    </div>

    <div
      v-show="is_connected"
      v-else-if="is_box || is_dropbox || is_google_drive"
    >
      <TextSeparator class="mt4 mb3 lh-copy ttu fw6 f6">Additional configuration</TextSeparator>
      <div class="center mw6">
        <p>Enter a base path to change the root folder for this connection.</p>
        <el-form
          ref="form"
          class="el-form--compact el-form__label-tiny"
          label-position="top"
          :model="edit_connection_info"
          :rules="rules"
          @submit.prevent.native
        >
          <el-form-item
            label="Base Path"
            key="base_path"
            prop="base_path"
          >
            <el-input
              placeholder="Base Path (optional)"
              spellcheck="false"
              v-model="edit_connection_info.base_path"
            />
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
  <div v-else>
    <div
      class="flex flex-row items-center justify-center lh-copy mb3"
      v-if="is_connected"
    >
      <i class="el-icon-success dark-green f4 mr2"></i>
      <span class="dn dib-ns dark-green f5">You are connected to {{service_name}}!</span>
    </div>
    <p
      class="tc"
      v-else
    >
      To use this connection, you must first connect {{service_name}} to Flex.io.
    </p>
    <BuilderItemForm
      :item="form_json"
      :default-values="edit_connection_info"
      :show-footer="false"
      @values-change="onValuesChange"
    >
      <el-form-item class="mt3" slot="form-append">
        <el-button
          class="ttu fw6"
          :type="test_btn_type"
          :icon="test_btn_icon"
          :loading="test_state == 'testing'"
          :disabled="test_state == 'success' || test_state == 'error'"
          @click="onTestClick"
        >
          {{test_btn_label}}
        </el-button>
      </el-form-item>
    </BuilderItemForm>
  </div>
</template>

<script>
  import axios from 'axios'
  import { mapState } from 'vuex'
  import { HOSTNAME } from '@/constants/common'
  import { CONNECTION_STATUS_AVAILABLE } from '@/constants/connection-status'
  import * as ctypes from '@/constants/connection-type'
  import * as cinfos from '@/constants/connection-info'
  import BuilderItemForm from '@/components/BuilderItemForm'
  import TextSeparator from '@/components/TextSeparator'
  import MixinOauth from '@/components/mixins/oauth'
  import MixinConnection from '@/components/mixins/connection'

  // form definitons
  import amazons3 from '../data/connection/amazons3.yml'
  import mysql from '../data/connection/mysql.yml'
  import postgres from '../data/connection/postgres.yml'
  import sftp from '../data/connection/sftp.yml'

  const getDefaultState = () => {
    return {
      is_emitting_update: false,
      test_state: 'none', // none, testing, error, success
      forms: {
        amazons3,
        mysql,
        postgres,
        sftp
      },
      rules: {
        github_url: [
          { required: true, message: 'Please enter the URL of the GitHub repository' }
        ]
      },


      // edit values
      edit_connection: {},
      edit_connection_info: {},
      github_url: '',
    }
  }

  export default {
    props: {
      connection: {
        type: Object,
        required: true
      }
    },
    mixins: [MixinOauth, MixinConnection],
    components: {
      BuilderItemForm,
      TextSeparator,
    },
    watch: {
      connection: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_connection: {
        handler: 'emitUpdate',
        deep: true
      },
      edit_connection_info: {
        handler(connection_info) {
          this.updateEditConnection({ connection_info })
        },
        deep: true
      },
      github_url: {
        handler: 'updateOwnerAndRepository',
      },
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.connection, 'connection_status', '')
      },
      is_oauth() {
        return this.$_Connection_isOauth(this.ctype)
      },
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      is_box() {
        return this.ctype == ctypes.CONNECTION_TYPE_BOX
      },
      is_dropbox() {
        return this.ctype == ctypes.CONNECTION_TYPE_DROPBOX
      },
      is_google_drive() {
        return this.ctype == ctypes.CONNECTION_TYPE_GOOGLEDRIVE
      },
      is_github() {
        return this.ctype == ctypes.CONNECTION_TYPE_GITHUB
      },
      cinfo() {
        return _.find(cinfos, { connection_type: this.ctype })
      },
      service_name() {
        return _.result(this.cinfo, 'service_name', '')
      },
      oauth_url() {
        var eid = _.get(this.connection, 'eid', '')
        return 'https://' + HOSTNAME + '/oauth2/connect' + '?service=' + this.ctype + '&eid=' + eid
      },
      form_json() {
        return this.forms[this.ctype] || {}
      },
      test_btn_type() {
        switch (this.test_state) {
          case 'none':    return 'primary'
          case 'testing': return 'primary'
          case 'success': return 'success'
          case 'error':   return 'danger'
        }

        return 'primary'
      },
      test_btn_icon() {
        switch (this.test_state) {
          case 'none':    return ''
          case 'testing': return ''
          case 'success': return 'el-icon-success'
          case 'error':   return 'el-icon-error'
        }

        return 'Test connection'
      },
      test_btn_label() {
        switch (this.test_state) {
          case 'none':    return 'Test connection'
          case 'testing': return 'Testing...'
          case 'success': return 'Success!'
          case 'error':   return 'Error'
        }

        return 'Test connection'
      },
    },
    methods: {
      initSelf() {
        if (this.is_emitting_update === true ) {
          return
        }

        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(this.connection))
        this.edit_connection_info = _.cloneDeep(_.get(this.connection, 'connection_info', {}))

        // set up value for GitHub repo URL input model
        if (this.is_github) {
          var owner = _.get(this.edit_connection_info, 'owner', '')
          var repository = _.get(this.edit_connection_info, 'repository', '')
          if (owner.length > 0 && repository.length > 0) {
            this.github_url = `${owner}/${repository}`
          }
        }
      },
      emitUpdate() {
        this.is_emitting_update = true
        this.$emit('update:connection', this.edit_connection)
        this.$nextTick(() => { this.is_emitting_update = false })
      },
      updateEditConnection(attrs) {
        this.edit_connection = _.assign({}, this.edit_connection, attrs)
      },
      updateOwnerAndRepository() {
        var idx = this.github_url.indexOf('github.com/')
        var suffix = idx == -1 ? this.github_url : this.github_url.substring(idx + 11)
        var arr = suffix.split('/')
        if (arr.length == 2) {
          var owner = arr[0]
          var repository = arr[1]
          var attrs = { owner, repository }

          this.github_url = `${owner}/${repository}`
          this.edit_connection_info = _.assign({}, this.edit_connection_info, attrs)
        }
      },
      onValuesChange(values) {
        this.edit_connection_info = _.assign({}, this.edit_connection_info, values)
      },
      onTestClick() {
        this.tryTest()
      },
      onDisconnectClick() {
        this.tryDisconnect()
      },
      onConnectClick() {
        this.tryOauthConnect()
      },
      tryTest() {
        var eid = _.get(this.connection, 'eid', '')
        var team_name = this.active_team_name
        var attrs = _.pick(this.edit_connection, ['name', 'description', 'connection_info'])

        // update the connection
        this.$store.dispatch('connections/update', { team_name, eid, attrs }).then(response => {
          this.test_state = 'testing'

          // test the connection
          this.$store.dispatch('connections/test', { team_name, eid, attrs }).then(response => {
            var connection_status = _.get(response.data, 'connection_status', '')
            this.updateEditConnection({ connection_status })

            this.test_state = 'success'
            setTimeout(() => { this.test_state = 'none' }, 4000)
          }).catch(error => {
            this.test_state = 'error'
            setTimeout(() => { this.test_state = 'none' }, 4000)
          })
        }).catch(error => {
          this.test_state = 'error'
          setTimeout(() => { this.test_state = 'none' }, 4000)
        })
      },
      tryDisconnect() {
        var eid = _.get(this.connection, 'eid', '')
        var team_name = this.active_team_name
        var attrs = _.pick(this.edit_connection, ['name', 'description', 'connection_info'])

        // disconnect from this connection (oauth only)
        this.$store.dispatch('connections/disconnect', { team_name, eid, attrs }).then(response => {
          var connection_status = _.get(response.data, 'connection_status', '')
          this.updateEditConnection({ connection_status })

          this.$nextTick(() => {
            if (!this.is_connected) {
              this.$message({
                message: "You've successfully disconnected from " + this.service_name + ".",
                type: 'success'
              })
            } else {
              this.$message({
                message: "There was a problem disconnecting from " + this.service_name + ".",
                type: 'error'
              })
            }
          })
        }).catch(error => {
          this.$message({
            message: _.get(error, 'response.data.error.message', ''),
            type: 'error'
          })
        })
      },
      tryOauthConnect() {
        var eid = _.get(this.connection, 'eid', '')
        var team_name = this.active_team_name

        this.$_Oauth_showPopup(this.oauth_url, params => {
          // TODO: handle 'code' and 'state' and 'error' here...

          // for now, re-fetch the connection to update its state
          this.$store.dispatch('connections/fetch', { team_name, eid }).then(response => {
            var connection_status = _.get(response.data, 'connection_status', '')
            this.updateEditConnection({ connection_status })

            this.$nextTick(() => {
              if (this.is_connected) {
                this.$message({
                  message: "You've successfully connected to " + this.service_name + "!",
                  type: 'success'
                })
              }
            })
          }).catch(error => {
            this.$message({
              message: _.get(error, 'response.data.error.message', ''),
              type: 'error'
            })
          })
        })
      },
    }
  }
</script>
