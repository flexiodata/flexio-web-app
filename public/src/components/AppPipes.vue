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
          <el-input
            class="w-100 mw5 mr3"
            placeholder="Filter items..."
            @keydown.esc.native="filter = ''"
            v-model="filter"
          />
        </div>
        <div class="flex-none flex flex-row items-center">
          <el-button type="primary" class="ttu b" @click="onNewPipeClick">New pipe</el-button>
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
      @item-schedule="openPipeScheduleDialog"
      @item-deploy="openPipeDeployDialog"
      @item-delete="tryDeletePipe"
    />

    <!-- pipe schedule dialog -->
    <el-dialog
      custom-class="no-header no-footer"
      width="56rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_schedule_dialog"
    >
      <pipe-schedule-panel
        :pipe="active_pipe"
        @close="show_pipe_schedule_dialog = false"
        @cancel="show_pipe_schedule_dialog = false"
        @submit="trySchedulePipe"
      />
    </el-dialog>

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
  import PipeSchedulePanel from './PipeSchedulePanel.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      Spinner,
      PipeList,
      PipeSchedulePanel,
      PipeDeployPanel,
      Btn
    },
    data() {
      return {
        filter: '',
        active_pipe: {},
        show_pipe_schedule_dialog: false,
        show_pipe_deploy_dialog: false
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
      openPipeScheduleDialog(item) {
        this.active_pipe = item
        this.show_pipe_schedule_dialog = true
        this.$store.track('Clicked `Schedule` Button In Pipe List', this.getAnalyticsPayload(item))
      },
      openPipeDeployDialog(item) {
        this.active_pipe = item
        this.show_pipe_deploy_dialog = true
        this.$store.track('Clicked `Deploy` Button In Pipe List', this.getAnalyticsPayload(item))
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
      tryCreatePipe(attrs) {
        if (!_.isObject(attrs))
          attrs = { name: 'Untitled Pipe' }

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'alias', 'created'])
            this.$store.track('Created Pipe: New', analytics_payload)

            this.openPipe(response.body.eid)
          }
           else
          {
            this.$store.track('Created Pipe: New (Error)')
          }
        })
      },
      tryDeletePipe(attrs) {
        var name = _.get(attrs, 'name', 'Pipe')

        this.$confirm('Are you sure you want to delete the pipe named "'+name+'"?', 'Really delete pipe?', {
          confirmButtonText: 'DELETE PIPE',
          cancelButtonText: 'CANCEL',
          type: 'warning'
        }).then(() => {
          this.$store.dispatch('deletePipe', { attrs })
        }).catch(() => {
          // do nothing
        })
      },
      trySchedulePipe(attrs) {
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

            this.$store.track('Scheduled Pipe', analytics_payload)

            this.show_pipe_schedule_dialog = false
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      getAnalyticsPayload(pipe) {
        // do this until we fix the object ref issue in the Flex.io JS SDK
        var task_obj = _.cloneDeep(pipe.task)
        var edit_code = Flexio.pipe(task_obj).toCode()
        var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'alias'])

        _.assign(analytics_payload, {
          title: pipe.name,
          code: edit_code
        })

        return analytics_payload
      },
      onNewPipeClick() {
        this.tryCreatePipe()
      }
    }
  }
</script>
