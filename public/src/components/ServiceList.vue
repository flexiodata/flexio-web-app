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
  import { CONNECTION_TYPE_FLEX }  from '@/constants/connection-type'
  import * as connections from '@/constants/connection-info'
  import ServiceItem from '@/components/ServiceItem'

  export default {
    props: {
      layout: {
        type: String,
        default: 'grid' // 'grid' or 'list'
      },
      filterBy: {
        type: Function
      }
    },
    components: {
      ServiceItem
    },
    computed: {
      filtered_services() {
        var services = connections
        return this.filterBy ? _.filter(services, this.filterBy) : services
      },
      services() {
        return _.reject(this.filtered_services, { connection_type: CONNECTION_TYPE_FLEX })
      }
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
