<template>
  <div class="flex flex-column h-100" v-if="visible">
    <!-- header -->
    <div class="flex-none flex flex-row items-center bg-nearer-white bb b--black-05 ph2 pv1">
      <div class="f6 fw6">Test Function</div>
      <div class="flex-fill"></div>
      <el-button
        size="small"
        type="text"
        style="padding: 0"
        @click="show_advanced_input = !show_advanced_input"
      >
        <span v-if="show_advanced_input">Test with parameters</span>
        <span v-else>Test with JSON</span>
      </el-button>
      <el-button
        size="mini"
        type="primary"
        class="ttu fw6"
        style="margin: 2px 8px; padding: 6px 12px"
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
        @submit.prevent.native
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
        <div class="flex-fill f6 fw6">Result</div>
        <el-radio-group
          size="micro"
          v-model="result_view"
          :disabled="has_errors"
        >
          <el-radio-button label="table"><span class="fw6">Table</span></el-radio-button>
          <el-radio-button label="json"><span class="fw6">JSON</span></el-radio-button>
        </el-radio-group>
      </div>

      <div v-if="is_running">
        <div class="pa2 flex flex-row items-center">
          <Spinner size="small" />
          <span class="ml2 f6">Running...</span>
        </div>
      </div>
      <template v-else>
        <el-alert
          style="border-radius: 0"
          type="error"
          show-icon
          title="An error occured while running the function"
          :closable="false"
          v-if="has_errors"
        />
        <pre
          class="flex-fill ma0 pa0 bg-white"
          v-if="has_errors || result_view == 'json'"
        ><code class="ma0 pa2">{{response_str}}</code></pre>
        <SimpleTable
          class="flex-fill overflow-x-auto"
          style="border: none"
          :rows="response_arr"
          v-else-if="response_arr.length > 0"
        />
      </template>

      <!-- output panel -->
      <ProcessContent
        class="flex-fill flex flex-column"
        :process-eid="active_process_eid"
        v-if="false"
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
  import Spinner from 'vue-simple-spinner'
  import SimpleTable from '@/components/SimpleTable'
  import ProcessInput from '@/components/ProcessInput'
  import ProcessContent from '@/components/ProcessContent'

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
      response_arr: [],
      response_str: '',
      result_view: 'table', // 'json' or 'table'
      has_errors: false,
      is_running: false
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
      Spinner,
      SimpleTable,
      ProcessInput,
      ProcessContent,
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
      /*
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
      */
      runTest() {
        var eid = this.pipeEid
        var team_name = this.active_team_name
        var cfg = this.show_advanced_input ? this.process_input_advanced : this.process_input_simple

        this.is_running = true

        this.$store.dispatch('pipes/run', { team_name, eid, cfg }).then(response => {
          this.has_errors = false
          this.response_arr = response.data
          this.response_str = JSON.stringify(response.data, null, 2)
        }).catch(error => {
          this.has_errors = true
          this.response_arr = []
          this.response_str = _.get(error, 'response.data.error.message', '')
        }).finally(() => {
          this.is_running = false
        })

        this.$store.track('Tested Function')
      },
    }
  }
</script>

