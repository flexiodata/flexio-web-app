<template>
  <div class="flex flex-row">
    <div class="flex-fill">
      <div class="flex flex-row items-center mb1">
        <inline-edit-text
          class="dib f3 li-title v-mid dark-gray mr1"
          input-key="name"
          :val="pipe_name"
          @save="editPipeSingleton">
        </inline-edit-text>
        <inline-edit-text
          class="dib f7 li-title v-mid silver pv1 ph2 mr1 bg-black-05"
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
      <inline-edit-text
        class="f6 lh-title gray"
        placeholder="Add a description"
        placeholder-cls="fw6 black-20 hover-black-40"
        input-key="description"
        :val="pipe_description"
        @save="editPipeSingleton">
      </inline-edit-text>
    </div>
    <div class="flex-none flex flex-row items-center">
      <div
        class="f6 fw6 blue pointer mr3"
        @click="setPipeView('builder')"
        v-if="pipeView == 'transfer'"
      >Use Builder View</div>
      <div
        class="f6 fw6 blue pointer mr3"
        @click="setPipeView('transfer')"
        v-else
      >Use Transfer View</div>
      <btn
        btn-md
        btn-primary
        class="ttu b"
        v-if="processRunning"
      >Cancel</btn>
      <btn
        btn-md
        btn-primary
        class="ttu b"
        :disabled="!is_run_allowed"
        @click="runPipe"
        v-else
      >Run</btn>
    </div>
  </div>
</template>

<script>
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

      is_run_allowed() {
        if (this.tasks.length == 0)
          return false
        return true
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
        this.$emit('run-pipe')
      }
    }
  }
</script>
