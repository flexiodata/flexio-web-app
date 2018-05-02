<template>
  <div>
    <div class="flex-none flex flex-row items-stretch cursor-default no-select">
      <div
        class="flex flex-row items-center"
        v-for="(item, index) in items"
      >
        <div
          class="flex flex-row items-center darken-05"
          style="padding: 0.25rem 0.375rem"
          @click="openFolder(item.path)"
          v-if="item.is_connection"
        >
          <service-icon
            class="br1"
            style="width: 1rem; height; 1rem"
            :type="ctype"
          ></service-icon>
          <div class="f7 ml1">
            {{service_name}}
          </div>
        </div>

        <i class="material-icons md-18 black-20 rotate-270" style="margin: 0 -2px" v-if="!item.is_connection">expand_more</i>

        <div
          class="f7 darken-05"
          style="padding: 0.3125rem 0.375rem"
          @click="openFolder(item.path)"
          v-if="!item.is_connection"
        >
          {{item.name}}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import ServiceIcon from './ServiceIcon.vue'
  import FileChooserList from './FileChooserList.vue'

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
      },
      'path': {
        type: String,
        default: '/'
      }
    },
    components: {
      ServiceIcon,
      FileChooserList
    },
    computed: {
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      },
      items() {
        var p = _.get(this, 'path', '')
        p = _.trim(p, '/')

        var items = p.split('/')
        items = _.compact(items)

        var idx = 0
        var path = ''
        return _.map(items, (name) => {
          path += '/'+name

          // show the folder name
          if (idx++ > 0)
            return { name, path, is_connection: false }

          // show the service icon and name
          if (name === _.get(this.connection, 'eid') || name === _.get(this.connection, 'alias'))
            return { name, path, is_connection: true }
        })
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      openFolder(path) {
        this.$emit('open-folder', _.defaultTo(path, '/'))
      }
    }
  }
</script>
