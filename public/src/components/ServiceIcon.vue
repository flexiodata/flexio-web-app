<template>
  <img v-if="url.length > 0" :src="url" :alt="name" :title="name">
  <img v-else-if="icon" :src="icon" :alt="name" :title="name">
  <div v-else :class="emptyCls"></div>
</template>

<script>
  import * as connections from '@/constants/connection-info'

  export default {
    props: {
      'url': {
        type: String,
        default: ''
      },
      'type': {
        type: String,
        required: false
      },
      'empty-cls': {
        type: String,
        default: 'ba b--black-20 b--dashed'
      }
    },
    computed: {
      icon() {
        return _.result(this, 'cinfo.icon', false)
      },
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
