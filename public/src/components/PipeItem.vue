<template>
  <article
    class="mv3-l bb ba-l br2-l pointer no-select trans-pm css-list-item"
    @click="openPipe"
  >
    <div class="flex flex-row items-center">
      <div class="flex-fill mr2 fw6 f6 f5-ns">
        <router-link class="link" :to="pipe_route">
          <div class="pa3">
            <div class="flex-l flex-row items-center">
              <h3 class="f6 f5-ns fw6 lh-title dark-gray mv0 mr2 css-list-title">{{item.name}}</h3>
              <div
                class="dib f8 code black-40 mt1 mt0-l pa1 bg-nearer-white ba b--black-05 br2"
                v-if="item.alias"
              >{{item.alias}}</div>
            </div>
            <div class="dn db-l mw7" v-if="has_description">
              <h4 class="f6 fw4 mt1 mb0 lh-copy light-silver">{{item.description}}</h4>
            </div>
          </div>
        </router-link>
      </div>
      <div class="flex-none flex flex-row items-center">
        <div class="hint--top" style="padding: 0 1px" :aria-label="schedule_tooltip">
          <i
            class="material-icons md-21"
            :class="is_deployed_schedule ? 'blue' : 'o-10'"
          >
            schedule
          </i>
        </div>
        <div class="hint--top" style="padding: 0 1px" :aria-label="api_endpoint_tooltip">
          <i
            class="material-icons md-21"
            :class="is_deployed_api ? 'blue' : 'o-10'"
          >
            code
          </i>
        </div>
        <div class="hint--top" style="padding: 0 1px" :aria-label="runtime_tooltip">
          <i
            class="material-icons md-21"
            :class="is_deployed_ui ? 'blue' : 'o-10'"
          >
            offline_bolt
          </i>
        </div>
      </div>
      <div class="flex-none nt3 nb3 ml3 ml4-l">
        <div class="pv3" @click.stop>
          <LabelSwitch
            class="dib hint--top"
            active-color="#13ce66"
            :width="58"
            :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
            v-model="is_deployed"
          />
        </div>
      </div>
      <div class="flex-none pl2" @click.stop>
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link dib pointer pa3 black-30 hover-black">
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem; margin-top: -0.5rem" slot="dropdown">
            <el-dropdown-item class="flex flex-row items-center ph2" command="open"><i class="material-icons mr3">edit</i> Edit</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="duplicate"><i class="material-icons mr3">content_copy</i> Duplicate</el-dropdown-item>
            <div class="mv2 bt b--black-10"></div>
            <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import pipe_util from '../utils/pipe'
  import { ROUTE_PIPES } from '../constants/route'
  import LabelSwitch from './LabelSwitch.vue'

  const DEPLOY_MODE_UNDEFINED = ''
  const DEPLOY_MODE_BUILD     = 'B'
  const DEPLOY_MODE_RUN       = 'R'

  const ACTIVE = 'A'
  const INACTIVE = 'I'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      LabelSwitch
    },
    computed: {
      input_type() {
        return ''
      },
      output_type() {
        return ''
      },
      has_description() {
        return _.get(this.item, 'description', '').length > 0
      },
      is_deployed_schedule() {
        return _.get(this.item, 'deploy_schedule', INACTIVE) == ACTIVE
      },
      is_deployed_api() {
        return _.get(this.item, 'deploy_api', INACTIVE) == ACTIVE
      },
      is_deployed_ui() {
        return _.get(this.item, 'deploy_ui', INACTIVE) == ACTIVE
      },
      is_deployed: {
        get() {
          return _.get(this.item, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var deploy_mode = value === false ? DEPLOY_MODE_BUILD : DEPLOY_MODE_RUN

            var attrs = {
              deploy_mode
            }

            this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
          }

          if (value === false) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonClass: 'ttu b',
              cancelButtonClass: 'ttu b',
              confirmButtonText: 'Turn pipe off',
              cancelButtonText: 'Cancel',
              type: 'warning'
            }).then(() => {
              doSet()
              this.$store.track("Turned Pipe Off")
            }).catch(() => {
              this.$store.track("Turned Pipe Off (Canceled)")
            })
          } else {
            doSet()
            this.$store.track("Turned Pipe On")
          }
        }
      },
      pipe_route() {
        return { name: ROUTE_PIPES, params: { eid: this.item.eid } }
      },
      schedule_str() {
        return pipe_util.getDeployScheduleStr(this.item.schedule)
      },
      api_endpoint_url() {
        var identifier = pipe_util.getIdentifier(this.item)
        return pipe_util.getDeployApiUrl(identifier)
      },
      runtime_url() {
        return pipe_util.getDeployRuntimeUrl(this.item.eid)
      },
      schedule_tooltip() {
        return this.is_deployed_schedule ? this.schedule_str : 'Not scheduled'
      },
      api_endpoint_tooltip() {
        return this.is_deployed_api ? this.api_endpoint_url : 'Not deployed as an API endpoint'
      },
      runtime_tooltip() {
        return this.is_deployed_ui ? this.runtime_url : 'Not deployed as a Flex.io Web Interface'
      }
    },
    methods: {
      openPipe() {
        this.$store.track('Opened Pipe', this.getAnalyticsPayload(this.item))
        this.$router.push(this.pipe_route)
      },
      getAnalyticsPayload(pipe) {
        var analytics_payload = _.pick(pipe, ['eid', 'name', 'alias'])
        _.assign(analytics_payload, { title: pipe.name })

        return analytics_payload
      },
      onCommand(cmd) {
        switch (cmd) {
          case 'open':      return this.openPipe()
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
