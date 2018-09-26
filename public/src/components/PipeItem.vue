<template>
  <article
    class="css-list-item mv3-l pv3 ph3 bb ba-l br2-l pointer no-select shadow-sui-segment-l trans-pm"
    @click="openPipe"
  >
    <div class="flex flex-row items-center">
      <div class="flex-fill mr2 fw6 f6 f5-ns">
        <div class="flex-ns flex-row items-center">
          <h3 class="f6 f5-ns fw6 lh-title dark-gray mv0 mr2 css-list-title">{{item.name}}</h3>
          <div class="dib" v-if="item.alias">
            <div class="f8 silver mt1 mt0-ns pv1 ph2 bg-black-05 br1">{{item.alias}}</div>
          </div>
        </div>
        <div class="dn db-l mw7" v-if="has_description">
          <h4 class="f6 fw4 mt1 mb0 lh-copy">{{item.description}}</h4>
        </div>
      </div>
      <div class="flex-none mr4 dn db-ns">
        <ServiceIcon :type="input_type" class="dib v-mid br2 square-3 mr2" v-show="input_type.length > 0" />
        <ServiceIcon :type="output_type" class="dib v-mid br2 square-3" v-show="output_type.length > 0" />
      </div>
      <div class="flex-none nt3 nb3">
        <div class="pv3" @click.stop>
          <el-switch
            class="hint--bottom"
            :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
            v-model="is_deployed"
          />
        </div>
      </div>
      <div class="flex-none pl2 nt3 nr3 nb3" @click.stop>
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link dib pointer pa3 black-30 hover-black">
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem; margin-left: -12px; margin-top: -8px" slot="dropdown">
            <el-dropdown-item class="flex flex-row items-center ph2" command="open"><i class="material-icons mr3">edit</i> Edit</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="duplicate"><i class="material-icons mr3">content_copy</i> Duplicate</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="schedule" v-if="false"><i class="material-icons mr3">date_range</i> Schedule</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="deploy" v-if="false"><i class="material-icons mr3">archive</i> Deploy</el-dropdown-item>
            <div class="mv2 bt b--black-10"></div>
            <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import { ROUTE_PIPES } from '../constants/route'
  import { TIMEZONE_UTC } from '../constants/timezone'
  import { SCHEDULE_STATUS_ACTIVE, SCHEDULE_STATUS_INACTIVE, SCHEDULE_FREQUENCY_DAILY } from '../constants/schedule'
  import { TASK_OP_INPUT, TASK_OP_OUTPUT } from '../constants/task-op'
  import { OBJECT_STATUS_TRASH } from '../constants/object-status'
  import util from '../utils'
  import ServiceIcon from './ServiceIcon.vue'

  const PIPE_MODE_UNDEFINED = ''
  const PIPE_MODE_BUILD     = 'B'
  const PIPE_MODE_RUN       = 'R'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      ServiceIcon
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
      /*
      is_scheduled: {
        get() {
          return _.get(this.item, 'schedule_status') == SCHEDULE_STATUS_ACTIVE ? true : false
        },
        set(val) {
          var attrs = {
            schedule_status: this.is_scheduled ? SCHEDULE_STATUS_INACTIVE : SCHEDULE_STATUS_ACTIVE
          }

          // if we don't have a schedule object, default to scheduling the pipe daily at 8am
          if (!_.isObject(_.get(this.item, 'schedule')))
          {
            _.assign(attrs, {
              schedule: {
                frequency: SCHEDULE_FREQUENCY_DAILY,
                timezone: TIMEZONE_UTC,
                days: [],
                times: [{ hour: 8, minute: 0 }]
              }
            })
          }

          this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
        }
      },
      */
      is_deployed: {
        get() {
          return _.get(this.item, 'pipe_mode') == PIPE_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var pipe_mode = value === false ? PIPE_MODE_BUILD : PIPE_MODE_RUN

            var attrs = {
              pipe_mode
            }

            this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
          }

          if (value === false) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonText: 'TURN PIPE OFF',
              cancelButtonText: 'CANCEL',
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
      owner_username() {
        return _.get(this.item, 'owned_by.username', '')
      },
      owner_fullname() {
        return _.get(this.item, 'owned_by.first_name', '')+' '+_.get(this.item, 'owned_by.last_name', '')
      },
      followers() {
        return _.get(this.item, 'followed_by', [])
      },
      follower_count() {
        return _.size(this.followers) + 1 /* owner */
      },
      follower_str() {
        var cnt = this.follower_count
        return util.pluralize(cnt, cnt+' '+'members', cnt+' '+'member')
      },
      follower_tooltip() {
        var tooltip = 'Owner: '+this.owner_fullname
        return tooltip
      },
      tasks() {
        return _.get(this.item, 'task', [])
      },
      pipe_route() {
        return { name: ROUTE_PIPES, params: { eid: this.item.eid } }
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
          case 'edit':      return this.$emit('edit', this.item)
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'share':     return this.$emit('share', this.item)
          case 'embed':     return this.$emit('embed', this.item)
          case 'schedule':  return this.$emit('schedule', this.item)
          case 'deploy':    return this.$emit('deploy', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
