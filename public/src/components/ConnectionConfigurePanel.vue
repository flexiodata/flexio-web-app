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
      <div class="flex flex-column w-50-ns center mt1 mb3" :class="form_cls">
        <ui-textbox
          class="css-condensed css-input-token"
          autocomplete="off"
          label="Token"
          floating-label
          v-model.trim="info.token"
          v-if="showInput('token')"
        ></ui-textbox>
        <ui-textbox
          class="css-condensed css-input-host"
          autocomplete="off"
          floating-label
          :label="host_label"
          v-model.trim="info.host"
          v-if="showInput('host')"
        ></ui-textbox>
        <ui-textbox
          class="css-condensed css-input-port"
          autocomplete="off"
          label="Port"
          floating-label
          v-model.trim.number="info.port"
          v-if="showInput('port')"
        ></ui-textbox>
        <ui-textbox
          class="css-condensed css-input-username"
          autocomplete="off"
          floating-label
          :label="username_label"
          v-model.trim="info.username"
          v-if="showInput('username')"
        ></ui-textbox>
        <ui-textbox
          type="password"
          class="css-condensed css-input-password"
          autocomplete="off"
          :label="password_label"
          floating-label
          v-model.trim="info.password"
          v-if="showInput('password')"
        ></ui-textbox>
        <ui-textbox
          class="css-condensed css-input-database"
          autocomplete="off"
          :label="database_label"
          floating-label
          v-model.trim="info.database"
          v-if="showInput('database') && !is_sftp && !is_elasticsearch"
        ></ui-textbox>
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
  import Btn from './Btn.vue'

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
      Btn
    },
    data() {
      var attrs = {
        token: _.get(this.connection, 'connection_info.token', ''),
        host: this.mode == 'edit' ? _.get(this.connection, 'connection_info.host', '') : this.getDefaultHost(),
        port: this.mode == 'edit' ? _.get(this.connection, 'connection_info.port', '') : this.getDefaultPort(),
        username: _.get(this.connection, 'connection_info.username', ''),
        password: _.get(this.connection, 'connection_info.password', ''),
        database: _.get(this.connection, 'connection_info.database', '')
      }

      switch (this.getConnectionType())
      {
        default:
          attrs = _.pick(attrs, ['host', 'port', 'username', 'password', 'database'])
          break;
        case types.CONNECTION_TYPE_AMAZONS3:
          attrs = _.pick(attrs, ['host', 'username', 'password', 'database'])
          break
        case types.CONNECTION_TYPE_FIREBASE:
          attrs = _.pick(attrs, ['host', 'username', 'password'])
          break
        case types.CONNECTION_TYPE_PIPELINEDEALS:
          attrs = _.pick(attrs, ['token'])
          break
      }

      return {
        info: attrs
      }
    },
    watch: {
      'info.token'()    { this.$emit('change', { connection_info: this.info }) },
      'info.host'()     { this.$emit('change', { connection_info: this.info }) },
      'info.port'()     { this.$emit('change', { connection_info: this.info }) },
      'info.username'() { this.$emit('change', { connection_info: this.info }) },
      'info.password'() { this.$emit('change', { connection_info: this.info }) },
      'info.database'() { this.$emit('change', { connection_info: this.info }) }
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
      form_cls() {
        return { 'css-amazon-s3': this.is_s3 }
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
        return this.is_s3 ? 'Region'     : 'Host'
      },
      username_label() {
        return this.is_s3 ? 'Access Key' : 'Username'
      },
      password_label() {
        return this.is_s3 ? 'Secret Key' : 'Password'
      },
      database_label() {
        return this.is_s3 ? 'Bucket' : 'Database'
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      getConnectionType() {
        return _.get(this, 'connection.connection_type', '')
      },
      getDefaultHost() {
        // we use a method here since we can't use computed values in the data()
        switch (this.getConnectionType())
        {
          case types.CONNECTION_TYPE_AMAZONS3: return 'us-east-1'
        }
        return ''
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

<style lang="less" scoped>
  .css-amazon-s3 {
    .css-input-username { order: 1; }
    .css-input-password { order: 2; }
    .css-input-database { order: 3; }
    .css-input-host     { order: 4; }
    .css-btn-test       { order: 5; }
  }
</style>
