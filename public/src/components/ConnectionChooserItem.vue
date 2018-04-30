<template>
  <article :class="cls" @click="onClick">
    <div class="tc css-valign" v-if="layout == 'grid'">
      <service-icon :type="ctype" class="dib v-mid br2 square-5" />
      <div class="mid-gray f6 fw6 mt2 cursor-default">{{item.name}}</div>
    </div>
    <div class="flex flex-row items-center" v-else>
      <i class="material-icons mid-gray md-18 b mr3" v-if="showSelectionCheckmark && is_selected">check</i>
      <i class="material-icons mid-gray md-18 b mr3" style="color: transparent" v-else-if="showSelectionCheckmark">check</i>
      <div class="flex flex-row items-center relative mr3">
        <service-icon :type="ctype" class="br1 square-3" />
        <div class="absolute z-1" style="top: -7px; right: -6px" v-if="showStatus">
          <i class="material-icons dark-green bg-white br-100 f7" v-if="is_available">check_circle</i>
          <i class="material-icons dark-red bg-white br-100 f7" v-else>cancel</i>
        </div>
      </div>
      <div class="flex-fill flex flex-column">
        <div class="mid-gray f5 fw6">{{item.name}}</div>
        <div class="light-silver f8 lh-copy code" v-if="identifier.length > 0">{{identifier}}</div>
      </div>
      <el-button
        type="plain"
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
      'item': {
        type: Object,
        required: true
      },
      'layout': {
        type: String,
        default: 'list'
      },
      'connection-eid': {
        type: String,
        required: false
      },
      'show-status': {
        type: Boolean,
        default: true
      },
      'show-selection-checkmark': {
        type: Boolean,
        default: false
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
        var sel_cls = this.is_selected ? 'bg-light-gray' : 'bg-white'

        if (this.showSelectionCheckmark)
          sel_cls = 'bg-white'

        if (_.get(this, 'layout', '') == 'list')
          return 'css-item pointer pa3 darken-05 ' + sel_cls
           else
          return 'pointer dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10 ' + sel_cls
      }
    },
    methods: {
      onClick: _.debounce(function() {
        this.$emit('item-activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>

<style scoped>
  .css-item + .css-item {
    border-top: 1px solid #eee;
  }
</style>
