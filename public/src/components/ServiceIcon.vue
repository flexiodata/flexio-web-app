<template>
  <img v-if="url.length > 0" :src="url_src" :alt="name" :title="name">
  <img v-else-if="icon" :src="icon" :alt="name" :title="name">
  <div v-else :class="empty_cls"></div>
</template>

<script>
  import * as connections from '../constants/connection-info'

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
      },
      url_src() {
        var base_url = this.url.substring(this.url.indexOf('//') + 2)
        if (base_url.indexOf('/') != -1)
          base_url = base_url.substr(0, base_url.indexOf('/'))
        return 'https://logo.clearbit.com/' + base_url
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.type })
      }
    }
  }
</script>
