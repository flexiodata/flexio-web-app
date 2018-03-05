<template>
  <div>
    <div v-if="is_oauth">
      <div v-if="is_connected">
        <div class="flex flex-row items-center mv1 lh-copy">
          <i class="el-icon-success v-mid dark-green f3 mr2"></i>
          <span class="dn dib-ns">You've successfully connected to {{service_name}}!</span>
        </div>
        <div class="mv3 tc">
          <el-button class="ttu b" type="plain" @click="onDisconnectClick">Disconnect from your {{service_name}} account</el-button>
        </div>
      </div>
      <div v-else>
        <div class="mv1 lh-copy">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
        <div class="mv3 tc">
          <el-button class="ttu b" type="primary" @click="onConnectClick">Authenticate your {{service_name}} account</el-button>
        </div>
      </div>
    </div>
    <div v-else>
      <ui-alert type="success" :dismissible="false" v-show="is_connected && error_msg.length == 0">
        You've successfully connected to {{service_name}}!
      </ui-alert>
      <ui-alert type="error" :dismissible="true" @dismiss="error_msg = ''" v-show="error_msg.length > 0">
        {{error_msg}}
      </ui-alert>
      <div class="lh-copy">To use this connection, you must first connect {{service_name}} to Flex.io.</div>
      <div class="flex flex-column w-50-ns center mt1 mb3">
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="AWS Access Key"
          floating-label
          v-model.trim="info.aws_key"
          v-if="showInput('aws_key')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="AWS Secret Key"
          floating-label
          v-model.trim="info.aws_secret"
          v-if="showInput('aws_secret')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Bucket"
          floating-label
          v-model.trim="info.bucket"
          v-if="showInput('bucket')"
        />
        <value-select
          autocomplete="off"
          label="Region"
          floating-label
          :options="aws_region_options"
          v-model.trim="info.region"
          v-if="showInput('region')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Token"
          floating-label
          v-model.trim="info.token"
          v-if="showInput('token')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          floating-label
          :label="host_label"
          v-model.trim="info.host"
          v-if="showInput('host')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          label="Port"
          floating-label
          v-model.trim.number="info.port"
          v-if="showInput('port')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          floating-label
          :label="username_label"
          v-model.trim="info.username"
          v-if="showInput('username')"
        />
        <ui-textbox
          type="password"
          spellcheck="false"
          autocomplete="off"
          :label="password_label"
          floating-label
          v-model.trim="info.password"
          v-if="showInput('password')"
        />
        <ui-textbox
          autocomplete="off"
          spellcheck="false"
          :label="database_label"
          floating-label
          v-model.trim="info.database"
          v-if="showInput('database') && !is_sftp && !is_elasticsearch"
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
  import Btn from './Btn.vue'
  import OauthPopup from './mixins/oauth-popup'

  const aws_region_options = [
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
      ValueSelect,
      Btn
    },
    watch: {
      'info.token'()      { this.$emit('change', { connection_info: this.info }) },
      'info.host'()       { this.$emit('change', { connection_info: this.info }) },
      'info.port'()       { this.$emit('change', { connection_info: this.info }) },
      'info.username'()   { this.$emit('change', { connection_info: this.info }) },
      'info.password'()   { this.$emit('change', { connection_info: this.info }) },
      'info.database'()   { this.$emit('change', { connection_info: this.info }) },

      'info.aws_key'()    { this.$emit('change', { connection_info: this.info }) },
      'info.aws_secret'() { this.$emit('change', { connection_info: this.info }) },
      'info.bucket'()     { this.$emit('change', { connection_info: this.info }) },
      'info.region'()     { this.$emit('change', { connection_info: this.info }) }
    },
    data() {
      var c = this.connection

      var attrs = {
        token: _.get(c, 'connection_info.token', ''),
        host: _.get(c, 'connection_info.host', ''),
        port: this.mode == 'edit' ? _.get(c, 'connection_info.port', '') : this.getDefaultPort(),
        username: _.get(c, 'connection_info.username', ''),
        password: _.get(c, 'connection_info.password', ''),
        database: _.get(c, 'connection_info.database', ''),

        // aws
        aws_key: _.get(c, 'connection_info.aws_key', ''),
        aws_secret: _.get(c, 'connection_info.aws_secret', ''),
        bucket: _.get(c, 'connection_info.bucket', ''),
        region: this.mode == 'edit' ? _.get(c, 'connection_info.region', '') : this.getDefaultRegion()
      }

      switch (this.getConnectionType())
      {
        default:
          attrs = _.pick(attrs, ['host', 'port', 'username', 'password', 'database'])
          break;
        case ctypes.CONNECTION_TYPE_AMAZONS3:
          attrs = _.pick(attrs, ['aws_key', 'aws_secret', 'bucket', 'region'])
          break
        case ctypes.CONNECTION_TYPE_FIREBASE:
          attrs = _.pick(attrs, ['host', 'username', 'password'])
          break
        case ctypes.CONNECTION_TYPE_PIPELINEDEALS:
          attrs = _.pick(attrs, ['token'])
          break
        case ctypes.CONNECTION_TYPE_TWILIO:
          attrs = _.pick(attrs, ['username', 'password'])
          break
      }

      return {
        error_msg: '',
        info: attrs
      }
    },
    computed: {
      eid() {
        return _.get(this, 'connection.eid', '')
      },
      ctype() {
        return this.getConnectionType()
      },
      cstatus() {
        return _.get(this, 'connection.connection_status', '')
      },
      is_connected() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      is_s3() {
        return this.ctype == ctypes.CONNECTION_TYPE_AMAZONS3
      },
      is_sftp() {
        return this.ctype == ctypes.CONNECTION_TYPE_SFTP
      },
      is_elasticsearch() {
        return this.ctype == ctypes.CONNECTION_TYPE_ELASTICSEARCH
      },
      cls() {
        return this.is_connected ? 'b--dark-green' : 'b--blue'
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      aws_region_options() {
        return aws_region_options
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
      },
      host_label() {
        return 'Host'
      },
      username_label() {
        return 'Username'
      },
      password_label() {
        return 'Password'
      },
      database_label() {
        return 'Database'
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
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
          case ctypes.CONNECTION_TYPE_ELASTICSEARCH: return '9200'
          case ctypes.CONNECTION_TYPE_MYSQL:         return '3306'
          case ctypes.CONNECTION_TYPE_POSTGRES:      return '5432'
          case ctypes.CONNECTION_TYPE_SFTP:          return '22'
        }
        return ''
      },
      showInput(key) {
        return _.has(this.info, key)
      },
      onDisconnectClick() {
        this.tryDisconnect(_.assign({}, this.connection, this.info))
      },
      onConnectClick() {
        this.tryOauthConnect(this.info)
      },
      onTestClick() {
        this.tryTest(_.assign({}, this.connection, this.info))
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
              this.$emit('change', _.omit(response.body, ['name', 'ename', 'description']))
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
        //attrs = _.pick(attrs, ['name', 'ename', 'description', 'connection_info'])
        attrs = _.pick(attrs, ['name', 'description', 'connection_info'])

        // update the connection
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            // test the connection
            this.$store.dispatch('testConnection', { eid, attrs }).then(response => {
              if (response.ok)
              {
                this.$emit('change', _.omit(response.body, ['name', 'ename', 'description', 'connection_info']))
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
