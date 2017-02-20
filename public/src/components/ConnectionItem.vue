<template>
  <article class="css-connection-item ma0 ma3-ns pv3 pv2a-ns ph3 bb ba-ns b--sui-segment br2-ns cursor-default shadow-sui-segment-ns trans-pm no-select">
    <div class="flex flex-row items-center">
      <div class="flex-none mr2">
        <connection-icon :type="item.connection_type" class="dib v-mid br2 fx-square-3 fx-square-4-ns"></connection-icon>
      </div>
      <div class="flex-fill mh2 fw6 f6 f5-ns black-60 mv0 lh-title">
        <h1 class="f6 f5-ns fw6 lh-title black mv0">{{item.name}}</h1>
        <div
          class="mw7 hint--bottom hint--large"
          :aria-label="item.description"
          v-show="item.description.length > 0"
        >
          <h2 class="f6 fw4 mt1 mb0 black-60 truncate">{{item.description}}</h2>
        </div>
      </div>
      <div class="ml2 min-w4-l">
        <div class="f6 fw6 cursor-default truncate" :class="status_cls">
          <i class="material-icons v-mid">{{status_icon}}</i>
          <span class="dn dib-l v-mid">{{status}}</span>
        </div>
      </div>
      <div class="tr ml3 ml2-ns min-w4-ns truncate f6 fw6">
          {{created}}
      </div>
      <div class="ml2 ml3-ns">
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
              id: 'edit',
              label: 'Edit',
              icon: 'edit'
            },{
              id: 'delete',
              label: 'Delete',
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
  import moment from 'moment'
  import {
    CONNECTION_STATUS_AVAILABLE,
    CONNECTION_STATUS_UNAVAILABLE,
    CONNECTION_STATUS_ERROR
  } from '../constants/connection-status'
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item'],
    components: {
      ConnectionIcon
    },
    computed: {
      status_cls() {
        switch (this.item.connection_status)
        {
          case CONNECTION_STATUS_ERROR:       return 'mid-gray'
          case CONNECTION_STATUS_AVAILABLE:   return 'dark-green'
          case CONNECTION_STATUS_UNAVAILABLE: return 'dark-red'
        }

        return ''
      },
      status_icon() {
        switch (this.item.connection_status)
        {
          case CONNECTION_STATUS_ERROR:       return 'error'
          case CONNECTION_STATUS_AVAILABLE:   return 'check_circle'
          case CONNECTION_STATUS_UNAVAILABLE: return 'cancel'
        }

        return ''
      },
      status() {
        switch (this.item.connection_status)
        {
          case CONNECTION_STATUS_ERROR:       return 'Connection Error'
          case CONNECTION_STATUS_AVAILABLE:   return 'Active'
          case CONNECTION_STATUS_UNAVAILABLE: return 'Not Active'
        }

        return ''
      },
      created() {
        return moment(this.item.created).format('LL')
      }
    },
    methods: {
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'edit':      return this.$emit('edit', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      },
    }
  }
</script>

<style lang="less" scoped>
  .css-connection-item {
    border-color: rgba(34, 36, 38, 0.15);

    &:hover {
      background-color: rgba(0,0,0,0.05);
      border-color: rgba(0,0,0,0.2);
    }
  }
</style>
