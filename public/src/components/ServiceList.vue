<template>
  <div>
    <service-item
      v-for="(service, index) in services"
      :item="service"
      :index="index"
      :layout="itemLayout"
      @activate="onItemActivate"
    >
    </service-item>
  </div>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import ServiceItem from './ServiceItem.vue'

  export default {
    props: {
      'list-type': {
        type: String,
        default: ''
      },
      'item-layout': {
        type: String,
        default: 'grid' // 'grid' or 'list'
      },
      'filter-items': {
        type: String,
        default: '' // '', 'services' or 'non-services'
      }
    },
    components: {
      ServiceItem
    },
    computed: {
      services() {
        var services = connections

        if (this.listType == 'input')
          services = _.filter(services, { is_input: true })
        if (this.listType == 'output')
          services = _.filter(services, { is_output: true })

        if (this.filterItems == 'services')
          services = _.filter(services, { is_service: true })
        if (this.filterItems == 'non-services')
          services = _.filter(services, { is_service: false })

        return services
      }
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
