<template>
  <div class="flex flex-row">
    <div class="flex-fill">
      <div class="mb1">
        <div class="dib f3 li-title v-mid dark-gray mr2 v-mid">{{pipe_name}}</div>
        <div class="dib f7 li-title v-mid silver pv1 ph2 bg-black-05">
          <span v-if="pipe_ename.length > 0">{{pipe_ename}}</span>
          <span v-else>Add an alias</span>
        </div>
      </div>
      <div class="f6 lh-title gray">
        <span v-if="pipe_description.length > 0">{{pipe_description}}</span>
        <span class="fw6 black-20" v-else>Add a description</span>
      </div>
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

  export default {
    props: ['pipe-eid', 'pipe-view', 'process-running'],
    components: {
      Btn
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
      }
    }
  }
</script>
