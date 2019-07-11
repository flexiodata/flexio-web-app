<template>
  <div>
    <div class="flex flex-row">
      <ServiceIcon class="flex-none mt1 br2 square-4" :type="ctype" :url="url" :empty-cls="''" />
      <div class="flex-fill flex flex-column" style="margin-left: 12px">
        <div class="f4 fw6 lh-title">{{connection.short_description}}</div>
        <div class="f6 fw4 mt1 lh-copy silver" v-if="cdesc.length > 0">{{cdesc}}</div>
        <div class="f6 fw4 mt1" v-else><em class="moon-gray">(No description)</em></div>
      </div>
      <div class="flex-fill flex flex-column" style="margin-left: 12px">
        <table>
          <colgroup>
            <col>
            <col class="w-100">
          </colgroup>
          <tr>
            <td class="nowrap">Connection Type</td>
            <td>
              <div class="flex flex-row items-center lh-copy">
                <ServiceIcon class="flex-none mr1 br1 square-1" :type="ctype" :url="url" :empty-cls="''" />
                <span class="f6 fw6">{{service_name}}</span>
              </div>
            </td>
          </tr>
          <tr>
            <td>Status</td>
            <td>
              <div class="flex flex-row items-center lh-copy">
                <i class="el-icon-success dark-green mr1" v-if="is_available"></i>
                <i class="el-icon-error dark-red mr1" v-else></i>
                <span class="f6 fw6">{{is_available ? 'Connected' : 'Not Connected'}}</span>
              </div>
            </td>
          </tr>
        </table>
      </div>
      <div class="flex-none">
        <el-button
          size="small"
          type="primary"
          class="ttu fw6"
          @click="$emit('edit-click')"
        >
          Edit Connection
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import ServiceIcon from '@comp/ServiceIcon'
  import MixinConnection from '@comp/mixins/connection'

  export default {
    props: {
      connection: {
        type: Object,
        default: () => { return {} }
      }
    },
    mixins: [MixinConnection],
    components: {
      ServiceIcon
    },
    computed: {
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cdesc() {
        var desc = _.get(this.connection, 'description', '')
        return desc.length > 200 ? desc.substring(0, 200) + '...' : desc
      },
      cstatus() {
        return _.get(this.connection, 'connection_status', '')
      },
      is_available() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      url() {
        return _.get(this, 'connection.connection_info.url', '')
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
      }
    }
  }
</script>

<style lang="stylus" scoped>
  td
    padding: 0 0.5rem 0.25rem 0
    font-size: .875rem
  td:first-child::after
    content: ":"
</style>
