<template>
  <div>
    <service-item
      v-for="(service, ctype) in services"
      :key="ctype"
      :item="service"
      :layout="layout"
      :class="itemCls"
      :override-cls="overrideItemCls"
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
      'layout': {
        type: String,
        default: 'grid' // 'grid' or 'list'
      },
      'item-cls': {
        type: String,
        default: ''
      },
      'override-item-cls': {
        type: Boolean,
        default: false
      },
      'filter-items': {
        type: String,
        default: '' // '', 'storage', 'services' or by connection type
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
           else if (this.listType == 'output')
          services = _.filter(services, { is_output: true })

        if (this.filterItems == 'storage')
          services = _.filter(services, { is_storage: true })
           else if (this.filterItems == 'services')
          services = _.filter(services, { is_service: true })
           else if (_.size(this.filterItems) > 0)
          services = _.filter(services, { connection_type: this.filterItems })

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
