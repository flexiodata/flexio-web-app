<template>
  <div class="flex flex-row items-start min-w5">
    <div class="flex-fill mb1 mr2">
      <div class="flex flex-column flex-row-l items-center-l mb1 mb0-l">
        <inline-edit-text
          class="dib f3 lh-title v-mid dark-gray mb1 mb0-l mr2-ns"
          input-key="name"
          :val="pipe_name"
          @save="editPipeSingleton">
        </inline-edit-text>
        <div class="flex flex-row items-center">
          <inline-edit-text
            class="dib f7 v-mid silver bg-black-05 pv1 ph2 mr1"
            placeholder="Add an alias"
            input-key="ename"
            :val="pipe_ename"
            :show-edit-button="false"
            @save="editPipeSingleton">
          </inline-edit-text>
          <div
            class="hint--bottom hint--large cursor-default"
            aria-label="When using the Flex.io command line interface (CLI) or API, pipes may be referenced either via their object ID or via an alias created here. Aliases are unique across the app, so we recommend prefixing your username to the alias (e.g., username-foo)."
            v-if="pipe_ename.length == 0"
          >
            <i class="material-icons blue md-18">info</i>
          </div>
        </div>
      </div>
      <inline-edit-text
        class="f6 lh-title gray"
        placeholder="Add a description"
        placeholder-cls="fw6 black-20 hover-black-40"
        input-key="description"
        :val="pipe_description"
        @save="editPipeSingleton">
      </inline-edit-text>
    </div>
    <div class="flex-none flex flex-column flex-row-ns items-end items-center-ns">
      <div
        class="f6 fw6 dark-gray pointer mr3-ns dn db-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='transfer'?'b--blue':'b--transparent']"
        @click="setPipeView('transfer')"
      >Pipe Overview</div>
      <div
        class="f6 fw6 dark-gray pointer mr3-ns dn db-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='builder'?'b--blue':'b--transparent']"
        @click="setPipeView('builder')"
      >Pipe Builder</div>
      <btn
        btn-md
        btn-primary
        class="ttu b"
        v-if="processRunning"
        @click="cancelProcess"
      >Cancel</btn>
      <div
        class="hint--bottom-left"
        :aria-label="run_button_tooltip"
        v-else
      >
        <btn
          btn-md
          btn-primary
          class="ttu b"
          :disabled="!is_run_allowed"
          @click="runPipe"
        >Run</btn>
      </div>
      <div
        class="f7 fw6 dark-gray pointer mt2 db dn-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='transfer'?'b--blue':'b--transparent']"
        @click="setPipeView('transfer')"
      >Pipe Overview</div>
      <div
        class="f7 fw6 dark-gray pointer mt2 db dn-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='builder'?'b--blue':'b--transparent']"
        @click="setPipeView('builder')"
      >Pipe Builder</div>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import InlineEditText from './InlineEditText.vue'

  export default {
    props: ['pipe-eid', 'pipe-view', 'process-running'],
    components: {
      Btn,
      InlineEditText
    },
    computed: {
      pipe()              { return _.get(this.$store, 'state.objects.'+this.pipeEid, {}) },
      pipe_name()         { return _.get(this.pipe, 'name', '') },
      pipe_ename()        { return _.get(this.pipe, 'ename', '') },
      pipe_description()  { return _.get(this.pipe, 'description', '') },
      tasks()             { return _.get(this.pipe, 'task', []) },
      input_tasks()       { return _.filter(this.tasks, { type: TASK_TYPE_INPUT }) },

      is_run_allowed() {
        if (this.input_tasks.length == 0)
          return false
        return true
      },

      run_button_tooltip() {
        return this.is_run_allowed ? '' : 'Pipes must have an input or execute step in order to be run'
      }
    },
    methods: {
      setPipeView(view) {
        this.$emit('set-pipe-view', view)
      },
      editPipeSingleton(attrs, input) {
        var eid = this.pipeEid
        this.$store.dispatch('updatePipe', { eid , attrs })
        input.endEdit()
      },
      runPipe() {
        this.setPipeView('builder')
        this.$emit('run-pipe')
      },
      cancelProcess() {
        this.$emit('cancel-process')
      }
    }
  }
</script>

<style lang="less">
  @import "../stylesheets/variables.less";

  .css-nav-text {
    transition: border 0.3s ease-in-out;
    padding: 2px;

    &:hover,
    &.css-nav-active {
      border-bottom: 2px solid @blue;
    }
  }
</style>
