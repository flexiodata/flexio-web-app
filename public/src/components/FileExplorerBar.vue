<template>
  <div>
    <div class="flex-none flex flex-row items-stretch cursor-default no-select">
      <div
        class="flex flex-row items-center"
        :key="item.full_path"
        v-for="(item, index) in items"
      >
        <div
          class="flex flex-row items-center darken-05"
          style="padding: 0.25rem 0.375rem"
          @click="openFolder(item.full_path)"
          v-if="item.is_connection"
        >
          <ServiceIcon
            class="br1"
            style="width: 1rem; height; 1rem"
            :type="ctype"
          />
          <div class="f7 ml1">
            {{base_folder_label}}
          </div>
        </div>

        <template v-else>
          <i class="material-icons md-18 black-20 rotate-270" style="margin: 0 -2px">expand_more</i>

          <div
            class="f7 darken-05"
            style="padding: 0.3125rem 0.375rem"
            @click="openFolder(item.full_path)"
          >
            {{item.name}}
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
  import ServiceIcon from '@comp/ServiceIcon'

  export default {
    props: {
      connection: {
        type: Object,
        required: true
      },
      path: {
        type: String,
        default: '/'
      }
    },
    components: {
      ServiceIcon
    },
    computed: {
      cname() {
        return _.get(this.connection, 'short_description', '')
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      base_path() {
        var path = _.get(this.connection, 'connection_info.base_path', '')
        if (path.length > 0 && path.indexOf('/') != 0) {
          path = '/' + path
        }
        return path
      },
      base_folder_label() {
        return this.cname + this.base_path
      },
      items() {
        var path = this.path

        // always use `connection_identifier:/folder/file.txt` syntax
        var path_items = path.split(':')
        var connection_identifier = path_items[0]
        path = path_items[1]
        path = _.trim(path, '/')

        var items = path.split('/')
        items = _.compact(items)

        // make sure we include the connection in our items array
        items.splice(0, 0, connection_identifier)

        var idx = 0
        var full_path = ''
        return _.map(items, (name) => {
          if (idx == 0) {
            full_path += name + ':/'
          } else if (idx == 1) {
            // don't prepend a slash here since the connection's path already has the slash in it
            full_path += name
          } else {
            full_path += '/' + name
          }

          // show the folder name
          if (idx++ > 0) {
            return { name, full_path, is_connection: false }
          }

          // show the service icon and name
          if (name === _.get(this.connection, 'eid') || name === _.get(this.connection, 'name')) {
            return { name, full_path, is_connection: true }
          }
        })
      }
    },
    methods: {
      openFolder(path) {
        this.$emit('open-folder', _.defaultTo(path, '/'))
      }
    }
  }
</script>
