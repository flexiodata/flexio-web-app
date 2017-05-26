<template>
  <article class="flex flex-row items-center pv3 ph2 bb b--black-10 pointer no-select trans-pm css-pipe-item"
    @click="openPipe"
    @mouseenter="onMouseEnter"
    @mouseover="onMouseOver"
    @mouseleave="onMouseLeave"
  >
    <div class="w-two-thirds w-50-ns truncate">
      <div class="flex flex-row items-center mh2">
        <div class="flex-fill fw6 f6 f5-ns lh-title truncate">
          <h1 class="f6 f5-ns fw6 lh-title dark-gray mv0 css-pipe-title">{{item.name}}</h1>
          <div
            class="mw7 hint--bottom hint--large"
            :aria-label="item.description"
            v-show="item.description.length > 0"
          >
            <h2 class="f6 fw4 mt1 mb0 black-60 truncate">{{item.description}}</h2>
          </div>
        </div>
      </div>
    </div>
    <div class="w-30-ns dn db-ns truncate">
      <div class="flex flex-row items-center mh2">
        <div class="flex-none">
          <task-summary-list
            :tasks="tasks"
            :icons-only="true"
          ></task-summary-list>
        </div>
      </div>
    </div>
    <div class="w-third w-20-ns truncate">
      <div class="flex flex-row items-center mh2">
        <div class="flex-fill">
          <toggle-button
            class="hint--top center"
            style="display: block"
            :aria-label="is_scheduled ? 'Scheduled' : 'Not Scheduled'"
            :checked="is_scheduled"
            @click.stop="toggleScheduled"
            @dblclick.stop
          ></toggle-button>
        </div>
        <div class="flex-none">
          <a
            class="f5 b dib pointer pa1 black-60 ba br2 popover-trigger"
            ref="dropdownTrigger"
            tabindex="0"
            :class="is_hover ? 'b--black-20' : 'b--transparent'"
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

              :options="[{
                id: 'open',
                label: 'Open',
                icon: 'file_upload'
              },{
                id: 'edit',
                label: 'Edit',
                icon: 'edit'
              },{
                id: 'duplicate',
                label: 'Duplicate',
                icon: 'content_copy'
              },{
                id: 'share',
                label: 'Share',
                icon: 'share'
              },{
                id: 'schedule',
                label: 'Schedule',
                icon: 'date_range'
              },{
                id: 'delete',
                label: 'Move to Trash',
                icon: 'delete'
              }]"

              @select="onDropdownItemClick"
              @close="$refs.dropdown.close()"
            ></ui-menu>
          </ui-popover>
        </div>
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
  import ConnectionIcon from './ConnectionIcon.vue'
  import ToggleButton from './ToggleButton.vue'
  import TaskSummaryList from './TaskSummaryList.vue'

  export default {
    props: ['item'],
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
      is_scheduled() {
        return _.get(this.item, 'schedule_status') == SCHEDULE_STATUS_ACTIVE ? true : false
      },
      tasks() {
        return _.get(this.item, 'task', [])
      },
      pipe_route() {
        return { name: ROUTE_PIPEHOME, params: { eid: this.item.eid } }
      }
    },
    methods: {
      getTaskConnectionType(task_type) {
        var task = _.find(this.item.task, { type: task_type })
        return _.get(task, 'metadata.connection_type')
      },
      openPipe() {
        this.$router.push(this.pipe_route)
      },
      trashPipe() {
        var attrs = {
          eid_status: OBJECT_STATUS_TRASH
        }

        this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
      },
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'open':      return this.openPipe()
          case 'edit':      return this.$emit('edit', this.item)
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'share':     return this.$emit('share', this.item)
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

  .css-pipe-item:hover .css-pipe-title {
    color: @blue;
  }
</style>
