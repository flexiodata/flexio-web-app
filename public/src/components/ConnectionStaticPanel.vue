<template>
  <div>
    <div class="flex flex-row">
      <ServiceIcon class="flex-none mt1 br2 square-4" :type="ctype" :url="url" :empty-cls="''" />
      <div class="flex-fill flex flex-column" style="margin-left: 12px">
        <div class="f4 fw6 lh-title">{{connection.name}}</div>
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
            <td>Type</td>
            <td>OAuth2</td>
          </tr>
          <tr>
            <td>Status</td>
            <td>
              <div class="flex flex-row items-center lh-copy">
                <i class="el-icon-success v-mid dark-green mr1"></i>
                <span class="f6 fw6">Connected</span>
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
          Edit Properties
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import * as ctypes from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import ServiceIcon from '@comp/ServiceIcon'

  export default {
    props: {
      connection: {
        type: Object,
        default: () => { return {} }
      }
    },
    components: {
      ServiceIcon
    },
    computed: {
      ctype() {
        return _.get(this, 'connection.connection_type', '')
      },
      cdesc() {
        var desc = _.get(this, 'connection.description', '')
        return desc.length > 200 ? desc.substring(0, 200) + '...' : desc
      },
      url() {
        return _.get(this, 'connection.connection_info.url', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      service_description() {
        return _.result(this, 'cinfo.service_description', '')
      },
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
    padding: 0.125rem 0.5rem 0.25rem 0
    font-size: .875rem
  td:first-child::after
    content: ":"
</style>
