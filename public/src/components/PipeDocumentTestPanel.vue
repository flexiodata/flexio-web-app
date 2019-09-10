<template>
  <div class="flex flex-column h-100" v-if="visible">
    <!-- header -->
    <div class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 ph2 pv1">
      <div class="f6 fw6">Test Function</div>
      <div class="flex-fill"></div>
      <LabelSwitch
        class="hint--bottom"
        active-label=""
        inactive-label=""
        font-size="12px"
        :aria-label="show_advanced_input ? 'Hide advanced input options' : 'Show advanced input options'"
        v-model="show_advanced_input"
      />
      <el-button
        size="mini"
        type="primary"
        class="ttu fw6"
        style="margin: 2px 12px; padding: 6px 12px"
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
      class="flex-none pa2 overflow-y-auto"
      style="max-height: 260px"
    >
      <ProcessInput
        ref="process-input"
        v-model="process_input_advanced"
        :process-data.sync="process_data"
        v-if="show_advanced_input"
      />
      <div
        class="mv3 tc lh-title silver i f7"
        v-else-if="pipe_params.length == 0"
      >
        There are no parameters to specify on this function</span>
      </div>
      <el-form
        class="el-form--compact el-form__label-tiny"
        size="small"
        :model="process_input_simple"
        v-else
      >
        <el-form-item
          style="margin-bottom: 5px"
          :label="item.name"
          :item="item"
          :index="index"
          :key="item.name"
          v-for="(item, index) in pipe_params"
        >
          <el-input
            auto-complete="off"
            spellcheck="false"
            style="line-height: 24px"
            v-model="process_input_simple.data[index]"
          />
        </el-form-item>
      </el-form>
    </div>

    <!-- output panel -->
    <div class="flex-fill flex flex-column">
      <!-- output panel header -->
      <div class="flex-none flex flex-row items-center bg-nearer-white bt bb b--black-05 pa2">
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
  import LabelSwitch from '@/components/LabelSwitch'

  const getSimpleProcessInput = () => {
    return {
      data: [],
      headers: {
        'Content-Type': 'application/json'
      }
    }
  }

  const getDefaultState = () => {
    return {
      active_process_eid: '',
      show_advanced_input: false,
      process_input_simple: getSimpleProcessInput(),
      process_input_advanced: {},
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
      LabelSwitch,
    },
    watch: {
      pipe_params: {
        handler: 'initProcessInputFromParams'
      },
      visible: {
        handler: 'initSelf',
        immediate: true
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
      pipe_params() {
        return _.get(this.pipe, 'params', [])
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())
        this.initProcessInputFromParams()
      },
      initProcessInputFromParams() {
        this.process_input_simple = _.assign({}, getSimpleProcessInput())
      },
      runTest() {
        var team_name = this.active_team_name
        var attrs = _.pick(this.pipe, ['task'])
        var cfg = this.show_advanced_input ? this.process_input_advanced : this.process_input_simple

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

