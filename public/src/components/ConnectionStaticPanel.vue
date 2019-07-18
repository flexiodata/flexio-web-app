<template>
  <div>
    <div class="flex flex-row">
      <div class="flex-fill flex flex-column flex-row-l">
        <div class="flex-fill flex flex-row mr4-l mb3 mb0-l">
          <ServiceIcon class="flex-none mt1 br2 square-4" :type="ctype" :url="url" :empty-cls="''" />
          <div class="flex-fill flex flex-column" style="margin-left: 12px">
            <div class="f4 fw6 lh-title">{{connection.name}}</div>
            <div class="f6 fw4 mt1 lh-copy silver" v-if="cdesc.length > 0">{{cdesc}}</div>
            <div class="f6 fw4 mt1" v-else><em class="moon-gray">(No description)</em></div>
          </div>
        </div>
        <div class="flex-fill flex flex-column">
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
                <td class="f6" v-if="base_path.length == 0"><em class="moon-gray">(root folder)</em></td>
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
          @click="$emit('edit-click')"
        >
          Edit
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
        var word_count = 50
        var desc = _.get(this.connection, 'description', '')
        var desc_arr = desc.split(' ')
        return desc_arr.length <= word_count ? desc_arr.join(' ') : desc_arr.slice(0, word_count).join(' ') + '...'
      },
      cstatus() {
        return _.get(this.connection, 'connection_status', '')
      },
      is_available() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
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
      }
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
</style>
