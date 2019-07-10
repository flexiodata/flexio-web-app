<template>
  <div>
    <div class="flex flex-row">
      <ServiceIcon class="flex-none mt1 br2 square-5" :type="ctype" :url="url" :empty-cls="''" />
      <div class="flex-fill flex flex-column" style="margin-left: 12px">
        <div class="f4 fw6 lh-title">{{service_name}}</div>
        <div class="f6 fw4 mt1">{{service_description}}</div>
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
