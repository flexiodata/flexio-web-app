<template>
  <div v-if="is_oauth">
    <template v-if="is_github">
      <p>Enter the URL of the GitHub repository to which you'd like to connect.</p>
      <el-form
        ref="form"
        class="el-form--cozy el-form__label-tiny"
        label-position="top"
        :model="$data"
        :rules="rules"
      >
        <el-form-item
          key="github_url"
          prop="github_url"
          label="Repository URL"
        >
          <el-input
            placeholder="https://github.com/flexiodata/functions-currency"
            spellcheck="false"
            v-model="github_url"
          />
        </el-form-item>
      </el-form>
      <p class="mt4">To use this connection with a private repository, you must also connect GitHub to Flex.io.</p>
    </template>
    <template v-else-if="!is_connected">
      <p class="tc">To use this connection, you must first connect {{service_name}} to Flex.io.</p>
    </template>

    <template v-if="is_connected">
      <div class="flex flex-row items-center justify-center lh-copy">
        <i class="el-icon-success v-mid dark-green f2 mr2"></i>
        <span class="dn dib-ns f4 fw6">You are connected to {{service_name}}!</span>
      </div>
      <div class="mv3 tc">
        <el-button
          class="ttu fw6"
          @click="onDisconnectClick"
          v-if="is_connected"
        >
          Disconnect from your {{service_name}} account
        </el-button>
      </div>
    </template>
    <div class="mv3 tc" v-else>
      <el-button
        class="ttu fw6"
        type="primary"
        @click="onConnectClick"
      >
        Authenticate your {{service_name}} account
      </el-button>
    </div>

    <div class="br2 bg-nearer-white mt4 pv3 ph4" v-if="is_box || is_dropbox || is_google_drive">
      <div class="center mw6">
        <div class="mb2 lh-copy ttu fw6 f6">
          Additional configuration
        </div>
        <el-form
          ref="form"
          class="el-form--compact el-form__label-tiny"
          label-position="top"
          :model="edit_connection_info"
          :rules="rules"
        >
          <el-form-item
            label="Base path"
            key="base_path"
            prop="base_path"
          >
            <el-input
              placeholder="/path/to/folder"
              spellcheck="false"
              v-model="edit_connection_info.base_path"
            />
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
  <div v-else>
    <p>To use this connection, you must first connect {{service_name}} to Flex.io.</p>
    <BuilderItemForm
      :item="form_json"
      :show-footer="false"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { HOSTNAME } from '@/constants/common'
  import { CONNECTION_STATUS_AVAILABLE } from '@/constants/connection-status'
  import * as ctypes from '@/constants/connection-type'
  import * as cinfos from '@/constants/connection-info'
  import BuilderItemForm from '@/components/BuilderItemForm'
  import MixinOauth from '@/components/mixins/oauth'
  import MixinConnection from '@/components/mixins/connection'

  // form definitons
  import amazons3 from '../data/connection/amazons3.yml'

  const getDefaultState = () => {
    return {
      emitting_update: false,
      forms: {
        amazons3
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
      BuilderItemForm
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
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      oauth_url() {
        var eid = _.get(this.connection, 'eid', '')
        return 'https://' + HOSTNAME + '/oauth2/connect' + '?service=' + this.ctype + '&eid=' + eid
      },
      form_json() {
        switch (this.ctype) {
          case ctypes.CONNECTION_TYPE_AMAZONS3:
            return this.forms.amazons3
        }
      }
    },
    methods: {
      initSelf() {
        if (this.emitting_update === true ) {
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
            this.github_url = 'https://github.com/' + `${owner}/${repository}`
          }
        }
      },
      emitUpdate() {
        this.emitting_update = true
        this.$emit('update:connection', this.edit_connection)
        this.$nextTick(() => { this.emitting_update = false })
      },
      updateEditConnection(attrs) {
        this.edit_connection = _.assign({}, this.edit_connection, attrs)
      },
      updateOwnerAndRepository() {
        var url = this.github_url.substring(this.github_url.indexOf('github.com/') + 11)
        var arr = url.split('/')
        if (arr.length == 2) {
          var attrs = {
            owner: arr[0],
            repository: arr[1]
          }
          this.edit_connection_info = _.assign({}, this.edit_connection_info, attrs)
        }
      },
      cinfo() {
        return _.find(cinfos, { connection_type: this.ctype })
      },
      onDisconnectClick() {
        /*
        var attrs = _.cloneDeep(this.connection)
        _.assign(attrs, { connection_info: this.edit_connection_info })
        this.tryDisconnect(attrs)
        */
      },
      onConnectClick() {
        this.tryOauthConnect()
      },
      tryDisconnect(attrs) {
        var eid = _.get(this.connection, 'eid', '')
        var team_name = this.active_team_name

        // disconnect from this connection (oauth only)
        this.$store.dispatch('connections/disconnect', { team_name, eid, attrs }).then(response => {
          var connection = response.data

          this.$message({
            message: "You've successfully disconnected from " + this.service_name + ".",
            type: 'success'
          })

          this.$emit('change', connection)
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

<style lang="stylus" scoped>
  .para
    margin: 6px 0 12px
    line-height: 1.5
</style>
