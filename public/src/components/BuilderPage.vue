<template>
  <div class="bg-nearer-white pv4">
    <div class="center mw7">
      <h1 class="fw6 f3 mid-gray mb4">{{title}}</h1>
      <div class="bg-white pa4 css-dashboard-box">
        <div class="tc" v-if="active_item_type == 'connection'">
          <ServiceIcon class="square-5" :type="active_item_connection" />
          <h2 class="fw6 f4 mid-gray mt2">Connect to <ServiceName :type="active_item_connection" /></h2>
        </div>
        <div class="tc mv5" v-if="active_item_type == 'connection'">
          <ConnectionAuthenticationPanel :connection="connection" />
        </div>
        <div class="flex flex-row justify-end">
          <el-button class="ttu b" type="plain" @click="$store.commit('BUILDER__GO_PREV_ITEM')" v-show="!is_first_item">Go Back</el-button>
          <el-button class="ttu b" type="primary" @click="$store.commit('BUILDER__GO_NEXT_ITEM')">Continue</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import ServiceIcon from './ServiceIcon.vue'
  import ServiceName from './ServiceName.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'

  export default {
    components: {
      ServiceIcon,
      ServiceName,
      ConnectionAuthenticationPanel
    },
    data() {
      return {
        connection: {
          connection_type: 'googledrive'
        }
      }
    },
    computed: {
      ...mapState({
        title: state => state.builder.title,
        items: state => state.builder.items,
        active_item: state => state.builder.active_item,
        active_item_idx: state => state.builder.active_item.idx,
        active_item_type: state => state.builder.active_item.type,
        active_item_connection: state => state.builder.active_item.connection_type
      }),
      is_first_item() {
        return this.active_item_idx == 0
      },
      is_last_item() {
        return this.active_item_idx == this.items.length - 1
      },
      debug_json() {
        return JSON.stringify(this.active_item, null, 2)
      }
    },
    mounted() {
      this.$store.commit('BUILDER__INIT_ITEMS')
    }
  }
</script>
