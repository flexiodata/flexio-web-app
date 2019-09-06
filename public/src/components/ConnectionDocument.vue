<template>
  <div>
    <!-- header -->
    <div class="flex flex-row">
      <div class="flex-fill flex flex-column flex-row-l">
        <div class="flex-fill flex flex-row mr3-l mb2 mb0-l">
          <ServiceIcon class="flex-none mt1 br2 square-4" :type="ctype" :url="url" :empty-cls="''" />
          <div class="flex-fill flex flex-column" style="margin-left: 12px">
            <div class="f3 fw6 lh-title">{{connection.name}}</div>
            <div class="f6 fw4 mt1 lh-copy silver" v-if="cdesc.length > 0">{{cdesc}}</div>
            <div class="f6 fw4 mt1 lh-copy moon-gray" v-else><em>(No description)</em></div>
          </div>
        </div>
        <div class="flex-fill flex flex-column mb2 mb0-l">
          <table>
            <colgroup>
              <col>
              <col class="w-100">
            </colgroup>
            <tbody>
              <tr>
                <td class="nowrap">Connection Type</td>
                <td>
                  <div class="flex flex-row items-center">
                    <ServiceIcon class="flex-none mr1 br1 square-1" :type="ctype" :url="url" :empty-cls="''" />
                    <span class="f6 fw6">{{service_name}}</span>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Status</td>
                <td>
                  <div class="flex flex-row items-center">
                    <i class="el-icon-success dark-green mr1" v-if="is_available"></i>
                    <i class="el-icon-error dark-red mr1" v-else></i>
                    <span class="f6 fw6">{{is_available ? 'Connected' : 'Not Connected'}}</span>
                  </div>
                </td>
              </tr>
              <tr v-if="has_owner && has_repository">
                <td>Repository</td>
                <td><span class="f6 fw6">{{owner}}/{{repository}}</span></td>
              </tr>
              <tr v-if="has_basepath">
                <td>Base Path</td>
                <td class="f6 moon-gray" v-if="base_path.length == 0"><em>(root folder)</em></td>
                <td class="f6 fw6" v-else>{{base_path}}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="flex-none">
        <el-button
          class="ttu fw6"
          style="min-width: 5rem"
          size="small"
          type="primary"
          @click="onEditClick"
          v-require-rights:connection.update
        >
          Edit
        </el-button>
      </div>
    </div>

    <!-- content -->
    <template v-if="is_keyring_connection">
      <div class="mv3 bt bw1 b--black-05"></div>
      <div class="mb2 lh-copy ttu fw6 f6">Keypair Values</div>
      <JsonDetailsPanel
        :json="connection.connection_info"
      />
    </template>
    <FileChooser
      class="mt3"
      :connection="connection"
      v-if="is_storage_connection"
    />
  </div>
</template>

<script>
  import { CONNECTION_STATUS_AVAILABLE } from '@/constants/connection-status'
  import { CONNECTION_TYPE_KEYRING } from '@/constants/connection-type'
  import * as ctypes from '@/constants/connection-type'
  import * as connections from '@/constants/connection-info'
  import ServiceIcon from '@/components/ServiceIcon'
  import FileChooser from '@/components/FileChooser'
  import JsonDetailsPanel from '@/components/JsonDetailsPanel'
  import MixinConnection from '@/components/mixins/connection'

  export default {
    props: {
      connectionEid: {
        type: String,
        required: true
      }
    },
    mixins: [MixinConnection],
    components: {
      ServiceIcon,
      FileChooser,
      JsonDetailsPanel,
    },
    computed: {
      connection() {
        return _.get(this.$store.state.connections, `items.${this.connectionEid}`, {})
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cdesc() {
        var word_count = 50
        var desc = _.get(this.connection, 'description', '')
        var desc_arr = desc.split(' ')
        return desc_arr.length <= word_count ? desc_arr.join(' ') : desc_arr.slice(0, word_count).join(' ') + '...'
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.connection, 'connection_status', '')
      },
      is_available() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      is_keyring_connection() {
        return this.ctype == CONNECTION_TYPE_KEYRING
      },
      is_storage_connection() {
        return this.$_Connection_isStorage(this.connection)
      },
      url() {
        return _.get(this.connection, 'connection_info.url', '')
      },
      owner() {
        return _.get(this.connection, 'connection_info.owner')
      },
      repository() {
        return _.get(this.connection, 'connection_info.repository')
      },
      base_path() {
        return _.get(this.connection, 'connection_info.base_path')
      },
      has_owner() {
        return _.isString(this.owner)
      },
      has_repository() {
        return _.isString(this.repository)
      },
      has_basepath() {
        return _.isString(this.base_path)
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      service_description() {
        return _.result(this, 'cinfo.service_description', '')
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      onEditClick() {
        this.$emit('edit-click', this.connection)
      },
    }
  }
</script>

<style lang="stylus" scoped>
  td
    padding: 0 0.5rem 0.25rem 0
    font-size: .875rem
    line-height: 1.5
  td:first-child::after
    content: ":"
  tr:last-child
    td
      padding-bottom: 0
</style>
