<template>
  <article class="css-pipe-item mw8 ma0 ma3-ns pv3 pv2a-ns ph3 bb ba-ns br2-ns cursor-default no-select shadow-sui-segment-ns trans-pm"
    @dblclick="openPipe"
  >
    <div class="flex flex-row items-center">
      <div class="flex-none mr2">
        <connection-icon :type="input_type" class="dib v-mid br2 square-3"></connection-icon>
        <i class="material-icons md-24 black-40 v-mid rotate-270" style="margin: 0 -4px">arrow_drop_down</i>
        <connection-icon :type="output_type" class="dib v-mid br2 square-3"></connection-icon>
      </div>
      <div class="flex-fill mh2 fw6 f6 f5-ns black-60 mv0 lh-title truncate">
        <span :title="item.name">{{item.name}}</span>
      </div>
      <div class="flex-none ml2">
        <toggle-button
          class="hint--top"
          :aria-label="is_scheduled ? 'Scheduled' : 'Not Scheduled'"
          :checked="is_scheduled"
          @click.stop="toggleScheduled"
          @dblclick.stop
        ></toggle-button>
      </div>
      <div class="flex-none ml2">
        <a
          class="f5 b dib pointer pa2 black-60 popover-trigger"
          ref="dropdownTrigger"
          tabindex="0"
        ><i class="material-icons v-mid b">expand_more</i></a>

        <ui-popover
          trigger="dropdownTrigger"
          ref="dropdown"
          dropdown-position="bottom right"
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

  export default {
    props: ['item'],
    components: {
      ConnectionIcon,
      ToggleButton
    },
    computed: {
      input_type() { return this.getTaskConnectionType(TASK_TYPE_INPUT) },
      output_type() { return this.getTaskConnectionType(TASK_TYPE_OUTPUT) },
      is_scheduled() { return _.get(this.item, 'schedule_status') == SCHEDULE_STATUS_ACTIVE ? true : false }
    },
    methods: {
      getTaskConnectionType(task_type) {
        var task = _.find(this.item.task, { type: task_type })
        return _.get(task, 'metadata.connection_type')
      },
      openPipe() {
        this.$router.push({ name: ROUTE_PIPEHOME, params: { eid: this.item.eid } })
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

<style lang="less" scoped>
  .css-pipe-item {
    border-color: rgba(34, 36, 38, 0.15);

    &:hover {
      background-color: rgba(0,0,0,0.05);
      border-color: rgba(0,0,0,0.2);
    }
  }
</style>
