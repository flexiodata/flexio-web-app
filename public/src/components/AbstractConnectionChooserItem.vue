<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="tc css-valign cursor-default" v-if="layout == 'grid'">
      <ServiceIcon :type="ctype" class="dib v-mid br2 square-5" />
      <div class="f6 fw6 mt2">{{cname}}</div>
    </div>
    <div class="flex flex-row items-center cursor-default" v-else>
      <i class="material-icons md-18 b mr3" v-if="showCheckmark && is_selected">check</i>
      <i class="material-icons md-18 b mr3" style="color: transparent" v-else-if="showCheckmark && !is_selected">check</i>
      <div class="flex flex-row items-center relative mr3">
        <ServiceIcon class="br1 square-3" :type="ctype" :url="url" :empty-cls="''" />
        <div class="absolute z-1" style="top: -9px; right: -6px" v-if="showStatus && !is_flexio">
          <i class="el-icon-success dark-green bg-white ba b--white br-100 f8" v-if="is_available"></i>
          <i class="el-icon-error dark-red bg-white ba b--white br-100 f8" v-else></i>
        </div>
      </div>

      <div class="flex-fill flex flex-column">
        <div class="f5 fw6 cursor-default mr1">{{cname}}</div>
        <div class="bt b--black-05" style="padding-top: 2px; margin-top: 2px; max-width: 12rem" v-if="showUrl && url.length > 0">
          <div class="light-silver f8 lh-copy truncate">{{url}}</div>
        </div>
      </div>
      <div class="flex-none ml2" @click.stop v-if="showDropdown && !is_flexio">
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link dib pointer pa1 black-30 hover-black">
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
            <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import { CONNECTION_STATUS_AVAILABLE } from '@/constants/connection-status'
  import { CONNECTION_TYPE_FLEX } from '@/constants/connection-type'
  import ServiceIcon from '@/components/ServiceIcon'
  import MixinConnection from '@/components/mixins/connection'

  export default {
    props: {
      layout: {
        type: String,
        default: 'list' // 'list' or 'grid'
      },
      item: {
        type: Object,
        required: true
      },
      itemCls: {
        type: String,
        default: ''
      },
      itemStyle: {
        type: String,
        default: ''
      },
      selectedItem: {
        type: Object,
        default: () => {}
      },
      selectedCls: {
        type: String,
        default: 'bg-light-gray'
      },
      showStatus: {
        type: Boolean,
        default: true
      },
      showUrl: {
        type: Boolean,
        default: true
      },
      showCheckmark: {
        type: Boolean,
        default: false
      },
      showDropdown: {
        type: Boolean,
        default: false
      },
      dropdownItems: {
        type: Array,
        default: () => { return ['edit','delete'] }
      }
    },
    mixins: [MixinConnection],
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
      is_flexio() {
        return this.ctype == CONNECTION_TYPE_FLEX
      },
      is_selected() {
        return this.eid.length > 0
          ? _.get(this.selectedItem, 'eid') === this.eid
          : _.get(this.selectedItem, 'connection_type') === this.ctype
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
