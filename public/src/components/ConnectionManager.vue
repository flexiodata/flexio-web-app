<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading connections..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="flex-none flex flex-row ph2 ph0-l mh0 mh3-l pt2 pt3-l pb2 bb bb-0-l b--black-10">
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
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddModal()">New connection</btn>
      </div>
    </div>

    <!-- list -->
    <connection-list
      class="flex-fill overflow-auto"
      :filter="filter"
      :project-eid="projectEid"
      @item-edit="openEditModal"
      @item-delete="tryDeleteConnection"
    ></connection-list>

    <!-- props modal (used for both add and edit) -->
    <connection-props-modal
      ref="modal-props"
      :project-eid="projectEid"
      @submit="tryUpdateConnection"
    ></connection-props-modal>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import Spinner from 'vue-simple-spinner'
  import ConnectionList from './ConnectionList.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'
  import Btn from './Btn.vue'

  export default {
    props: ['project-eid'],
    components: {
      Spinner,
      ConnectionList,
      ConnectionPropsModal,
      Btn
    },
    data() {
      return {
        filter: ''
      }
    },
    computed: {
      is_fetched() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'connections_fetched', false)
      },
      is_fetching() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'connections_fetching', true)
      }
    },
    created() {
      if (!this.projectEid)
        return

      this.tryFetchConnections()
    },
    methods: {
      ...mapGetters([
        'getAllProjects'
      ]),
      openAddModal() {
        this.$refs['modal-props'].open()
      },
      openEditModal(item) {
        this.$refs['modal-props'].open(item, 'edit')
      },
      tryFetchConnections() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchConnections', this.projectEid)
      },
      tryUpdateConnection(attrs, modal) {
        var me = this
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'ename', 'description', 'token', 'host', 'port', 'username', 'password', 'database'])
        _.extend(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()

            // try to connect to the connection
            me.$store.dispatch('testConnection', { eid, attrs })
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
