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
          <el-button type="primary" class="ttu b" @click="tryCreatePipe">New pipe</el-button>
        </div>
      </div>
    </div>

    <!-- list -->
    <pipe-list
      class="pl4-l pr4-l pb4-l"
      style="max-width: 1152px"
      :filter="filter"
      :show-header="true"
      @item-duplicate="duplicatePipe"
      @item-share="openPipeShareModal"
      @item-embed="openPipeEmbedModal"
      @item-schedule="openPipeScheduleModal"
      @item-deploy="openPipeDeployDialog"
      @item-delete="tryDeletePipe"
    ></pipe-list>

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

    <!-- pipe deploy dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="56rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_deploy_dialog"
    >
      <pipe-deploy-panel
        :pipe="active_pipe"
        @close="show_pipe_deploy_dialog = false"
      />
    </el-dialog>

  </div>
</template>

<script>
  import { ROUTE_PIPES } from '../constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Flexio from 'flexio-sdk-js'
  import Spinner from 'vue-simple-spinner'
  import PipeList from './PipeList.vue'
  import PipeShareModal from './PipeShareModal.vue'
  import PipeEmbedModal from './PipeEmbedModal.vue'
  import PipeScheduleModal from './PipeScheduleModal.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      PipeList,
      PipeShareModal,
      PipeEmbedModal,
      PipeScheduleModal,
      PipeDeployPanel,
      Btn
    },
    data() {
      return {
        filter: '',
        active_pipe: {},
        show_pipe_share_modal: false,
        show_pipe_embed_modal: false,
        show_pipe_schedule_modal: false,
        show_pipe_deploy_dialog: false,
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
        this.$store.dispatch('analyticsTrack', 'Clicked `Schedule` Button In Pipe List', this.getAnalyticsPayload(item))
      },
      openPipeDeployDialog(item) {
        this.active_pipe = item
        this.show_pipe_deploy_dialog = true
        this.$store.dispatch('analyticsTrack', 'Clicked `Deploy` Button In Pipe List', this.getAnalyticsPayload(item))
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
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'ename', 'created'])
            this.$store.dispatch('analyticsTrack', 'Created Pipe: New', analytics_payload)

            if (!_.isNil(modal))
              modal.close()

            this.openPipe(response.body.eid)
          }
           else
          {
            this.$store.dispatch('analyticsTrack', 'Created Pipe: New (Error)')
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
        var eid = _.get(attrs, 'eid', '')
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])
        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
          {
            var frequency = _.get(response.body, 'schedule.frequency', '')
            var schedule_status = _.get(response.body, 'schedule_status', '')
            var analytics_payload = this.getAnalyticsPayload(attrs)

            _.assign(analytics_payload, {
              frequency,
              schedule_status
            })

            this.$store.dispatch('analyticsTrack', 'Scheduled Pipe', analytics_payload)
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      getAnalyticsPayload(pipe) {
        var edit_code = Flexio.pipe(pipe.task).toCode()
        var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'ename'])

        _.assign(analytics_payload, {
          title: pipe.name,
          code: edit_code
        })

        return analytics_payload
      }
    }
  }
</script>
