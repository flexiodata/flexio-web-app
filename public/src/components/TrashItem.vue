<template>
  <article class="css-trash-item mw8 ma0 ma3-ns pv3 pv2a-ns ph3 bb ba-ns br2-ns cursor-default no-select shadow-sui-segment-ns trans-pm">
    <div class="flex flex-row items-center">
      <div class="flex-none mr2">
        <connection-icon :type="input_type" class="dib v-mid br2 fx-square-3"></connection-icon>
        <i class="material-icons md-24 black-40 v-mid rotate-270" style="margin: 0 -4px">arrow_drop_down</i>
        <connection-icon :type="output_type" class="dib v-mid br2 fx-square-3"></connection-icon>
      </div>
      <div class="flex-fill mh2 fw6 f6 f5-ns black-60 mv0 lh-title truncate">
        <span :title="item.name">{{item.name}}</span>
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
              id: 'restore',
              label: 'Restore',
              icon: 'keyboard_return'
            },{
              id: 'delete',
              label: 'Permanently Delete',
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
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item'],
    components: {
      ConnectionIcon
    },
    computed: {
      input_type() { return this.getTaskConnectionType(TASK_TYPE_INPUT) },
      output_type() { return this.getTaskConnectionType(TASK_TYPE_OUTPUT) },
    },
    methods: {
      getTaskConnectionType(task_type) {
        var task = _.find(this.item.task, { type: task_type })
        return _.get(task, 'metadata.connection_type')
      },
      restorePipe() {
        var attrs = {
          eid_status: OBJECT_STATUS_AVAILABLE
        }

        this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
      },
      deletePipe() {
        this.$store.dispatch('deletePipe', { attrs: this.item })
      },
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'restore': return this.restorePipe()
          case 'delete':  return this.deletePipe()
        }
      }
    }
  }
</script>

<style lang="less" scoped>
  .css-trash-item {
    border-color: rgba(34, 36, 38, 0.15);

    &:hover {
      background-color: rgba(0,0,0,0.05);
      border-color: rgba(0,0,0,0.2);
    }
  }
</style>
