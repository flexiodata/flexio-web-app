<template>
  <div v-show="visible">
    <!-- input panel header -->
    <div class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 pa2">
      <div class="f6 fw6">Test Function</div>
      <div class="flex-fill"></div>
      <el-button
        size="tiny"
        type="primary"
        class="ttu fw6"
        style="margin-right: 8px; padding: 7px 12px"
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

    <div class="flex-none flex flex-row items-center">
      <el-button
        type="text"
        size="small"
        style="padding: 8px"
        :class="show_input_parameters ? '' : 'hint--bottom'"
        :aria-label="show_input_parameters ? '' : 'Test this function with POST parameters'"
        @click="show_input_parameters = !show_input_parameters"
      >
        {{show_input_parameters ? 'Hide input parameters' : 'Show input parameters'}}
      </el-button>
    </div>

    <div class="flex-fill flex flex-column">
      <!-- input panel -->
      <div
        class="flex-none pa3 bb b--black-05 overflow-y-auto"
        style="max-height: 17rem"
        v-show="show_input_parameters"
      >
        <div class="f6 fw6">Input Parameters</div>
        <ProcessInput
          ref="process-input"
          v-model="process_input"
          :process-data.sync="process_data"
        />
      </div>

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
        alert('showing Reset Message')
      }
    }
  }
</script>

