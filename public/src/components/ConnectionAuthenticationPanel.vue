<template>
  <div>
    <div v-if="is_oauth">
      <div v-if="is_connected">
        <div class="flex flex-row items-center lh-copy">
          <i class="el-icon-success v-mid dark-green f3 mr2"></i>
          <span class="dn dib-ns">You've successfully connected to {{service_name}}!</span>
        </div>
        <div class="mt3 tc">
          <el-button class="ttu b" type="plain" @click="onDisconnectClick">Disconnect from your {{service_name}} account</el-button>
        </div>
      </div>
      <div v-else>
        <div class="lh-copy">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
        <div class="mt3 tc">
          <el-button class="ttu b" type="primary" @click="onConnectClick">Authenticate your {{service_name}} account</el-button>
        </div>
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
      <div class="flex flex-column w-50-ns center mt3">
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="AWS Access Key"
          floating-label
          help=" "
          v-model.trim="aws_key"
          v-if="showInput('aws_key')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="AWS Secret Key"
          floating-label
          help=" "
          v-model.trim="aws_secret"
          v-if="showInput('aws_secret')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Bucket"
          floating-label
          help=" "
          v-model.trim="bucket"
          v-if="showInput('bucket')"
        />
        <value-select
          autocomplete="off"
          label="Region"
          floating-label
          help=" "
          :options="region_options"
          v-model.trim="region"
          v-if="showInput('region')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Token"
          floating-label
          help=" "
          v-model.trim="token"
          v-if="showInput('token')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          floating-label
          help=" "
          label="Host"
          v-model.trim="host"
          v-if="showInput('host')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Port"
          floating-label
          help=" "
          v-model.trim.number="port"
          v-if="showInput('port')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          floating-label
          help=" "
          label="Username"
          v-model.trim="username"
          v-if="showInput('username')"
        />
        <ui-textbox
          type="password"
          spellcheck="false"
          autocomplete="off"
          label="Password"
          floating-label
          help=" "
          v-model.trim="password"
          v-if="showInput('password')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Database"
          floating-label
          help=" "
          v-model.trim="database"
          v-if="showInput('database')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Base Path"
          floating-label
          help=" "
          v-model.trim="base_path"
          v-if="showInput('base_path')"
        />
        <div class="mt3">
          <el-button class="ttu b" type="primary" @click="onTestClick">Test connection</el-button>
        </div>
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
      region: ''
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

      aws_key()    { this.emitChange() },
      aws_secret() { this.emitChange() },
      bucket()     { this.emitChange() },
      region()     { this.emitChange() }
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
        region: this.mode == 'edit' ? _.get(c, 'region', '') : this.getDefaultRegion()
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
        return _.result(this, 'cinfo.service_name', '')
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
      oauth_url() {
        var eid =  _.get(this, 'connection.eid', '')
        var base_url = 'https://'+HOSTNAME+'/a/connectionauth'

        switch (this.ctype)
        {
          case ctypes.CONNECTION_TYPE_BOX:          return base_url+'?service=box&eid='+eid
          case ctypes.CONNECTION_TYPE_DROPBOX:      return base_url+'?service=dropbox&eid='+eid
          case ctypes.CONNECTION_TYPE_GITHUB:       return base_url+'?service=github&eid='+eid
          case ctypes.CONNECTION_TYPE_GOOGLEDRIVE:  return base_url+'?service=googledrive&eid='+eid
          case ctypes.CONNECTION_TYPE_GOOGLESHEETS: return base_url+'?service=googlesheets&eid='+eid
        }

        return ''
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      reset() {
        _.each(_.get(this.connection, 'connection_info', {}), (val, key) => {
          this[key] = val
        })
      },
      emitChange() {
        this.$emit('change', { connection_info: this.connection_info })
      },
      getConnectionType() {
        return _.get(this, 'connection.connection_type', '')
      },
      getDefaultRegion() {
        return 'us-east-1'
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
