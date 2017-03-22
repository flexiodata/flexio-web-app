<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading pipe..."></spinner>
  </div>
  <div v-else class="flex flex-column items-stretch">
    <div class="mt3 mh5">
      <div class="mb1">
        <div class="dib f3 li-title v-mid dark-gray mr2 v-mid">{{pipe_name}}</div>
        <div class="dib f7 li-title v-mid silver pv1 ph2 bg-black-05">
          <span v-if="pipe_ename.length > 0">{{pipe_ename}}</span>
          <span v-else>Add an alias</span>
        </div>
      </div>
      <div class="f6 lh-title mid-gray">
        <span v-if="pipe_description.length > 0">{{pipe_description}}</span>
        <span class="fw6 black-20" v-else>Add a description</span>
      </div>
    </div>

    <div class="flex-fill flex flex-column">
      <task-list2 :tasks="tasks"></task-list2>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import setActiveProject from './mixins/set-active-project'

  import Btn from './Btn.vue'
  import Spinner from './Spinner.vue'
  import TaskList2 from './TaskList2.vue'

  export default {
    mixins: [setActiveProject],
    components: {
      Btn,
      Spinner,
      TaskList2,
    },
    data() {
      return {
        eid: this.$route.params.eid
      }
    },
    computed: {
      pipe()              { return _.get(this.$store, 'state.objects.'+this.eid, {}) },
      pipe_name()         { return _.get(this.pipe, 'name', '') },
      pipe_ename()        { return _.get(this.pipe, 'ename', '') },
      pipe_description()  { return _.get(this.pipe, 'description', '') },
      is_fetched()        { return _.get(this.pipe, 'is_fetched', false) },
      is_fetching()       { return _.get(this.pipe, 'is_fetching', false) },
      processes_fetched() { return _.get(this.pipe, 'processes_fetched', false) },
      tasks()             { return _.get(this.pipe, 'task', []) },
      project_eid()       { return _.get(this.pipe, 'project.eid', '') },
    },
    created() {
      this.tryFetchPipe()
      this.tryFetchProcesses()
    },
    methods: {
      ...mapGetters([
        'getActiveDocumentProcesses'
      ]),

      tryFetchPipe() {
        if (!this.is_fetched)
        {
          this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
            if (response.ok)
            {
              if (this.project_eid.length > 0)
                this.setActiveProject(this.project_eid)
            }
             else
            {
              // TODO: add error handling
            }
          })
        }
      },

      tryFetchProcesses() {
        // this will fetch all of the processes for this pipe; doing so will update the processes
        // in the global store, which will in turn cause the getActiveDocumentProcesses()
        // getter on the store to change, which will cause the computed 'active_process' value
        // to be updated which will propogate to any derived computed values to change which
        // will cause our component to update
        if (!this.processes_fetched)
          this.$store.dispatch('fetchProcesses', this.eid)
      }
    }
  }
</script>
