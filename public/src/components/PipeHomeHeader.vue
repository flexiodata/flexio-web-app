<template>
  <div class="flex flex-row">
    <div class="flex-fill">
      <div class="mb1">
        <inline-edit-text
          class="dib f3 li-title v-mid dark-gray"
          input-key="name"
          :val="pipe_name"
          @save="editPipeSingleton">
        </inline-edit-text>
        <inline-edit-text
          class="dib f7 li-title v-mid silver pv1 ph2 bg-black-05"
          placeholder="Add an alias"
          input-key="ename"
          :val="pipe_ename"
          :show-edit-button="false"
          @save="editPipeSingleton">
        </inline-edit-text>
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
        var attrs = {}
        attrs[key] = val

        this.$store.dispatch('updatePipe', { eid , attrs })
        input.endEdit()
      }
    }
  }
</script>
