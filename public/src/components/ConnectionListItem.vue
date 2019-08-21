<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="flex flex-row items-center cursor-default">
      <i class="material-icons md-18 b mr3" v-if="showCheckmark && is_selected">check</i>
      <i class="material-icons md-18 b mr3" style="color: transparent" v-else-if="showCheckmark && !is_selected">check</i>
      <div class="flex flex-row items-center relative mr3">
        <ServiceIcon class="br1 square-3" :type="ctype" :empty-cls="''" />
        <div class="absolute z-1" style="top: -9px; right: -6px" v-if="showStatus && !is_flexio">
          <i class="el-icon-success dark-green bg-white ba b--white br-100 f8" v-if="is_available"></i>
          <i class="el-icon-error dark-red bg-white ba b--white br-100 f8" v-else></i>
        </div>
      </div>
      <div class="flex-fill f5 fw6 cursor-default mr1 lh-title truncate">{{cname}}</div>
      <div
        class="flex-none ml2"
        @click.stop
        v-if="showDropdown && !is_flexio"
        v-require-rights:connection.update.hidden
      >
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

  export default {
    props: {
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
        default: 'relative bg-nearer-white'
      },
      showDropdown: {
        type: Boolean,
        default: false
      },
      showStatus: {
        type: Boolean,
        default: true
      },
      showCheckmark: {
        type: Boolean,
        default: false
      },
    },
    components: {
      ServiceIcon
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
      cls() {
        var sel_cls = this.is_selected ? this.selectedCls : ''

        if (this.itemCls.length > 0) {
          return this.itemCls + ' ' + sel_cls
        } else {
          return 'min-w5 pa3 bb b--black-05 bg-white hover-bg-nearer-white ' + sel_cls
        }
      }
    },
    methods: {
      onCommand(cmd) {
        switch (cmd) {
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
