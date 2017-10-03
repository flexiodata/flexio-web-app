<template>
  <img v-if="icon" :src="icon" :alt="name" :title="name">
  <div v-else :class="empty_cls"></div>
</template>

<script>
  import * as connections from '../constants/connection-info'

  export default {
    props: {
      'type': {
        type: String,
        required: false
      },
      'dashed-border': {
        type: Boolean,
        default: true
      }
    },
    computed: {
      icon() {
        return _.result(this, 'cinfo.icon', false)
      },
      name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      empty_cls() {
        return this.dashedBorder ? 'ba b--black-20 b--dashed' : ''
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.type })
      }
    }
  }
</script>
