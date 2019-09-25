<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="flex flex-row items-center">
      <i class="material-icons md-18 b mr3" v-if="showCheckmark && is_selected">check</i>
      <i class="material-icons md-18 b mr3" style="color: transparent" v-else-if="showCheckmark && !is_selected">check</i>
      <div
        class="flex flex-row items-center relative"
        :class="itemSize == 'small' ? 'mr2' : 'mr3'"
      >
        <ServiceIcon
          class="br1"
          :class="itemSize == 'small' ? 'square-2' : 'square-3'"
          :type="ctype"
        />
        <div class="absolute z-1" style="top: -9px; right: -6px" v-if="showStatus && !is_flexio">
          <i class="el-icon-success dark-green bg-white ba b--white br-100 f8" v-if="is_available"></i>
          <i class="el-icon-error dark-red bg-white ba b--white br-100 f8" v-else></i>
        </div>
      </div>
      <div
        class="flex-fill fw6 mr1 lh-title truncate"
        :class="itemSize == 'small' ? 'f6' : 'f5'"
      >
        {{cname}}
      </div>
      <el-button
        class="child item-delete-button"
        style="background: none; border:0; padding: 0; margin: 0"
        @click.stop="emitDelete"
        v-require-rights:connection.delete.hidden
        v-if="showDelete && !showDropdown"
      >
        <i class="material-icons">delete</i>
      </el-button>
      <div
        class="flex-none ml2"
        @click.stop
        v-if="showDropdown && !is_flexio"
        v-require-rights:connection.delete.hidden
      >
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link dib pointer pa1 black-30 hover-black">
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
            <el-dropdown-item
              class="flex flex-row items-center ph2"
              command="delete"
              v-show="showDelete"
            >
              <i class="material-icons mr3">delete</i> Delete
            </el-dropdown-item>
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
        default: 'item'
      },
      itemStyle: {
        type: String,
        default: ''
      },
      itemSize: {
        type: String,
        default: 'large'
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
      showDelete: {
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
        var cls = this.itemCls
        var size_cls = this.itemCls + '-' + this.itemSize
        var sel_cls = this.is_selected ? this.selectedCls : ''

        return `${cls} ${size_cls} ${sel_cls} hide-child`
      }
    },
    methods: {
      emitDelete() {
        this.$emit('delete', this.item)
      },
      onCommand(cmd) {
        switch (cmd) {
          case 'delete': emitDelete(); return
        }
      },
      onClick: _.debounce(function() {
        this.$emit('activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .item
    cursor: pointer
    user-select: none
    &:hover
      background-color: darken($nearer-white, 3%)

  .item-delete-button
    color: rgba(0,0,0,0.3)
    font-size: 20px
    line-height: 20px
    &:hover
      color: #000

  // -- sizing modifiers --

  .item-large
    min-width: 16rem
    padding: 16px
    border-bottom: 1px solid rgba(0,0,0,0.05)

  .item-small
    min-width: 12rem
    padding: 8px
</style>
