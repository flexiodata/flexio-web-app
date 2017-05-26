<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading pipes..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="flex-none flex flex-row pa3 pa4-l bb bb-0-l b--black-10" style="max-width: 1574px">
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
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openPipeAddModal()">New pipe</btn>
      </div>
    </div>

    <!-- list -->
    <pipe-list
      class="flex-fill pl4-l pr4-l pb4-l"
      style="max-width: 1574px"
      :filter="filter"
      @item-edit="openPipeEditModal"
      @item-duplicate="duplicatePipe"
      @item-share="openPipeShareModal"
      @item-schedule="openPipeScheduleModal"
    ></pipe-list>

    <!-- add modal -->
    <pipe-props-modal
      ref="modal-add-pipe"
      @submit="tryCreatePipe"
      @hide="show_pipe_add_modal = false"
      v-if="show_pipe_add_modal"
    ></pipe-props-modal>

    <!-- edit modal -->
    <pipe-props-modal
      ref="modal-edit-pipe"
      @submit="tryUpdatePipe"
      @hide="show_pipe_edit_modal = false"
      v-if="show_pipe_edit_modal"
    ></pipe-props-modal>

    <!-- share modal -->
    <pipe-share-modal
      ref="modal-share-pipe"
      @hide="show_pipe_share_modal = false"
      v-if="show_pipe_share_modal"
    ></pipe-share-modal>

    <!-- schedule modal -->
    <pipe-schedule-modal
      ref="modal-schedule-pipe"
      @submit="trySchedulePipe"
      @hide="show_pipe_schedule_modal = false"
      v-if="show_pipe_schedule_modal"
    ></pipe-schedule-modal>
  </div>
</template>

<script>
  import { ROUTE_PIPEHOME } from '../constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeList from './PipeList.vue'
  import PipePropsModal from './PipePropsModal.vue'
  import PipeShareModal from './PipeShareModal.vue'
  import PipeScheduleModal from './PipeScheduleModal.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      PipeList,
      PipePropsModal,
      PipeShareModal,
      PipeScheduleModal,
      Btn
    },
    data() {
      return {
        filter: '',
        show_pipe_add_modal: false,
        show_pipe_edit_modal: false,
        show_pipe_share_modal: false,
        show_pipe_schedule_modal: false,
        show_connection_add_modal: false
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'pipes_fetching',
        'is_fetched': 'pipes_fetched'
      })
    },
    created() {
      this.tryFetchPipes()
    },
    methods: {
      openPipe(eid) {
        this.$router.push({ name: ROUTE_PIPEHOME, params: { eid } })
      },
      openPipeAddModal(ref, attrs) {
        this.show_pipe_add_modal = true
        this.$nextTick(() => { this.$refs['modal-add-pipe'].open() })
      },
      openPipeEditModal(item) {
        this.show_pipe_edit_modal = true
        this.$nextTick(() => { this.$refs['modal-edit-pipe'].open(item) })
      },
      openPipeShareModal(item) {
        this.show_pipe_share_modal = true
        this.$nextTick(() => { this.$refs['modal-share-pipe'].open(item) })
      },
      openPipeScheduleModal(item) {
        this.show_pipe_schedule_modal = true
        this.$nextTick(() => { this.$refs['modal-schedule-pipe'].open(item) })
      },
      duplicatePipe(item) {
        var attrs = {
          copy_eid: item.eid,
          name: item.name
        }

        this.$store.dispatch('createPipe', { attrs })
      },
      tryFetchPipes() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchPipes')
      },
      tryCreatePipe(attrs, modal) {
        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
            this.openPipe(response.body.eid)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryUpdatePipe(attrs, modal) {
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'ename', 'description', 'rights'])

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
      }
    }
  }
</script>
