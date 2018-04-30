<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="tc css-valign cursor-default" v-if="layout == 'grid'">
      <service-icon :type="ctype" class="dib v-mid br2 square-5"></service-icon>
      <div class="mid-gray f6 fw6 mt2">{{cname}}</div>
    </div>
    <div class="flex flex-row items-center cursor-default" v-else>
      <i class="material-icons mid-gray md-18 b mr3" v-if="showCheckmark && is_selected">check</i>
      <i class="material-icons mid-gray md-18 b mr3" style="color: transparent" v-else-if="showCheckmark && !is_selected">check</i>
      <div class="flex flex-row items-center relative mr3">
        <service-icon class="br1 square-3" :type="ctype" :url="url" :empty-cls="''" />
        <div class="absolute z-1" style="top: -7px; right: -7px" v-if="showStatus">
          <i class="material-icons dark-green bg-white br-100 f7" v-if="is_available">check_circle</i>
          <i class="material-icons dark-red bg-white br-100 f7" v-else>cancel</i>
        </div>
      </div>

      <div class="flex-fill flex flex-column">
        <div class="mid-gray f5 fw6 cursor-default">{{cname}}</div>
        <div class="light-silver f8 lh-copy code" v-if="showIdentifier && identifier.length > 0 && !is_home">{{identifier}}</div>
        <div class="bt b--black-05" style="padding-top: 2px; margin-top: 2px; max-width: 12rem" v-if="showUrl && url.length > 0">
          <div class="light-silver f8 lh-copy truncate">{{url}}</div>
        </div>
      </div>
      <div class="ml2" v-if="showDropdown && !is_home">
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link dib pointer pa1 black-30 hover-black">
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
            <!--
            <el-dropdown-item class="flex flex-row items-center ph2" command="edit"><i class="material-icons mr3">edit</i> Edit</el-dropdown-item>
            <div class="mv2 bt b--black-10"></div>
            -->
            <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import { CONNECTION_TYPE_HOME } from '../constants/connection-type'
  import ServiceIcon from './ServiceIcon.vue'

  export default {
    props: {
      'layout': {
        type: String,
        default: 'list' // 'list' or 'grid'
      },
      'item': {
        type: Object,
        required: true
      },
      'item-cls': {
        type: String,
        default: ''
      },
      'item-style': {
        type: String,
        default: ''
      },
      'selected-item': {
        type: Object,
        default: () => { return {} }
      },
      'selected-cls': {
        type: String,
        default: 'bg-light-gray'
      },
      'show-status': {
        type: Boolean,
        default: true
      },
      'show-identifier': {
        type: Boolean,
        default: true
      },
      'show-url': {
        type: Boolean,
        default: true
      },
      'show-checkmark': {
        type: Boolean,
        default: false
      },
      'show-dropdown': {
        type: Boolean,
        default: false
      },
      'dropdown-items': {
        type: Array,
        default: () => { return ['edit','delete'] }
      }
    },
    components: {
      ServiceIcon
    },
    data() {
      return {
        is_hover: false,
        is_dropdown_open: false
      }
    },
    computed: {
      eid() {
        return _.get(this.item, 'eid', '')
      },
      cname() {
        return _.get(this.item, 'name', '')
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.item, 'connection_status', '')
      },
      url() {
        return _.get(this.item, 'connection_info.url', '')
      },
      identifier() {
        var cid = _.get(this.item, 'alias', '')
        return cid.length > 0 ? cid : _.get(this.item, 'eid', '')
      },
      is_home() {
        return this.ctype == CONNECTION_TYPE_HOME
      },
      is_selected() {
        return this.eid.length > 0
          ? _.get(this.selectedItem, 'eid') === this.eid
          : _.get(this.selectedItem, 'connection_type') === this.ctype
      },
      is_available() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      cls() {
        var sel_cls = this.is_selected ? this.selectedCls : ''

        if (this.itemCls.length > 0)
          return this.itemCls + ' ' + sel_cls

        if (_.get(this, 'layout', '') == 'list')
          return 'min-w5 pa3 bb b--black-05 darken-05 ' + sel_cls
           else
          return 'dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10 ' + sel_cls
      }
    },
    methods: {
      onCommand(cmd) {
        switch (cmd)
        {
          case 'edit':      return this.$emit('edit', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      },
      onClick: _.debounce(function() {
        this.$emit('activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>
