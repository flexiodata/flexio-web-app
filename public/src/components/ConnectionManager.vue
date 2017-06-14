<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading connections..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="flex flex-row pa3 pa4-l bb bb-0-l b--black-10" style="max-width: 1152px">
      <div class="flex-fill">
        <input
          type="text"
          class="input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue pa2 w-90 w-50-m w-30-l min-w5-m min-w5a-l f6"
          placeholder="Filter connections..."
          @keydown.esc="filter = ''"
          v-model="filter"
        >
      </div>
      <div class="flex-none">
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddModal">New connection</btn>
      </div>
    </div>

    <!-- list -->
    <connection-list
      class="pl4-l pr4-l pb4-l"
      style="max-width: 1152px"
      :filter="filter"
      :show-header="true"
      @item-edit="openEditModal"
      @item-delete="tryDeleteConnection"
    ></connection-list>

    <!-- props modal (used for both add and edit) -->
    <connection-props-modal
      ref="modal-connection-props"
      @submit="tryUpdateConnection"
      @hide="show_connection_props_modal = false"
      v-if="show_connection_props_modal"
    ></connection-props-modal>
  </div>
</template>

<script>
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionList from './ConnectionList.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      ConnectionList,
      ConnectionPropsModal,
      Btn
    },
    data() {
      return {
        filter: '',
        show_connection_props_modal: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      })
    },
    created() {
      this.tryFetchConnections()
    },
    methods: {
      openAddModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open() })
        analytics.track('Clicked New Connection Button')
      },
      openEditModal(item) {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(item, 'edit') })
      },
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      tryUpdateConnection(attrs, modal) {
        var eid = attrs.eid
        var ctype = _.get(attrs, 'connection_type', '')
        var is_pending = _.get(attrs, 'eid_status', '') === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'ename', 'description', 'rights', 'token', 'host', 'port', 'username', 'password', 'database'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()

            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs })

            if (is_pending)
            {
              var analytics_payload = { eid, attrs }
              _.set(analytics_payload, 'connection_type', ctype)
              analytics.track('Created Connection', analytics_payload)
            }
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryDeleteConnection(attrs) {
        this.$store.dispatch('deleteConnection', { attrs })
      }
    }
  }
</script>
