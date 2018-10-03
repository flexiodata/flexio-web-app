<template>
  <div>
    <ServiceItem
      v-for="(service, ctype) in services"
      :key="ctype"
      :item="service"
      :layout="layout"
      @activate="onItemActivate"
    />
  </div>
</template>

<script>
  import { CONNECTION_TYPE_HOME }  from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import ServiceItem from './ServiceItem.vue'

  export default {
    props: {
      layout: {
        type: String,
        default: 'grid' // 'grid' or 'list'
      }
    },
    components: {
      ServiceItem
    },
    computed: {
      filtered_services() {
        var services = connections
        return services
      },
      services() {
        return _.reject(this.filtered_services, { connection_type: CONNECTION_TYPE_HOME })
      }
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
