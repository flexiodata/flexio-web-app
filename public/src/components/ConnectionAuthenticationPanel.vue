<template>
  <div>
    <div v-if="is_oauth">
      <div v-if="!is_connected">
        <div class="lh-copy" v-if="">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
        <div class="mv3 tc">
          <el-button
            class="ttu b"
            type="primary"
            @click="onConnectClick"
          >
            Authenticate your {{service_name}} account
          </el-button>
        </div>
      </div>
      <div v-else>
        <div class="flex flex-row items-center justify-center lh-copy" v-if="is_connected">
          <i class="el-icon-success v-mid dark-green f3 mr2"></i>
          <span class="dn dib-ns">You are connected to {{service_name}}!</span>
        </div>
        <div class="mv3 tc">
          <el-button
            class="ttu b"
            @click="onDisconnectClick"
            v-if="is_connected"
          >
            Disconnect from your {{service_name}} account
          </el-button>
        </div>
      </div>
      <div class="br2 bg-near-white mt3 pa3" v-if="is_google_cloud_storage && is_connected">
        <div class="w-60-ns center">
          <el-form
            ref="form"
            class="flex flex-column el-form--compact el-form__label-tiny"
            label-position="top"
            :model="cinfo"
            :rules="rules"
          >
            <!-- google cloud storage -->
            <el-form-item
              label="What Google Cloud Storage bucket would you like to use?"
              key="bucket"
              prop="bucket"
              :class="getClass('bucket')"
              v-if="showInput('bucket')"
            >
              <el-input
                placeholder="Bucket"
                spellcheck="false"
                v-model="cinfo.bucket"
              />
            </el-form-item>
          </el-form>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="lh-copy">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
      <div class="w-two-thirds-ns center mt3">
        <el-form
          ref="form"
          class="flex flex-column el-form--compact"
          label-width="8rem"
          :model="cinfo"
          :rules="rules"
        >
          <!-- amazon s3 -->
          <el-form-item
            label="AWS Access Key"
            key="aws_key"
            prop="aws_key"
            :class="getClass('aws_key')"
            v-if="showInput('aws_key')"
          >
            <el-input
              placeholder="AWS Access Key"
              spellcheck="false"
              :autofocus="true"
              v-model="cinfo.aws_key"
            />
          </el-form-item>

          <!-- amazon s3 -->
          <el-form-item
            label="AWS Secret Key"
            key="aws_secret"
            prop="aws_secret"
            :class="getClass('aws_secret')"
            v-if="showInput('aws_secret')"
          >
            <el-input
              placeholder="AWS Secret Key"
              spellcheck="false"
              v-model="cinfo.aws_secret"
            />
          </el-form-item>

          <!-- amazon s3 -->
          <el-form-item
            label="Bucket"
            key="bucket"
            prop="bucket"
            :class="getClass('bucket')"
            v-if="showInput('bucket')"
          >
            <el-input
              placeholder="Bucket"
              spellcheck="false"
              v-model="cinfo.bucket"
            />
          </el-form-item>

          <!-- amazon s3 -->
          <el-form-item
            label="Region"
            key="region"
            prop="region"
            :class="getClass('region')"
            v-if="showInput('region')"
          >
            <el-select
              placeholder="Region"
              v-model="cinfo.region"
            >
              <el-option
                :label="option.label"
                :value="option.val"
                :key="option.val"
                v-for="option in region_options"
              />
            </el-select>
          </el-form-item>

          <!-- TODO: anyone? -->
          <el-form-item
            label="Token"
            key="token"
            prop="token"
            :class="getClass('token')"
            v-if="showInput('token')"
          >
            <el-input
              placeholder="Token"
              spellcheck="false"
              v-model="cinfo.token"
            />
          </el-form-item>

          <!-- smtp -->
          <el-form-item
            label="Email address"
            key="email"
            prop="email"
            spellcheck="false"
            :class="getClass('email')"
            v-if="showInput('email')"
          >
            <el-input
              placeholder="Email address"
              :autofocus="true"
              v-model="cinfo.email"
            />
          </el-form-item>

          <!-- smtp -->
          <el-form-item
            label="Security"
            key="security"
            prop="security"
            :class="getClass('security')"
            v-if="showInput('security')"
          >
            <el-select
              placeholder="Security"
              v-model="cinfo.security"
            >
              <el-option label="None" value="" />
              <el-option label="TLS" value="tls" />
              <el-option label="SSL" value="ssl" />
            </el-select>
          </el-form-item>

          <!-- shared -->
          <el-form-item
            label="Host"
            key="host"
            prop="host"
            :class="getClass('host')"
            v-if="showInput('host')"
          >
            <el-input
              placeholder="Host"
              spellcheck="false"
              v-model="cinfo.host"
            />
          </el-form-item>

          <!-- shared -->
          <el-form-item
            label="Port"
            key="port"
            prop="port"
            :class="getClass('port')"
            v-if="showInput('port')"
          >
            <el-input
              placeholder="Port"
              spellcheck="false"
              v-model="cinfo.port"
            />
          </el-form-item>

          <!-- shared -->
          <el-form-item
            label="Username"
            key="username"
            prop="username"
            :class="getClass('username')"
            v-if="showInput('username')"
          >
            <el-input
              placeholder="Username"
              spellcheck="false"
              v-model="cinfo.username"
            />
          </el-form-item>

          <!-- shared -->
          <el-form-item
            label="Password"
            key="password"
            prop="password"
            :class="getClass('password')"
            v-if="showInput('password')"
          >
            <el-input
              type="password"
              placeholder="Password"
              spellcheck="false"
              v-model="cinfo.password"
            />
          </el-form-item>

          <!-- shared -->
          <el-form-item
            label="Database"
            key="database"
            prop="database"
            :class="getClass('database')"
            v-if="showInput('database')"
          >
            <el-input
              placeholder="Database"
              spellcheck="false"
              v-model="cinfo.database"
            />
          </el-form-item>

          <!-- shared -->
          <el-form-item
            label="Base Path"
            key="base_path"
            prop="base_path"
            :class="getClass('base_path')"
            v-if="showInput('base_path')"
          >
            <el-input
              placeholder="Base Path"
              spellcheck="false"
              v-model="cinfo.base_path"
            />
          </el-form-item>

          <el-form-item class="order-last">
            <el-button
              class="ttu b"
              :type="test_btn_type"
              :icon="test_btn_icon"
              :loading="test_state == 'testing'"
              :disabled="test_state == 'success' || test_state == 'error'"
              @click="onTestClick"
            >
              {{test_btn_label}}
            </el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script>
  import { HOSTNAME } from '../constants/common'
  import {
    CONNECTION_STATUS_AVAILABLE,
    CONNECTION_STATUS_UNAVAILABLE,
    CONNECTION_STATUS_ERROR
  } from '../constants/connection-status'
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import MixinOauth from './mixins/oauth'

  const defaultConnectionInfo = () => {
    return {
      token: '',
      host: '',
      port: '',
      username: '',
      password: '',
      database: '',
      base_path: '',

      // aws
      aws_key: '',
      aws_secret: '',
      bucket: '',
      region: '',

      // smtp
      email: '',
      security: 'ssl'
    }
  }

  const region_options = [
    { val: 'us-east-2',      label: 'US East (Ohio)'            },
    { val: 'us-east-1',      label: 'US East (N. Virginia)'     },
    { val: 'us-west-1',      label: 'US West (N. California)'   },
    { val: 'us-west-2',      label: 'US West (Oregon)'          },
    { val: 'ca-central-1',   label: 'Canada (Central)'          },
    { val: 'ap-south-1',     label: 'Asia Pacific (Mumbai)'     },
    { val: 'ap-northeast-2', label: 'Asia Pacific (Seoul)'      },
    { val: 'ap-southeast-1', label: 'Asia Pacific (Singapore)'  },
    { val: 'ap-southeast-2', label: 'Asia Pacific (Sydney)'     },
    { val: 'ap-northeast-1', label: 'Asia Pacific (Tokyo)'      },
    { val: 'cn-northwest-1', label: 'China (Ningxia)'           },
    { val: 'eu-central-1',   label: 'EU (Frankfurt)'            },
    { val: 'eu-west-1',      label: 'EU (Ireland)'              },
    { val: 'eu-west-2',      label: 'EU (London)'               },
    { val: 'sa-east-1',      label: 'South America (São Paulo)' }
  ]

  export default {
    props: {
      connection: {
        type: Object,
        required: true
      },
      mode: {
        type: String,
        default: 'add'
      }
    },
    mixins: [MixinOauth],
    watch: {
      connection: {
        handler: 'reset',
        immediate: true,
        deep: true
      },
      cinfo: {
        handler: 'emitChange',
        immediate: true,
        deep: true
      }
    },
    data() {
      var cinfo = _.get(this.connection, 'connection_info', {})
      cinfo = _.assign({}, defaultConnectionInfo(), cinfo)

      if (this.mode == 'add') {
        cinfo.port = this.getDefaultPort()
        cinfo.region = 'us-east-1'
        cinfo.security = 'ssl'
      }

      return {
        cinfo,
        region_options,
        test_state: 'none', // none, testing, error, success
        rules: {
          email: [
            { required: true, message: 'Please input an email address', trigger: 'blur' },
            { type: 'email', message: 'Please input a valid email address', trigger: 'blur' }
          ]
        }
      }
    },
    computed: {
      eid() {
        return _.get(this.connection, 'eid', '')
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.connection, 'connection_status', '')
      },
      is_smtp() {
        return this.ctype == ctypes.CONNECTION_TYPE_SMTP
      },
      is_gmail() {
        return this.ctype == ctypes.CONNECTION_TYPE_GMAIL
      },
      is_google_cloud_storage() {
        return this.ctype == ctypes.CONNECTION_TYPE_GOOGLECLOUDSTORAGE
      },
      is_oauth() {
        switch (this.ctype)
        {
          case ctypes.CONNECTION_TYPE_BOX:
          case ctypes.CONNECTION_TYPE_DROPBOX:
          case ctypes.CONNECTION_TYPE_GITHUB:
          case ctypes.CONNECTION_TYPE_GMAIL:
          case ctypes.CONNECTION_TYPE_GOOGLECLOUDSTORAGE:
          case ctypes.CONNECTION_TYPE_GOOGLEDRIVE:
          case ctypes.CONNECTION_TYPE_GOOGLESHEETS:
            return true
        }
        return false
      },
      key_values() {
        if (this.is_google_cloud_storage) {
          return ['bucket']
        }

        if (this.is_oauth) {
          return []
        }

        switch (this.getConnectionType()) {
          default:
            return ['host', 'port', 'username', 'password', 'database']
          case ctypes.CONNECTION_TYPE_SFTP:
            return ['host', 'port', 'username', 'password', 'base_path']
          case ctypes.CONNECTION_TYPE_SMTP:
            return ['email', 'username', 'password', 'host', 'port', 'security']
          case ctypes.CONNECTION_TYPE_ELASTICSEARCH:
            return ['host', 'port', 'username', 'password']
          case ctypes.CONNECTION_TYPE_AMAZONS3:
            return ['aws_key', 'aws_secret', 'bucket', 'region']
          case ctypes.CONNECTION_TYPE_FIREBASE:
            return ['host', 'username', 'password']
          case ctypes.CONNECTION_TYPE_PIPELINEDEALS:
            return ['token']
          case ctypes.CONNECTION_TYPE_TWILIO:
            return ['username', 'password']
        }
      },
      connection_info() {
        return _.pick(this.cinfo, this.key_values)
      },
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      service_name() {
        return this.is_gmail ? 'Gmail' :
          this.is_smtp ? 'your email account' :
          _.result(this, 'getConnectionInfo.service_name', '')
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
      oauth_url() {
        var eid =  _.get(this, 'connection.eid', '')
        return 'https://' + HOSTNAME + '/oauth2/connect' + '?service=' + this.ctype + '&eid=' + eid
      }
    },
    methods: {
      validate(callback) {
        if (this.$refs.form) {
          this.$refs.form.validate(callback)
        } else {
          callback(true)
        }
      },
      getConnectionInfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      reset() {
        _.each(_.get(this.connection, 'connection_info', {}), (val, key) => {
          this.cinfo[key] = val
        })

        if (this.$refs.form) {
          this.$refs.form.clearValidate()
        }
      },
      emitChange() {
        this.test_state = 'none'
        this.$emit('change', { connection_info: this.connection_info })
      },
      getConnectionType() {
        return _.get(this, 'connection.connection_type', '')
      },
      getDefaultPort() {
        // we use a method here since we can't use computed values in the data()
        switch (this.getConnectionType()) {
          case ctypes.CONNECTION_TYPE_ELASTICSEARCH: return '443'
          case ctypes.CONNECTION_TYPE_MYSQL:         return '3306'
          case ctypes.CONNECTION_TYPE_POSTGRES:      return '5432'
          case ctypes.CONNECTION_TYPE_SFTP:          return '22'
        }
        return ''
      },
      getClass(key) {
        return 'order-' + this.key_values.indexOf(key)
      },
      showInput(key) {
        return _.includes(this.key_values, key)
      },
      onDisconnectClick() {
        var attrs = _.cloneDeep(this.connection)
        _.assign(attrs, { connection_info: this.connection_info })
        this.tryDisconnect(attrs)
      },
      onConnectClick() {
        this.tryOauthConnect()
      },
      onTestClick() {
        var attrs = _.cloneDeep(this.connection)
        _.assign(attrs, { connection_info: this.connection_info })
        this.tryTest(attrs)
      },
      tryDisconnect(attrs) {
        var eid = attrs.eid

        // disconnect from this connection (oauth only)
        this.$store.dispatch('disconnectConnection', { eid, attrs }).then(response => {
          if (response.ok) {
            this.$message({
              message: "You've successfully disconnected from " + this.service_name + ".",
              type: 'success'
            })

            this.$emit('change', response.body)
          } else {
            this.$message({
              message: _.get(response, 'data.error.message', ''),
              type: 'error'
            })
          }
        })
      },
      tryOauthConnect() {
        var eid = this.eid

        this.$_Oauth_showPopup(this.oauth_url, (params) => {
          // TODO: handle 'code' and 'state' and 'error' here...

          // for now, re-fetch the connection to update its state
          this.$store.dispatch('fetchConnection', { eid }).then(response => {
            if (response.ok) {
              this.$emit('change', _.omit(response.body, ['name', 'alias', 'description']))

              this.$nextTick(() => {
                if (this.is_connected) {
                  this.$message({
                    message: "You've successfully connected to " + this.service_name + "!",
                    type: 'success'
                  })
                }
              })
            } else {
              this.$message({
                message: _.get(response, 'data.error.message', ''),
                type: 'error'
              })
            }
          })
        })
      },
      tryTest(attrs) {
        var eid = attrs.eid
        //attrs = _.pick(attrs, ['name', 'alias', 'description', 'connection_info'])
        attrs = _.pick(attrs, ['name', 'description', 'connection_info'])

        // update the connection
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok) {
            this.test_state = 'testing'

            // test the connection
            this.$store.dispatch('testConnection', { eid, attrs }).then(response => {
              if (response.ok) {
                this.test_state = 'success'
                this.$emit('change', _.omit(response.body, ['name', 'alias', 'description', 'connection_info']))
              } else {
                this.test_state = 'error'
                setTimeout(() => { this.test_state = 'none' }, 4000)
              }
            })
          } else {
            this.test_state = 'error'
            setTimeout(() => { this.test_state = 'none' }, 4000)
          }
        })
      }
    }
  }
</script>
