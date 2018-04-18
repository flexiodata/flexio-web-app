<template>
  <div>
    <div class="tc">
      <ServiceIcon class="square-4" :type="ctype" />
      <h3 class="fw6 f4 mid-gray mt2">Connect to {{service_name}}</h3>
    </div>
    <div class="mv4 pa4 br2 ba b--black-05">
      <ConnectionAuthenticationPanel
        :connection="store_connection"
        v-if="ceid && !store_connection_status_available"
      />
      <div v-else>
        <ConnectionChooserList
          class="mb3"
          style="max-height: 24rem"
          layout="list"
          :connection="store_connection"
          :connection-type-filter="ctype"
          :show-selection="true"
          :show-selection-checkmark="true"
          @item-activate="chooseConnection"
          v-if="connections_of_type.length > 0"
        />
        <el-button
          class="ttu b"
          type="plain"
          @click="setUpConnection"
        >
          Set up a new connection
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import MixinConnectionInfo from './mixins/connection-info'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
    },
    mixins: [MixinConnectionInfo],
    components: {
      ServiceIcon,
      ConnectionAuthenticationPanel,
      ConnectionChooserList
    },
    computed: {
      ceid() {
        return _.get(this.item, 'connection_eid', null)
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      service_name() {
        return this.getConnectionInfo(this.ctype, 'service_name')
      },
      connections() {
        return this.getAllConnections()
      },
      connections_of_type() {
        return _.filter(this.connections, { connection_type: this.ctype })
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      store_connection_status_available() {
        return _.get(this.store_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      chooseConnection(connection) {
        this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
      },
      setUpConnection() {
        var attrs = {
          //eid_status: OBJECT_STATUS_PENDING,
          name: this.service_name,
          connection_type: this.ctype
        }

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          var connection = response.body
          this.chooseConnection(connection)
        })
      }
    }
  }
</script>
