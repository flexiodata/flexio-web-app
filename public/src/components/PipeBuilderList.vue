<template>
  <div class="overflow-y-auto" @scroll="onScroll">

    <ui-alert
      class="ma3"
      style="width: auto; box-shadow: 0 2px 4px -2px rgba(0,0,0,0.4)"
      type="success"
      :dismissible="false"
      @dismiss="show_success = false"
      v-show="show_success">
      {{success_message}}
    </ui-alert>

    <ui-alert
      class="ma3"
      style="width: auto; box-shadow: 0 2px 4px -2px rgba(0,0,0,0.4)"
      type="error"
      @dismiss="show_error = false"
      v-show="show_error">
      {{error_message}}
    </ui-alert>

    <div class="mv4 pt3 center" style="max-width: 1440px" v-if="tasks.length == 0">
      <div class="mh4 pa4 bg-white ba b--white-box br2 tc">
        <div class="lh-copy mid-gray mb3 i">There are no steps in this pipe.</div>
        <div class="mt3">
          <btn
            btn-lg
            btn-primary
            class="ttu b"
            @click="insertNewTask(-1)"
          >
            Add a step
          </btn>
        </div>
      </div>
    </div>

    <div class="mv4 pt3 center" style="max-width: 1440px" v-else>
      <pipe-builder-item
        v-for="(task, index) in tasks"
        :key="task.eid"
        :item="task"
        :index="index"
        :tasks="tasks"
        :active-prompt-idx="activePromptIdx"
        :first-prompt-idx="first_prompt_idx"
        :last-prompt-idx="last_prompt_idx"
        :is-prompting="isPrompting"
        :is-scrolling="is_scrolling"
        :active-process="activeProcess"
        :show-preview="show_all_previews"
        :show-insert-before-first-task="true"
        @insert-task="insertNewTask"
        @toggle-preview="togglePreview"
        @prompt-value-change="onPromptValueChange"
        @go-prev-prompt="$emit('go-prev-prompt')"
        @go-next-prompt="emitGoNextPrompt"
        @run-once-with-values="$emit('run-once-with-values')"
        @save-values-and-run="$emit('save-values-and-run')"
      ></pipe-builder-item>
    </div>
  </div>
</template>

<script>
  import {
    PROCESS_STATUS_RUNNING,
    PROCESS_STATUS_FAILED,
    PROCESS_STATUS_COMPLETED
  } from '../constants/process'
  import Btn from './Btn.vue'
  import PipeBuilderItem from './PipeBuilderItem.vue'

  export default {
    props: {
      'pipe-options': {
        type: Object,
        default: () => { return {} }
      },
      'tasks': {
        type: Array,
        required: true
      },
      'connections': {
        type: Array,
        default: () => { return [] }
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'active-prompt-idx': {
        type: Number,
        default: 0
      },
      'active-process': {
        type: Object
      }
    },
    components: {
      Btn,
      PipeBuilderItem
    },
    inject: ['pipeEid'],
    watch: {
      active_process_status: function(val, old_val) {
        if (val == PROCESS_STATUS_RUNNING)
        {
          this.show_error = false
          this.show_success = false

          // scroll back to the top of the pipe list when the process starts
          this.$scrollTo('#'+this.pipeEid, {
            container: '#'+this.pipeEid,
            duration: 400,
            easing: 'ease-out'
          })
        }
         else if (old_val == PROCESS_STATUS_RUNNING)
        {
          if (val == PROCESS_STATUS_COMPLETED)
          {
            setTimeout(() => { this.show_success = false }, 6000)

            setTimeout(() => {
              this.show_success = true
              this.show_error = false
            }, 1000)
          }

          if (val == PROCESS_STATUS_FAILED)
          {
            setTimeout(() => {
              this.show_success = false
              this.show_error = true
            }, 1000)
          }

          /*
          var eid = this.pipeEid
          var process_eid = _.get(this.activeProcess, 'eid', '')
          var subprocesses = _.get(this.activeProcess, 'subprocesses', [])
          var task_ops_arr = _.map(subprocesses, (s) => { return _.get(s, 'task_op', '') })
          var task_ops = task_ops_arr.join(', ')
          var task_count = _.size(task_ops_arr)
          var duration = _.get(this.activeProcess, 'duration', -1)

          if (val == PROCESS_STATUS_COMPLETED)
          {
            setTimeout(() => { this.show_success = false }, 6000)

            setTimeout(() => {
              this.show_success = true
              this.show_error = false

              //analytics.track('Ran Pipe: Success', { eid, process_eid, task_ops, task_count, duration })
            }, 1000)
          }

          if (val == PROCESS_STATUS_FAILED)
          {
            setTimeout(() => {
              this.show_success = false
              this.show_error = true

              var subprocess = _.find(subprocesses, { process_status: PROCESS_STATUS_FAILED } )
              var error = _.get(subprocess, 'process_info.error', {})
              var error_code = _.get(error, 'code', '')
              var message = _.get(error, 'message', '')
              if (message.length == 0)
                message = 'An error occurred while running the pipe.'

              this.error_message = message

              //analytics.track('Ran Pipe: Error', { eid, process_eid, task_ops, task_count, duration, message, error_code })
            }, 1000)
          }
          */
        }
      }
    },
    data() {
      return {
        is_scrolling: false,
        show_all_previews: true,
        show_success: false,
        show_error: false,
        success_message: 'The pipe was run successfully!',
        error_message: ''
      }
    },
    computed: {
      first_prompt_idx() {
        return _.findIndex(this.tasks, { is_prompt: true })
      },
      last_prompt_idx() {
        return _.findLastIndex(this.tasks, { is_prompt: true })
      },
      active_process_status() {
        return _.get(this.activeProcess, 'process_status', '')
      }
    },
    methods: {
      insertNewTask(idx) {
        var eid = this.pipeEid
        var attrs = {
          index: _.defaultTo(idx, -1),
          op: '',
          params: {}
        }
        this.$store.dispatch('createPipeTask', { eid, attrs })
      },

      togglePreview(show, toggle_all) {
        if (toggle_all)
          this.show_all_previews = show
      },

      onPromptValueChange(val, variable_set_key) {
        this.$emit('prompt-value-change', val, variable_set_key)
      },

      emitGoNextPrompt(task_eid) {
        this.$emit('go-next-prompt', task_eid)
      },

      resetScroll: _.debounce(function() {
        this.is_scrolling = false
      }, 200),

      onScroll: _.debounce(function() {
        this.is_scrolling = true
        this.resetScroll()
      }, 20, { 'leading': true, 'trailing': false })
    }
  }
</script>
