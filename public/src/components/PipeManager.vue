<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading pipes..."></spinner>
    </div>
  </div>
  <div class="flex flex-column overflow-y-auto" v-else>
    <!-- control bar -->
    <div class="pa3 pa4-l pb3-l bb bb-0-l b--black-10" style="max-width: 1152px">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2 dn db-ns mr3">Pipes</div>
          <input
            type="text"
            class="input-reset ba b--black-10 focus-b--transparent focus-outline focus-ow1 focus-o--blue pa2 w-100 mw5 mr3 f6"
            placeholder="Filter items..."
            @keydown.esc="filter = ''"
            v-model="filter"
          >
        </div>
        <div class="flex-none flex flex-row items-center">
          <btn btn-md btn-primary class="btn-add ttu b ba" @click="tryCreatePipe()">New pipe</btn>
        </div>
      </div>
    </div>

    <!-- list -->
    <pipe-list
      class="pl4-l pr4-l pb4-l"
      style="max-width: 1152px"
      :filter="filter"
      :show-header="true"
      @item-edit="openPipeEditModal"
      @item-duplicate="duplicatePipe"
      @item-share="openPipeShareModal"
      @item-embed="openPipeEmbedModal"
      @item-schedule="openPipeScheduleModal"
      @item-deploy="openPipeDeployModal"
      @item-delete="tryDeletePipe"
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

    <!-- embed modal -->
    <pipe-embed-modal
      ref="modal-embed-pipe"
      @hide="show_pipe_embed_modal = false"
      v-if="show_pipe_embed_modal"
    ></pipe-embed-modal>

    <!-- schedule modal -->
    <pipe-schedule-modal
      ref="modal-schedule-pipe"
      @submit="trySchedulePipe"
      @hide="show_pipe_schedule_modal = false"
      v-if="show_pipe_schedule_modal"
    ></pipe-schedule-modal>

    <!-- deploy modal -->
    <pipe-deploy-modal
      ref="modal-deploy-pipe"
      :is-onboarding="false"
      @hide="show_pipe_deploy_modal = false"
      v-if="show_pipe_deploy_modal"
    ></pipe-deploy-modal>
  </div>
</template>

<script>
  import { ROUTE_PIPES } from '../constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeList from './PipeList.vue'
  import PipePropsModal from './PipePropsModal.vue'
  import PipeShareModal from './PipeShareModal.vue'
  import PipeEmbedModal from './PipeEmbedModal.vue'
  import PipeScheduleModal from './PipeScheduleModal.vue'
  import PipeDeployModal from './PipeDeployModal.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      PipeList,
      PipePropsModal,
      PipeShareModal,
      PipeEmbedModal,
      PipeScheduleModal,
      PipeDeployModal,
      Btn
    },
    data() {
      return {
        filter: '',
        show_pipe_add_modal: false,
        show_pipe_edit_modal: false,
        show_pipe_share_modal: false,
        show_pipe_embed_modal: false,
        show_pipe_schedule_modal: false,
        show_pipe_deploy_modal: false,
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
        this.$router.push({ name: ROUTE_PIPES, params: { eid } })
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
      openPipeEmbedModal(item) {
        this.show_pipe_embed_modal = true
        this.$nextTick(() => { this.$refs['modal-embed-pipe'].open(item) })
      },
      openPipeScheduleModal(item) {
        this.show_pipe_schedule_modal = true
        this.$nextTick(() => { this.$refs['modal-schedule-pipe'].open(item) })
      },
      openPipeDeployModal(item) {
        this.show_pipe_deploy_modal = true
        this.$nextTick(() => { this.$refs['modal-deploy-pipe'].open(item) })
      },
      duplicatePipe(item) {
        var attrs = {
          copy_eid: item.eid,
          name: item.name
        }

        this.$store.dispatch('createPipe', { attrs })
      },
      tryFetchPipes() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchPipes')
      },
      tryCreatePipe(attrs, modal) {
        if (!_.isObject(attrs))
          attrs = { name: 'Untitled Pipe' }

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'ename'])

            // add Segment-friendly keys
            _.assign(analytics_payload, {
              createdAt: _.get(pipe, 'created')
            })

            analytics.track('Created Pipe: New', analytics_payload)

            if (!_.isNil(modal))
              modal.close()

            this.openPipe(response.body.eid)
          }
           else
          {
            analytics.track('Created Pipe: New (Error)')
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
      tryDeletePipe(attrs) {
        this.$store.dispatch('deletePipe', { attrs })
      },
      trySchedulePipe(attrs, modal) {
        var eid = attrs.eid
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])
        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
          {
            var frequency = _.get(response.body, 'schedule.frequency', '')
            var schedule_status = _.get(response.body, 'schedule_status', '')
            analytics.track('Scheduled Pipe', { eid, frequency, schedule_status })
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
