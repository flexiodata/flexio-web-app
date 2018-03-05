<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading storage items..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="pa3 pa4-l pb3-l bb bb-0-l b--black-10" style="max-width: 1152px">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2 dn db-ns mr3">Storage</div>
          <el-input
            class="w-100 mw5 mr3 f6"
            placeholder="Filter items..."
            @keydown.esc.native="filter = ''"
            v-model="filter"
          />
        </div>
        <div class="flex-none flex flex-row items-center">
          <el-button type="primary" class="ttu b" @click="openAddModal">New storage</el-button>
        </div>
      </div>
    </div>

    <!-- list -->
    <storage-list
      class="pl4-l pr4-l pb4-l h-100"
      style="max-width: 1152px"
      :filter="filter"
      :show-header="true"
      @item-edit="openEditModal"
      @item-delete="tryDeleteConnection"
    ></storage-list>

    <!-- props modal (used for both add and edit) -->
    <storage-props-modal
      ref="modal-connection-props"
      @submit="tryUpdateConnection"
      @hide="show_connection_props_modal = false"
      v-if="show_connection_props_modal"
    ></storage-props-modal>
  </div>
</template>

<script>
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import StorageList from './StorageList.vue'
  import StoragePropsModal from './StoragePropsModal.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      StorageList,
      StoragePropsModal,
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

        attrs = _.pick(attrs, ['name', 'ename', 'description', 'connection_info'])
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
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'ename', 'description', 'connection_type'])
              this.$store.dispatch('analyticsTrack', 'Created Connection', analytics_payload)
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
