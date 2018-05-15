<template>
  <div>
    <div v-if="is_oauth && is_connected">
      <div class="flex flex-row items-center lh-copy">
        <i class="el-icon-success v-mid dark-green f3 mr2"></i>
        <span class="dn dib-ns">You've successfully connected to {{service_name}}!</span>
      </div>
      <div class="mt3 tc">
        <el-button
          class="ttu b"
          @click="onDisconnectClick"
        >
          Disconnect from your {{service_name}} account
        </el-button>
      </div>
    </div>
    <div v-else-if="is_oauth && !is_connected">
      <div class="lh-copy">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
      <div class="mt3 tc">
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
      <ui-alert type="success" :dismissible="false" v-show="is_tested && is_connected && error_msg.length == 0">
        You've successfully connected to {{service_name}}!
      </ui-alert>
      <ui-alert type="error" :dismissible="true" @dismiss="error_msg = ''" v-show="error_msg.length > 0">
        {{error_msg}}
      </ui-alert>
      <div class="lh-copy">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
      <div
        class="w-two-thirds-ns center mt4"
        v-if="is_smtp"
      >
        <el-form
          ref="form"
          class="el-form-compact"
          label-width="8rem"
          :model="$data"
          :rules="rules"
        >
          <el-form-item
            label="Email address"
            key="email"
            prop="email"
            spellcheck="false"
          >
            <el-input
              placeholder="Email address"
              :autofocus="true"
              v-model="email"
            />
          </el-form-item>
          <el-form-item
            label="Username"
            key="username"
            prop="username"
          >
            <el-input
              placeholder="Username"
              v-model="username"
            />
          </el-form-item>
          <el-form-item
            label="Password"
            key="password"
            prop="password"
          >
            <el-input
              type="password"
              placeholder="Password"
              v-model="password"
            />
          </el-form-item>
          <el-form-item
            label="Host"
            key="host"
            prop="host"
          >
            <el-input
              placeholder="Host"
              v-model="host"
            />
          </el-form-item>
          <el-form-item
            label="Port"
            key="port"
            prop="port"
          >
            <el-input
              placeholder="Port"
              v-model="port"
            />
          </el-form-item>
          <el-form-item
            label="Security"
            key="security"
            prop="security"
          >
            <el-select
              placeholder="Security"
              v-model="security"
            >
              <el-option label="None" value="" />
              <el-option label="TLS" value="tls" />
              <el-option label="SSL" value="ssl" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button
              class="ttu b"
              type="primary"
              @click="onTestClick"
            >
              Test connection
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      <div
        class="w-two-thirds-ns center mt4"
        v-else
      >
        <el-form
          class="el-form-compact"
          label-width="8rem"
          :model="$data"
        >
          <el-form-item
            label="AWS Access Key"
            key="aws_key"
            prop="aws_key"
            v-if="showInput('aws_key')"
          >
            <el-input
              placeholder="AWS Access Key"
              spellcheck="false"
              :autofocus="true"
              v-model="aws_key"
            />
          </el-form-item>
          <el-form-item
            label="AWS Secret Key"
            key="aws_secret"
            prop="aws_secret"
            v-if="showInput('aws_secret')"
          >
            <el-input
              placeholder="AWS Secret Key"
              spellcheck="false"
              v-model="aws_secret"
            />
          </el-form-item>
          <el-form-item
            label="Bucket"
            key="bucket"
            prop="bucket"
            v-if="showInput('bucket')"
          >
            <el-input
              placeholder="Bucket"
              spellcheck="false"
              v-model="bucket"
            />
          </el-form-item>
          <el-form-item
            label="Region"
            key="region"
            prop="region"
            v-if="showInput('region')"
          >
            <el-select
              placeholder="Region"
              v-model="region"
            >
              <el-option
                :label="option.label"
                :value="option.val"
                :key="option.val"
                v-for="option in region_options"
              />
            </el-select>
          </el-form-item>
          <el-form-item
            label="Token"
            key="token"
            prop="token"
            v-if="showInput('token')"
          >
            <el-input
              placeholder="Token"
              spellcheck="false"
              v-model="token"
            />
          </el-form-item>
          <el-form-item
            label="Host"
            key="host"
            prop="host"
            v-if="showInput('host')"
          >
            <el-input
              placeholder="Host"
              spellcheck="false"
              v-model="host"
            />
          </el-form-item>
          <el-form-item
            label="Post"
            key="port"
            prop="port"
            v-if="showInput('port')"
          >
            <el-input
              placeholder="Post"
              spellcheck="false"
              v-model="port"
            />
          </el-form-item>
          <el-form-item
            label="Username"
            key="username"
            prop="username"
            v-if="showInput('username')"
          >
            <el-input
              placeholder="Username"
              spellcheck="false"
              v-model="username"
            />
          </el-form-item>
          <el-form-item
            label="Password"
            key="password"
            prop="password"
            v-if="showInput('password')"
          >
            <el-input
              type="password"
              placeholder="Password"
              spellcheck="false"
              v-model="password"
            />
          </el-form-item>
          <el-form-item
            label="Database"
            key="database"
            prop="database"
            v-if="showInput('database')"
          >
            <el-input
              placeholder="Database"
              spellcheck="false"
              v-model="database"
            />
          </el-form-item>
          <el-form-item
            label="Base Path"
            key="base_path"
            prop="base_path"
            v-if="showInput('base_path')"
          >
            <el-input
              placeholder="Base Path"
              spellcheck="false"
              v-model="base_path"
            />
          </el-form-item>
          <el-form-item>
            <el-button
              class="ttu b"
              type="primary"
              @click="onTestClick"
            >
              Test connection
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
  import ValueSelect from './ValueSelect.vue'
  import OauthPopup from './mixins/oauth-popup'

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
      'connection': {
        type: Object,
        required: true
      },
      'mode': {
        type: String,
        default: 'add'
      }
    },
    mixins: [OauthPopup],
    components: {
      ValueSelect
    },
    watch: {
      connection() { this.reset() },

      token()      { this.emitChange() },
      host()       { this.emitChange() },
      port()       { this.emitChange() },
      username()   { this.emitChange() },
      password()   { this.emitChange() },
      database()   { this.emitChange() },
      base_path()  { this.emitChange() },

      // aws
      aws_key()    { this.emitChange() },
      aws_secret() { this.emitChange() },
      bucket()     { this.emitChange() },
      region()     { this.emitChange() },

      // smtp
      email()      { this.emitChange() },
      security()   { this.emitChange() }
    },
    data() {
      var c = _.get(this.connection, 'connection_info', {})
      c = _.assign({}, defaultConnectionInfo(), c)

      return {
        error_msg: '',
        is_tested: false,
        region_options,

        token: _.get(c, 'token', ''),
        host: _.get(c, 'host', ''),
        port: this.mode == 'edit' ? _.get(c, 'port', '') : this.getDefaultPort(),
        username: _.get(c, 'username', ''),
        password: _.get(c, 'password', ''),
        database: _.get(c, 'database', ''),
        base_path: _.get(c, 'base_path', ''),

        // aws
        aws_key: _.get(c, 'aws_key', ''),
        aws_secret: _.get(c, 'aws_secret', ''),
        bucket: _.get(c, 'bucket', ''),
        region: this.mode == 'edit' ? _.get(c, 'region', '') : 'us-east-1',

        // email
        email: _.get(c, 'email', ''),
        security: this.mode == 'edit' ? _.get(c, 'security', '') : 'ssl',

        rules: {
          email: [
            { required: true, message: 'Please input an email address' },
            { type: 'email', message: 'Please enter a valid email address', trigger: 'blur' }
          ]
        }
      }
    },
    computed: {
      eid() {
        return _.get(this, 'connection.eid', '')
      },
      ctype() {
        return _.get(this, 'connection.connection_type', '')
      },
      cstatus() {
        return _.get(this, 'connection.connection_status', '')
      },
      connection_info() {
        return _.pick(this.$data, this.key_values)
      },
      key_values() {
        switch (this.getConnectionType())
        {
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
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      cls() {
        return this.is_connected ? 'b--dark-green' : 'b--blue'
      },
      service_name() {
        return this.is_gmail ? 'Gmail' :
          this.is_smtp ? 'your email account' :
          _.result(this, 'cinfo.service_name', '')
      },
      is_smtp() {
        return this.ctype == ctypes.CONNECTION_TYPE_SMTP
      },
      is_gmail() {
        return this.ctype == ctypes.CONNECTION_TYPE_GMAIL
      },
      is_oauth() {
        switch (this.ctype)
        {
          case ctypes.CONNECTION_TYPE_BOX:
          case ctypes.CONNECTION_TYPE_DROPBOX:
          case ctypes.CONNECTION_TYPE_GITHUB:
          case ctypes.CONNECTION_TYPE_GMAIL:
          case ctypes.CONNECTION_TYPE_GOOGLEDRIVE:
          case ctypes.CONNECTION_TYPE_GOOGLESHEETS:
            return true
        }
        return false
      },
      oauth_url() {
        var eid =  _.get(this, 'connection.eid', '')
        var base_url = 'https://'+HOSTNAME+'/a/connectionauth'

        switch (this.ctype)
        {
          case ctypes.CONNECTION_TYPE_BOX:          return base_url+'?service=box&eid='+eid
          case ctypes.CONNECTION_TYPE_DROPBOX:      return base_url+'?service=dropbox&eid='+eid
          case ctypes.CONNECTION_TYPE_GITHUB:       return base_url+'?service=github&eid='+eid
          case ctypes.CONNECTION_TYPE_GMAIL:        return base_url+'?service=gmail&eid='+eid
          case ctypes.CONNECTION_TYPE_GOOGLEDRIVE:  return base_url+'?service=googledrive&eid='+eid
          case ctypes.CONNECTION_TYPE_GOOGLESHEETS: return base_url+'?service=googlesheets&eid='+eid
        }

        return ''
      }
    },
    mounted() {
      // TODO: this is crude, but it's the best way right now of getting the connection info
      //       back to the connection edit panel (even if we never edit anything)
      this.reset()
      this.$nextTick(() => { this.emitChange() })
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      reset() {
        _.each(_.get(this.connection, 'connection_info', {}), (val, key) => {
          this[key] = val
        })

        if (this.$refs.form) {
          this.$refs.form.clearValidate()
        }
      },
      emitChange() {
        this.$emit('change', { connection_info: this.connection_info })
      },
      getConnectionType() {
        return _.get(this, 'connection.connection_type', '')
      },
      getDefaultPort() {
        // we use a method here since we can't use computed values in the data()
        switch (this.getConnectionType())
        {
          case ctypes.CONNECTION_TYPE_ELASTICSEARCH: return '443'
          case ctypes.CONNECTION_TYPE_MYSQL:         return '3306'
          case ctypes.CONNECTION_TYPE_POSTGRES:      return '5432'
          case ctypes.CONNECTION_TYPE_SFTP:          return '22'
        }
        return ''
      },
      showInput(key) {
        return _.includes(this.key_values, key)
      },
      onDisconnectClick() {
        this.tryDisconnect(_.assign({}, this.connection, { connection_info: this.connection_info }))
      },
      onConnectClick() {
        this.tryOauthConnect()
      },
      onTestClick() {
        this.tryTest(_.assign({}, this.connection, { connection_info: this.connection_info }))
      },
      tryDisconnect(attrs) {
        var eid = attrs.eid

        // disconnect from this connection (oauth only)
        this.$store.dispatch('disconnectConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            this.error_msg = ''
            this.$emit('change', response.body)
          }
           else
          {
            this.error_msg = _.get(response, 'body.error.message', '')
          }
        })
      },
      tryOauthConnect() {
        var eid = this.eid

        this.oauthPopup(this.oauth_url, (params) => {
          // TODO: handle 'code' and 'state' and 'error' here...

          // for now, re-fetch the connection to update its state
          this.$store.dispatch('fetchConnection', { eid }).then(response => {
            if (response.ok)
            {
              this.error_msg = ''
              this.$emit('change', _.omit(response.body, ['name', 'alias', 'description']))
            }
             else
            {
              this.error_msg = _.get(response, 'body.error.message', '')
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
          this.is_tested = true
          setTimeout(() => { this.is_tested = false }, 4000)

          if (response.ok)
          {
            // test the connection
            this.$store.dispatch('testConnection', { eid, attrs }).then(response => {
              if (response.ok)
              {
                this.error_msg = ''
                this.$emit('change', _.omit(response.body, ['name', 'alias', 'description', 'connection_info']))
              }
               else
              {
                this.error_msg = _.get(response, 'body.error.message', '')
              }
            })
          }
           else
          {
            this.error_msg = _.get(response, 'body.error.message', '')
          }
        })
      }
    }
  }
</script>
