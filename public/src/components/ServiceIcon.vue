<template>
  <img v-if="url.length > 0" :src="url_src" :alt="name" :title="name">
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
      },
      url_src() {
        var url = this.url
        if (url.indexOf('logo.clearbit.com') >= 0) {
          return url
        }

        var idx = url.indexOf('//')
        url = idx >= 0 ? this.url.substring(idx + 2) : this.url
        if (url.indexOf('/') != -1) {
          url = url.substr(0, url.indexOf('/'))
        }

        return 'https://logo.clearbit.com/' + url
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.type })
      }
    }
  }
</script>
