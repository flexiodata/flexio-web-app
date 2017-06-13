<template>
  <article
    class="css-pipe-item mv3-l pv3 pv2a-l ph3 bb ba-l br2-l no-select shadow-sui-segment-l trans-pm"
    :class="isTrash ? 'css-trash-item' : 'pointer'"
    @click="openPipe"
    @mouseenter="onMouseEnter"
    @mouseover="onMouseOver"
    @mouseleave="onMouseLeave"
  >
    <div class="flex flex-row items-center">
      <div class="flex-none mr2">
        <connection-icon :type="input_type" class="dib v-mid br2 square-3"></connection-icon>
        <i class="material-icons md-24 black-40 v-mid rotate-270" style="margin: 0 -4px">arrow_drop_down</i>
        <connection-icon :type="output_type" class="dib v-mid br2 square-3"></connection-icon>
      </div>
      <div class="flex-fill mh2 fw6 f6 f5-ns">
        <h1 class="f6 f5-ns fw6 lh-title dark-gray mv0 css-pipe-title">{{item.name}}</h1>
        <div class="dn db-l mw7" v-if="has_description">
          <h2 class="f6 fw4 mt1 mb0 mid-gray lh-copy">{{item.description}}</h2>
        </div>
      </div>
      <div class="dn db-l flex-none mh3 f7 fw6 mid-gray" style="min-width: 160px">

        <span
          class="hint--bottom hint--multiline"
          :aria-label="follower_tooltip"
        >{{follower_str}} (@{{owner_username}})</span>
      </div>
      <div class="flex-none mr2">
        <div :class="isTrash ? 'o-40 no-pointer-events' : ''">
          <toggle-button
            class="hint--bottom"
            style="display: block"
            :aria-label="is_scheduled ? 'Scheduled' : 'Not Scheduled'"
            :checked="is_scheduled"
            @click.stop="toggleScheduled"
            @dblclick.stop
          ></toggle-button>
        </div>
      </div>
      <div class="flex-none ml2">
        <a
          class="f5 b dib pointer pa1 black-60 popover-trigger"
          ref="dropdownTrigger"
          tabindex="0"
          @click.stop
        ><i class="material-icons v-mid b">expand_more</i></a>

        <ui-popover
          trigger="dropdownTrigger"
          ref="dropdown"
          dropdown-position="bottom right"
          @open="is_dropdown_open = true"
          @close="is_dropdown_open = false"
          v-if="is_hover || is_dropdown_open"
        >
          <ui-menu
            contain-focus
            has-icons
            :options="menu_items"
            @select="onDropdownItemClick"
            @close="$refs.dropdown.close()"
          ></ui-menu>
        </ui-popover>
      </div>
    </div>
  </article>
</template>

<script>
  import { ROUTE_PIPEHOME } from '../constants/route'
  import { TIMEZONE_UTC } from '../constants/timezone'
  import { SCHEDULE_STATUS_ACTIVE, SCHEDULE_STATUS_INACTIVE, SCHEDULE_FREQUENCY_DAILY } from '../constants/schedule'
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import { OBJECT_STATUS_TRASH } from '../constants/object-status'
  import util from '../utils'
  import ConnectionIcon from './ConnectionIcon.vue'
  import ToggleButton from './ToggleButton.vue'
  import TaskSummaryList from './TaskSummaryList.vue'

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
      ConnectionIcon,
      ToggleButton,
      TaskSummaryList
    },
    data() {
      return {
        is_hover: false,
        is_dropdown_open: false
      }
    },
    computed: {
      input_type() {
        return this.getTaskConnectionType(TASK_TYPE_INPUT)
      },
      output_type() {
        return this.getTaskConnectionType(TASK_TYPE_OUTPUT)
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
        return { name: ROUTE_PIPEHOME, params: { eid: this.item.eid } }
      },
      menu_items() {
        if (_.isArray(this.menuItems))
          return this.menuItems

        return [{
          id: 'open',
          label: 'Open',
          icon: 'file_upload'
        }/*,{
          id: 'edit',
          label: 'Edit',
          icon: 'edit'
        }*/,{
          id: 'duplicate',
          label: 'Duplicate',
          icon: 'content_copy'
        },{
          id: 'share',
          label: 'Share',
          icon: 'person_add'
        },{
          id: 'embed',
          label: 'Embed',
          icon: 'share'
        },{
          id: 'schedule',
          label: 'Schedule',
          icon: 'date_range'
        },{
          id: 'delete',
          label: 'Move to Trash',
          icon: 'delete'
        }]
      }
    },
    methods: {
      getTaskConnectionType(task_type) {
        var task = _.find(this.item.task, { type: task_type })
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
      onDropdownItemClick(menu_item) {
        // custom menu passed to us; fire back the event
        if (_.isArray(this.menuItems))
          return this.$emit('dropdown-item-click', menu_item, this.item)

        switch (menu_item.id)
        {
          case 'open':      return this.openPipe()
          case 'edit':      return this.$emit('edit', this.item)
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'share':     return this.$emit('share', this.item)
          case 'embed':     return this.$emit('embed', this.item)
          case 'schedule':  return this.$emit('schedule', this.item)
          case 'delete':    return this.trashPipe()
        }
      },
      onMouseEnter() {
        this.is_hover = true
      },
      onMouseLeave() {
        this.is_hover = false
      },
      onMouseOver() {
        this.is_hover = true
      },
      toggleScheduled() {
        var attrs = {
          schedule_status: this.is_scheduled ? SCHEDULE_STATUS_INACTIVE : SCHEDULE_STATUS_ACTIVE
        }

        // if we don't have a schedule object, default to scheduling the pipe daily at 8am
        if (!_.isObject(_.get(this.item, 'schedule')))
        {
          _.extend(attrs, {
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

<style lang="less">
  // match .blue color to Material Design's 'Blue A600' color
  @blue: #1e88e5;

  .css-pipe-item {
    border-color: rgba(34, 36, 38, 0.15);

    &:hover {
      background-color: rgba(0,0,0,0.05);
      border-color: rgba(0,0,0,0.2);
    }
  }

  .css-pipe-item:not(.css-trash-item):hover .css-pipe-title {
    color: @blue;
  }
</style>
