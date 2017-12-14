<template>
  <div>
    <div v-if="is_oath">
      <div v-if="is_connected">
        <div class="lh-copy">
          <i class="material-icons v-mid dark-green">check_circle</i>
          <span class="dn dib-ns">You've successfully connected to {{service_name}}!</span>
        </div>
        <div class="mt3 mb2 tc">
          <btn btn-lg btn-outline class="b--black-20 bg-white" @click="onDisconnectClick">Disconnect from your {{service_name}} account</btn>
        </div>
      </div>
      <div v-else>
        <div class="lh-copy">To use this connection, you must connect {{service_name}} to Flex.io.</div>
        <div class="mt3 mb2 tc">
          <btn btn-lg btn-primary @click="onConnectClick">Authenticate your {{service_name}} account</btn>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="lh-copy" v-if="is_connected">
        <i class="material-icons v-mid dark-green">check_circle</i>
        <span class="dn dib-ns">You've successfully connected to {{service_name}}!</span>
      </div>
      <div class="lh-copy" v-else>To use this connection, you must connect {{service_name}} to Flex.io.</div>
      <div class="flex flex-column w-50-ns center mt1 mb3">
        <ui-textbox
          autocomplete="off"
          label="AWS Access Key"
          floating-label
          v-model.trim="info.aws_key"
          v-if="showInput('aws_key')"
        />
        <ui-textbox
          autocomplete="off"
          label="AWS Secret Key"
          floating-label
          v-model.trim="info.aws_secret"
          v-if="showInput('aws_secret')"
        />
        <ui-textbox
          autocomplete="off"
          label="Bucket"
          floating-label
          v-model.trim="info.bucket"
          v-if="showInput('bucket')"
        />
        <value-select
          autocomplete="off"
          label="Region"
          floating-label
          :options="region_options"
          v-model.trim="info.region"
          v-if="showInput('region')"
        />
        <ui-textbox
          autocomplete="off"
          label="Token"
          floating-label
          v-model.trim="info.token"
          v-if="showInput('token')"
        />
        <ui-textbox
          autocomplete="off"
          floating-label
          :label="host_label"
          v-model.trim="info.host"
          v-if="showInput('host')"
        />
        <ui-textbox
          autocomplete="off"
          label="Port"
          floating-label
          v-model.trim.number="info.port"
          v-if="showInput('port')"
        />
        <ui-textbox
          autocomplete="off"
          floating-label
          :label="username_label"
          v-model.trim="info.username"
          v-if="showInput('username')"
        />
        <ui-textbox
          type="password"
          autocomplete="off"
          :label="password_label"
          floating-label
          v-model.trim="info.password"
          v-if="showInput('password')"
        />
        <ui-textbox
          autocomplete="off"
          :label="database_label"
          floating-label
          v-model.trim="info.database"
          v-if="showInput('database') && !is_sftp && !is_elasticsearch"
        />
        <div class="mt3 css-btn-test">
          <btn btn-lg btn-primary class="w-100 ttu b" @click="onTestClick">Test connection</btn>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {
    CONNECTION_STATUS_AVAILABLE,
    CONNECTION_STATUS_UNAVAILABLE,
    CONNECTION_STATUS_ERROR
  } from '../constants/connection-status'
  import * as types from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import ValueSelect from './ValueSelect.vue'
  import Btn from './Btn.vue'

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
    components: {
      ValueSelect,
      Btn
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
        case types.CONNECTION_TYPE_AMAZONS3:
          attrs = _.pick(attrs, ['aws_key', 'aws_secret', 'bucket', 'region'])
          break
        case types.CONNECTION_TYPE_FIREBASE:
          attrs = _.pick(attrs, ['host', 'username', 'password'])
          break
        case types.CONNECTION_TYPE_PIPELINEDEALS:
          attrs = _.pick(attrs, ['token'])
          break
      }

      return {
        info: attrs,
        region_options
      }
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
    computed: {
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
        return this.ctype == types.CONNECTION_TYPE_AMAZONS3
      },
      is_sftp() {
        return this.ctype == types.CONNECTION_TYPE_SFTP
      },
      is_elasticsearch() {
        return this.ctype == types.CONNECTION_TYPE_ELASTICSEARCH
      },
      cls() {
        return this.is_connected ? 'b--dark-green' : 'b--blue'
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      is_oath() {
        switch (this.ctype)
        {
          case types.CONNECTION_TYPE_BOX:
          case types.CONNECTION_TYPE_DROPBOX:
          case types.CONNECTION_TYPE_GOOGLEDRIVE:
          case types.CONNECTION_TYPE_GOOGLESHEETS:
            return true
        }
        return false
      },
      host_label()     {
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
          case types.CONNECTION_TYPE_ELASTICSEARCH: return '9200'
          case types.CONNECTION_TYPE_MYSQL:         return '3306'
          case types.CONNECTION_TYPE_POSTGRES:      return '5432'
          case types.CONNECTION_TYPE_SFTP:          return '22'
        }
        return ''
      },
      showInput(key) {
        return _.has(this.info, key)
      },
      onDisconnectClick() {
        this.$emit('disconnect', _.assign({}, this.connection, this.info))
      },
      onConnectClick() {
        this.$emit('connect', this.info)
      },
      onTestClick() {
        this.$emit('test', _.assign({}, this.connection, this.info))
      }
    }
  }
</script>
