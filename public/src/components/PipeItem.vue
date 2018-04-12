<template>
  <article
    class="css-list-item mv3-l pv3 pv2a-l ph3 bb ba-l br2-l no-select shadow-sui-segment-l trans-pm"
    :class="isTrash ? 'css-trash-item' : 'pointer'"
    @click="openPipe"
  >
    <div class="flex flex-row items-center">
      <div class="flex-none mr2">
        <service-icon :type="input_type" class="dib v-mid br2 square-3"></service-icon>
        <i class="material-icons md-24 black-40 v-mid rotate-270" style="margin: 0 -4px" v-if="false">arrow_drop_down</i>
        <service-icon :type="output_type" class="dib v-mid br2 square-3 ml2"></service-icon>
      </div>
      <div class="flex-fill mh2 fw6 f6 f5-ns">
        <div class="flex flex-row items-center">
          <h3 class="f6 f5-ns fw6 lh-title dark-gray mv0 css-list-title">{{item.name}}</h3>
          <div class="dib f8 silver pv1 ph2 ml2 bg-black-05 br1" v-if="item.alias">{{item.alias}}</div>
        </div>
        <div class="dn db-l mw7" v-if="has_description">
          <h4 class="f6 fw4 mt1 mb0 mid-gray lh-copy">{{item.description}}</h4>
        </div>
      </div>
      <div class="dn db-l flex-none mh3 f7 fw6 mid-gray" style="min-width: 160px" v-if="false">
        <span
          class="hint--bottom hint--multiline"
          :aria-label="follower_tooltip"
        >{{follower_str}} (@{{owner_username}})</span>
      </div>
      <div class="flex-none mr2 nt3 nb3" v-if="!isTrash">
        <div class="pv3" @click.stop="toggleScheduled">
          <toggle-button
            class="hint--bottom"
            style="display: block"
            :aria-label="is_scheduled ? 'Scheduled' : 'Not Scheduled'"
            :checked="is_scheduled"
            :prevent-default="true"
            @click.stop="toggleScheduled"
          ></toggle-button>
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
            <el-dropdown-item class="flex flex-row items-center ph2" command="schedule"><i class="material-icons mr3">date_range</i> Schedule</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="deploy"><i class="material-icons mr3">archive</i> Deploy</el-dropdown-item>
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
  import ToggleButton from './ToggleButton.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'index': {
        type: Number
      },
      'menu-items': {
        type: Array
      },
      'is-trash': {
        type: Boolean,
        default: false
      }
    },
    components: {
      ServiceIcon,
      ToggleButton
    },
    computed: {
      input_type() {
        return this.getTaskConnectionType(TASK_OP_INPUT)
      },
      output_type() {
        return this.getTaskConnectionType(TASK_OP_OUTPUT)
      },
      has_description() {
        return _.get(this.item, 'description', '').length > 0
      },
      is_scheduled() {
        return _.get(this.item, 'schedule_status') == SCHEDULE_STATUS_ACTIVE ? true : false
      },
      owner_username() {
        return _.get(this.item, 'owned_by.user_name', '')
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
      getTaskConnectionType(task_op) {
        var task = _.find(this.item.task, { op: task_op })
        return _.get(task, 'metadata.connection_type')
      },
      openPipe() {
        if (this.isTrash)
          return

        this.$router.push(this.pipe_route)
      },
      trashPipe() {
        var attrs = {
          eid_status: OBJECT_STATUS_TRASH
        }

        this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
      },
      onCommand(cmd) {
        switch (cmd)
        {
          case 'open':      return this.openPipe()
          case 'edit':      return this.$emit('edit', this.item)
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'share':     return this.$emit('share', this.item)
          case 'embed':     return this.$emit('embed', this.item)
          case 'schedule':  return this.$emit('schedule', this.item)
          case 'deploy':    return this.$emit('deploy', this.item)
          case 'delete':    return this.$emit('delete', this.item)
          case 'trash':     return this.trashPipe()
        }
      },
      toggleScheduled() {
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
    }
  }
</script>
