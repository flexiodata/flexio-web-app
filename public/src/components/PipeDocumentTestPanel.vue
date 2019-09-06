<template>
  <div class="flex flex-column h-100" v-show="visible">
    <!-- header -->
    <div class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 pa2">
      <div class="f6 fw6">Test Function</div>
      <el-button
        type="text"
        size="small"
        style="margin-left: 8px; padding: 1px 0 0; border: 0"
        :class="show_input_parameters ? '' : 'hint--bottom'"
        :aria-label="show_input_parameters ? '' : 'Test this function with input parameters'"
        @click="show_input_parameters = !show_input_parameters"
      >
        {{show_input_parameters ? 'Hide parameters' : 'Show parameters'}}
      </el-button>
      <div class="flex-fill"></div>
      <el-button
        size="mini"
        type="primary"
        class="ttu fw6"
        style="margin-right: 8px"
        @click="runTest"
      >
        Run test
      </el-button>
      <div
        class="pointer f5 black-30 hover-black-60 hint--bottom-left"
        aria-label="Close"
        @click="$emit('update:visible', false)"
      >
        <i class="el-icon-close fw6"></i>
      </div>
    </div>

    <!-- input panel -->
    <div
      class="flex-none pa3 bb b--black-05 overflow-y-auto"
      style="max-height: 16rem"
      v-show="show_input_parameters"
    >
      <ProcessInput
        ref="process-input"
        v-model="process_input"
        :process-data.sync="process_data"
      />
    </div>

    <!-- output panel -->
    <div class="flex-fill flex flex-column">
      <!-- output panel header -->
      <div
        class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 pa2"
        v-show="show_input_parameters"
      >
        <div class="f6 fw6">Result</div>
      </div>

      <!-- output panel -->
      <ProcessContent
        class="flex-fill flex flex-column"
        :process-eid="active_process_eid"
      >
        <!-- don't show any empty text -->
        <div class="pa3 tc f6 lh-copy" slot="empty"></div>
      </ProcessContent>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { PROCESS_MODE_BUILD } from '@/constants/process'
  import ProcessInput from '@/components/ProcessInput'
  import ProcessContent from '@/components/ProcessContent'

  const getDefaultState = () => {
    return {
      active_process_eid: '',
      show_input_parameters: false,
      process_input: {},
      process_data: {},
    }
  }

  export default {
    props: {
      pipeEid: {
        type: String,
        required: true
      },
      visible: {
        type: Boolean,
        default: false
      }
    },
    components: {
      ProcessInput,
      ProcessContent,
    },
    watch: {
      pipeEid: {
        handler: 'showResetMessage'
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())
      },
      showResetMessage() {
        //console.log('TODO')
      },
      runTest() {
        var team_name = this.active_team_name
        var attrs = _.pick(this.pipe, ['task'])
        var cfg = this.process_input

        _.assign(attrs, {
          parent_eid: this.pipe.eid,
          process_mode: PROCESS_MODE_BUILD
        })

        this.$store.dispatch('processes/create', { team_name, attrs }).then(response => {
          var process = response.data
          var eid = process.eid
          this.active_process_eid = eid
          this.$store.dispatch('processes/run', { team_name, eid, cfg })
        })

        this.$store.track('Tested Function')
      },
    }
  }
</script>

