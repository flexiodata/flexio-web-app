<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-row items-center">
      <Spinner size="small" />
      <span class="ml2 f6">Loading...</span>
    </div>
  </div>

  <!-- fetched -->
  <div v-else-if="is_fetched">
    <!-- header -->
    <div class="flex flex-row">
      <div class="flex-fill flex flex-row items-center">
        <div class="f3 fw6 lh-title pipe-title">{{pipe.name}}</div>
        <LabelSwitch
          class="ml3 hint--bottom"
          active-color="#13ce66"
          :aria-label="is_deployed ? 'Turn function off' : 'Turn function on'"
          :width="58"
          v-model="is_deployed"
          v-require-rights:pipe.update
        />
      </div>
      <div class="flex-none">
        <el-button
          class="ttu fw6"
          style="min-width: 5rem"
          size="small"
          type="primary"
          v-require-rights:pipe.update
        >
          Edit
        </el-button>
      </div>
    </div>

    <!-- content -->
    <div>
      <pre class="f7 overflow-x-scroll">{{pipe.task}}</pre>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import LabelSwitch from '@/components/LabelSwitch'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  export default {
    props: {
      pipeEid: {
        type: String,
        required: true
      }
    },
    components: {
      Spinner,
      LabelSwitch
    },
    watch: {
      pipeEid: {
        handler: 'tryFetchPipe',
        immediate: true,
      }
    },
    data() {
      return {
        is_local_fetching: false,
      }
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
      is_fetched() {
        return _.get(this.pipe, 'vuex_meta.is_fetched', false)
      },
      is_fetching() {
        return _.get(this.pipe, 'vuex_meta.is_fetching', false) || this.is_local_fetching /* Vuex pipe `is_fetching` isn't yet implemented */
      },
      is_deployed: {
        get() {
          return _.get(this.pipe, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
        },
        set(value) {
          if (value === false) {
            this.$confirm('This function is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn function off?', {
              confirmButtonClass: 'ttu fw6',
              cancelButtonClass: 'ttu fw6',
              confirmButtonText: 'Turn function off',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              this.deployPipe(value)
            })
          } else {
            this.deployPipe(value)
          }
        }
      },
    },
    methods: {
      tryFetchPipe() {
        var team_name = this.active_team_name
        var name = this.pipe.name

        if (!this.is_fetched && !this.is_fetching) {
          this.is_local_fetching = true
          this.$store.dispatch('pipes/fetch', { team_name, name }).finally(() => {
            this.is_local_fetching = false
          })
        }
      },
      deployPipe(is_deployed) {
        var team_name = this.active_team_name
        var eid = this.pipe.eid

        var deploy_mode = is_deployed === false ? DEPLOY_MODE_BUILD : DEPLOY_MODE_RUN
        var attrs = { deploy_mode }

        this.$store.dispatch('pipes/update', { team_name, eid, attrs }).then(response => {
          var updated_pipe = response.data

          this.$message({
            message: 'The function was updated successfully.',
            type: 'success'
          })

          // change the object name in the route
          if (updated_pipe.name != this.pipe.name) {
            var object_name = updated_pipe.name

            var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
            new_route.params = _.assign({}, new_route.params, { object_name })
            this.$router.replace(new_route)
          }
        })
      }
    }
  }
</script>
