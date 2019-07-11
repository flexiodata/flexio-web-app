<template>
  <article :class="cls" @click="onClick">
    <div class="tc css-valign" v-if="layout == 'grid'">
      <div class="absolute z-1" style="top: -9px; right: 18px" v-if="showStatus">
        <i class="el-icon-success dark-green bg-white ba bw1 b--white br-100" v-if="is_available"></i>
        <i class="el-icon-error dark-red bg-white ba bw1 b--white br-100" v-else></i>
      </div>
      <service-icon :type="ctype" class="dib v-mid br2 square-5" />
      <div class="f6 fw6 mt2 cursor-default">{{item.short_description}}</div>
    </div>
    <div class="flex flex-row items-center" v-else>
      <i class="material-icons md-18 b mr3" v-if="showSelectionCheckmark && is_selected">check</i>
      <i class="material-icons md-18 b mr3" style="color: transparent" v-else-if="showSelectionCheckmark">check</i>
      <div class="flex flex-row items-center relative mr3">
        <service-icon class="br1 square-3" :type="ctype" :empty-cls="''" />
        <div class="absolute z-1" style="top: -9px; right: -6px" v-if="showStatus">
          <i class="el-icon-success dark-green bg-white ba b--white br-100 f8" v-if="is_available"></i>
          <i class="el-icon-error dark-red bg-white ba b--white br-100 f8" v-else></i>
        </div>
      </div>
      <div class="flex-fill flex flex-column">
        <div class="f5 fw6">{{item.short_description}}</div>
        <div class="flex flex-row items-center">
          <div class="light-silver" style="margin: 3px 3px 0 0" v-if="is_storage">
            <i class="db material-icons hint--top" aria-label="Storage connection" style="font-size: 14px">layers</i>
          </div>
          <div class="light-silver" style="margin: 3px 3px 0 0" v-if="is_email">
            <i class="db material-icons hint--top" aria-label="Email connection" style="font-size: 14px">email</i>
          </div>
          <div class="flex-fill light-silver f8 lh-copy code" v-if="identifier.length > 0">{{identifier}}</div>
        </div>
      </div>
      <el-button
        plain
        size="tiny"
        class="ttu fw6"
        @click="$emit('item-fix', item)"
        v-if="showStatus && !is_available"
      >
        Fix Connection
      </el-button>
      <slot name="buttons"></slot>
    </div>
  </article>
</template>

<script>
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import ServiceIcon from '@comp/ServiceIcon'
  import MixinConnection from '@comp/mixins/connection'

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
      connectionIdentifier: {
        type: String,
        default: ''
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
        type: String,
        required: false
      }
    },
    mixins: [MixinConnection],
    components: {
      ServiceIcon
    },
    computed: {
      eid() {
        return _.get(this.item, 'eid', '')
      },
      alias() {
        return _.get(this.item, 'alias', '')
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      cstatus() {
        return _.get(this.item, 'connection_status', '')
      },
      identifier() {
        return this.alias.length > 0 ? this.alias : this.eid
      },
      is_selected() {
        var cid = this.connectionIdentifier
        return cid.length > 0 && (cid == this.eid || cid == this.alias)
      },
      is_available() {
        return this.cstatus == CONNECTION_STATUS_AVAILABLE
      },
      is_storage() {
        return this.$_Connection_isStorage(this.ctype)
      },
      is_email() {
        return this.$_Connection_isEmail(this.ctype)
      },
      cls() {
        var sel_cls = this.selectedCls ? this.selectedCls : 'bg-nearer-white'
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
