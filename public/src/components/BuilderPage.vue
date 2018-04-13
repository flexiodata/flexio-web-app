<template>
  <div class="bg-nearer-white pv4 overflow-y-auto">
    <div class="center mw7">
      <h1 class="fw6 f3 mid-gray mb4">{{title}}</h1>
      <div class="bg-white pa4 css-dashboard-box">
        <div class="tc" v-if="active_item_type == 'connection'">
          <ServiceIcon class="square-5" :type="active_item_connection_type" />
          <h2 class="fw6 f4 mid-gray mt2">Connect to <ServiceName :type="active_item_connection_type" /></h2>
        </div>
        <div class="tc mv4 pa4 br2 ba b--black-05" v-if="active_item_type == 'connection'">
          <ConnectionAuthenticationPanel :connection="active_item_connection" />
        </div>
        <div class="flex flex-row justify-end">
          <el-button
            class="ttu b"
            type="plain"
            @click="$store.commit('BUILDER__GO_PREV_ITEM')"
            v-show="!is_first_item"
          >
            Go Back
          </el-button>
          <el-button
            class="ttu b"
            type="primary"
            :disabled="can_continue"
            @click="$store.commit('BUILDER__GO_NEXT_ITEM')"
          >
            Continue
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import ServiceIcon from './ServiceIcon.vue'
  import ServiceName from './ServiceName.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'

  export default {
    components: {
      ServiceIcon,
      ServiceName,
      ConnectionAuthenticationPanel
    },
    watch: {
      active_item() {
        if (this.active_item_type != 'connection')
          return

        if (_.isNil(this.active_item_connection_eid)) {
          var attrs = { connection_type: this.active_item_connection_type }
          this.$store.dispatch('createConnection', { attrs }).then(response => {
            var connection = response.body
            this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
          })
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
        active_item_connection_eid: state => state.builder.active_item.connection_eid,
        active_item_connection_type: state => state.builder.active_item.connection_type
      }),
      is_first_item() {
        return this.active_item_idx == 0
      },
      is_last_item() {
        return this.active_item_idx == this.items.length - 1
      },
      active_item_connection() {
        return _.get(this.$store, 'state.objects.'+this.active_item_connection_eid, {})
      },
      can_continue() {
        if (this.active_item_type == 'connection') {
          return _.get(this.active_item_connection, 'connection_status', '') != CONNECTION_STATUS_AVAILABLE
        }

        return true
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
