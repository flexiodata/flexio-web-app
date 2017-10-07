<template>
  <div>
    <div class="flex-none flex flex-row items-stretch cursor-default no-select">
      <div
        class="flex flex-row items-center darken-05" @click="openFolder('/')"
        style="padding: 0.25rem 0.375rem"
      >
        <service-icon
          class="br2 css-icon"
          style="width: 1rem; height; 1rem"
          :type="ctype"
        ></service-icon>
        <div class="f7 ml1">
          {{service_name}}
        </div>
      </div>
      <div
        class="flex flex-row items-center"
        v-for="(item, index) in items"
      >
        <i class="material-icons md-24 black-20 rotate-270 na1">arrow_drop_down</i>
        <span
          class="f7 darken-05"
          style="padding: 0.3125rem 0.375rem"
          @click="openFolder(item.path)"
        >
          {{item.name}}
        </span>
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
        var items = _
          .chain(_.get(this, 'path', ''))
          .trim('/')
          .split('/')
          .compact()
          .value()

        var path = ''
        return _.map(items, (name) => {
          path += '/'+name
          return { name, path }
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
