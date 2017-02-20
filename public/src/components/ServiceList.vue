<template>
  <div>
    <service-item
      v-for="(service, index) in services"
      :item="service"
      :index="index"
      @activate="onItemActivate"
    >
    </service-item>
  </div>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import ServiceItem from './ServiceItem.vue'

  export default {
    props: ['list-type'],
    components: {
      ServiceItem
    },
    computed: {
      services() {
        if (this.listType == 'input')
          return _.filter(connections, { is_service: true, is_input: true })
        if (this.listType == 'output')
          return _.filter(connections, { is_service: true, is_output: true })

        return _.filter(connections, { is_service: true })
      }
    },
    methods: {
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
