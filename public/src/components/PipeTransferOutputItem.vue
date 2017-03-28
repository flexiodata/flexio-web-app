<template>
  <article class="mb5">
    <div class="flex flex-row items-center pa2 bg-black-05 bb b--black-05">
      <connection-icon :type="ctype" class="v-mid br1 fx-square-2 mr2"></connection-icon>
      <div class="f6 fw6 ttu silver">{{service_name}}</div>
      <div class="flex-fill"></div>
      <i class="material-icons silver">menu</i>
    </div>
    <div class="flex flex-row items-center pa2 f6 truncate bb b--light-gray hide-child" v-for="(item, index) in items">
      <div class="flex-fill">{{item.path}}</div>
      <div class="flex-none f4 pointer silver hover-black child">&times;</div>
    </div>
  </article>
</template>

<script>
  import * as connections from '../constants/connection-info'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item', 'connection-type'],
    components: {
      ConnectionIcon
    },
    computed: {
      ctype() {
        return _.get(this.item, 'metadata.connection_type', '')
      },
      items() {
        return _.get(this.item, 'params.items', '')
      },
      service_name() {
        return _.result(this, 'cinfo.service_name', '')
      }
    },
    methods: {
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      }
    }
  }
</script>
