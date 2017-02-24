<template>
  <div>
    <!-- control bar -->
    <div v-if="!is_fetching" class="flex-none flex flex-row ph2 ph0-l mh0 mh3-l pt2 pt3-l pb2 bb bb-0-l b--black-10">
      <div class="flex-fill">
        <input
          type="text"
          class="input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue pa2 w-90 w-50-m w-30-l min-w5-m min-w5a-l f6"
          placeholder="Filter pipes..."
          @keydown.esc="filter = ''"
          v-model="filter"
        >
      </div>
      <div class="flex-none">
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddModal()">New pipe</btn>
      </div>
    </div>

    <!-- list -->
    <pipe-list
      class="flex-fill overflow-auto"
      :filter="filter"
      :project-eid="projectEid"
      @item-edit="openEditModal"
      @item-duplicate="duplicatePipe"
      @item-share="openShareModal"
      @item-schedule="openScheduleModal"
    ></pipe-list>

    <!-- add modal -->
    <pipe-props-modal
      ref="modal-add-pipe"
      open-from=".btn-add"
      close-to=".btn-add"
      :project-eid="projectEid"
      @add-connection="openConnectionModal"
      @submit="tryCreatePipe"
    ></pipe-props-modal>

    <!-- edit modal -->
    <pipe-props-modal
      ref="modal-edit-pipe"
      :project-eid="projectEid"
      @submit="tryUpdatePipe"
    ></pipe-props-modal>

    <!-- share modal -->
    <pipe-share-modal
      ref="modal-share-pipe"
      @hide="show_share_modal = false"
      v-if="show_share_modal"
    ></pipe-share-modal>

    <!-- schedule modal -->
    <pipe-schedule-modal
      ref="modal-schedule-pipe"
      @submit="trySchedulePipe"
      @hide="show_schedule_modal = false"
      v-if="show_schedule_modal"
    ></pipe-schedule-modal>

    <!-- connection modal -->
    <connection-props-modal
      ref="modal-add-connection"
      :project-eid="projectEid"
      @submit="tryAddConnection"
    ></connection-props-modal>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import Spinner from './Spinner.vue'
  import PipeList from './PipeList.vue'
  import PipePropsModal from './PipePropsModal.vue'
  import PipeShareModal from './PipeShareModal.vue'
  import PipeScheduleModal from './PipeScheduleModal.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'
  import Btn from './Btn.vue'

  export default {
    props: ['project-eid'],
    components: {
      Spinner,
      PipeList,
      PipePropsModal,
      PipeShareModal,
      PipeScheduleModal,
      ConnectionPropsModal,
      Btn
    },
    data() {
      return {
        filter: '',
        show_share_modal: false,
        show_schedule_modal: false
      }
    },
    computed: {
      is_fetched() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'pipes_fetched', false)
      },
      is_fetching() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'pipes_fetching', true)
      }
    },
    created() {
      if (!this.projectEid)
        return

      this.tryFetchPipes()
    },
    methods: {
      ...mapGetters([
        'getAllProjects'
      ]),
      openAddModal(ref, attrs) {
        this.$refs['modal-add-pipe'].open()
      },
      openEditModal(item) {
        this.$refs['modal-edit-pipe'].open(item)
      },
      openShareModal(item) {
        this.show_share_modal = true
        this.$nextTick(() => { this.$refs['modal-share-pipe'].open(item) })
      },
      openScheduleModal(item) {
        this.show_schedule_modal = true
        this.$nextTick(() => { this.$refs['modal-schedule-pipe'].open(item) })
      },
      openConnectionModal() {
        this.$refs['modal-add-pipe'].close()
        this.$refs['modal-add-connection'].open()
      },
      duplicatePipe(item) {
        var attrs = {
          copy_eid: item.eid,
          name: item.name,
          parent_eid: this.projectEid
        }

        this.$store.dispatch('createPipe', { attrs })
      },
      tryFetchPipes() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchPipes', this.projectEid)
      },
      tryCreatePipe(attrs, modal) {
        _.extend(attrs, { parent_eid: this.projectEid })

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryUpdatePipe(attrs, modal) {
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'ename', 'description'])

        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      trySchedulePipe(attrs, modal) {
        var eid = attrs.eid
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])
        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryAddConnection(attrs, modal) {
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

            // re-open the add pipe modal and set its connection
            me.$refs['modal-add-pipe'].open({ connection: attrs })
          }
           else
          {
            // TODO: add error handling
          }
        })
      }
    }
  }
</script>
