<template>
  <img v-if="icon" :src="icon" :alt="name" :title="name">
  <div v-else class="ba b--black-20 b--dashed"></div>
</template>

<script>
  import * as connections from '../constants/connection-info'

  export default {
    props: ['type'],
    computed: {
      icon() { return _.result(this, 'cinfo.icon', false) },
      name() { return _.result(this, 'cinfo.service_name', '') }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.type })
      }
    }
  }
</script>
