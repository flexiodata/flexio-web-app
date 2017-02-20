<template>
  <div>
    <div class="flex-none flex flex-row items-center cursor-default no-select">
      <connection-icon
        class="dib v-top br2 css-icon"
        :type="ctype"
      ></connection-icon>
      <div
        class="ml1 mid-gray underline-hover"
        @click="openFolder('/')"
      >
        {{service_name}}
      </div>
      <div
        class="flex flex-row items-center"
        v-for="(item, index) in items"
      >
        <i class="material-icons md-18">chevron_right</i>
        <span
          class="underline-hover"
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
  import ConnectionIcon from './ConnectionIcon.vue'
  import FileChooserList from './FileChooserList.vue'

  export default {
    props: ['connection', 'path'],
    components: {
      ConnectionIcon,
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

<style>
  .css-icon {
    width: 1.25rem;
    height: 1.25rem;
  }
</style>
