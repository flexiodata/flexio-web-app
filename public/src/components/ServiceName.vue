<template>
  <span>{{name}}</span>
</template>

<script>
  import * as connections from '../constants/connection-info'

  export default {
    props: {
      'type': {
        type: String,
        required: true
      }
    },
    computed: {
      name() {
        return _.result(this, 'cinfo.service_name', '')
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.type })
      }
    }
  }
</script>
