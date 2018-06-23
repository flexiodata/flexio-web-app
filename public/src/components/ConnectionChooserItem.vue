<template>
  <article :class="cls" @click="onClick">
    <div class="tc css-valign" v-if="layout == 'grid'">
      <div class="absolute z-1" style="top: -9px; right: 18px" v-if="showStatus">
        <i class="el-icon-success dark-green bg-white ba bw1 b--white br-100" v-if="is_available"></i>
        <i class="el-icon-error dark-red bg-white ba bw1 b--white br-100" v-else></i>
      </div>
      <service-icon :type="ctype" class="dib v-mid br2 square-5" />
      <div class="mid-gray f6 fw6 mt2 cursor-default">{{item.name}}</div>
    </div>
    <div class="flex flex-row items-center" v-else>
      <i class="material-icons mid-gray md-18 b mr3" v-if="showSelectionCheckmark && is_selected">check</i>
      <i class="material-icons mid-gray md-18 b mr3" style="color: transparent" v-else-if="showSelectionCheckmark">check</i>
      <div class="flex flex-row items-center relative mr3">
        <service-icon class="br1 square-3" :type="ctype" :empty-cls="''" />
        <div class="absolute z-1" style="top: -9px; right: -6px" v-if="showStatus">
          <i class="el-icon-success dark-green bg-white ba b--white br-100 f8" v-if="is_available"></i>
          <i class="el-icon-error dark-red bg-white ba b--white br-100 f8" v-else></i>
        </div>
      </div>
      <div class="flex-fill flex flex-column">
        <div class="mid-gray f5 fw6">{{item.name}}</div>
        <div class="light-silver f8 lh-copy code" v-if="identifier.length > 0">{{identifier}}</div>
      </div>
      <el-button
        size="tiny"
        class="ttu b"
        @click="$emit('item-fix', item)"
        v-if="showStatus && !is_available"
      >
        Fix Connection
      </el-button>
    </div>
  </article>
</template>

<script>
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import ServiceIcon from './ServiceIcon.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      layout: {
        type: String,
        default: 'list'
      },
      connectionEid: {
        type: String,
        required: false
      },
      showStatus: {
        type: Boolean,
        default: true
      },
      showSelectionCheckmark: {
        type: Boolean,
        default: false
      },
      selectedCls: {
        type: String
      }
    },
    components: {
      ServiceIcon
    },
    computed: {
      eid() {
        return _.get(this.item, 'eid', '')
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.item, 'connection_status', '')
      },
      identifier() {
        var cid = _.get(this.item, 'alias', '')
        return cid.length > 0 ? cid : this.eid
      },
      is_selected() {
        return this.eid.length > 0 ? this.connectionEid == this.eid : false
      },
      is_available() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      cls() {
        var sel_cls = this.selectedCls ? this.selectedCls : 'bg-near-white'
        sel_cls = this.is_selected ? sel_cls : 'bg-white'

        if (_.get(this, 'layout', '') == 'list') {
          return 'css-item pointer pa3 ' + sel_cls
        } else {
          return 'pointer dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10 ' + sel_cls
        }
      }
    },
    methods: {
      onClick: _.debounce(function() {
        this.$emit('item-activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>

<style lang="stylus" scoped>
  .css-item
    border-top: 1px solid #eee
    border-bottom: 1px solid #eee
    &:hover
      background-color: #ecf5ff // match Element UI button
      border-color: #c6e2ff     // match Element UI button
      position: relative
  .css-item + .css-item
    margin-top: -1px
</style>
