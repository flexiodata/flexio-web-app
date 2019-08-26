<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="flex flex-row items-center">
      <div class="flex-fill">
        <div class="flex flex-row items-center">
          <div class="item-on-indicator" :class="is_deployed ? 'on' : 'off'"></div>
          <div class="flex-fill truncate item-title">{{pname}}</div>
        </div>
      </div>
      <div
        class="flex-none item-dropdown"
        @click.stop
        v-require-rights:pipe.delete.hidden
      >
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link item-dropdown-trigger">
            <i class="material-icons item-dropdown-trigger-icon">expand_more</i>
          </span>
          <el-dropdown-menu class="item-dropdown-menu" slot="dropdown">
            <el-dropdown-item class="flex flex-row items-center item-dropdown-menu-item" command="delete"><i class="material-icons item-dropdown-menu-item-icon">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  const DEPLOY_MODE_RUN = 'R'

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
        default: 'item-selected'
      },
      showDropdown: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      eid() {
        return _.get(this.item, 'eid', '')
      },
      pname() {
        return _.get(this.item, 'name', '')
      },
      is_selected() {
        return _.get(this.selectedItem, 'eid') === this.eid
      },
      is_deployed() {
        return _.get(this.item, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
      },
      cls() {
        var cls = this.itemCls
        var size_cls = this.itemCls + '-' + this.itemSize
        var sel_cls = this.is_selected ? this.selectedCls : ''

        return `${cls} ${size_cls} ${sel_cls}`
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

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .item
    cursor: pointer
    &:hover
      background-color: $nearer-white

  .item-on-indicator
    height: 8px
    width: 8px
    background-color: #dcdfe6
    border-radius: 100%
    margin-right: 8px
    &.on
      background-color: #13ce66

  .item-title
    font-size: 16px
    font-weight: 600
    line-height: 1.25

  .item-dropdown-trigger
    margin-left: 8px
    cursor: pointer
    color: rgba(0,0,0,0.3)
    &:hover
      color: #000

  .item-dropdown-trigger-icon
    vertical-align: middle
    font-size: 24px

  .item-dropdown-menu
    min-width: 10rem

  .item-dropdown-menu-item-icon
    margin-right: 12px

  // -- sizing modifiers --

  .item-large
    min-width: 16rem
    padding: 16px
    border-bottom: 1px solid rgba(0,0,0,0.05)

  .item-small
    min-width: 12rem
    padding: 8px

    .item-on-indicator
      height: 6px
      width: 6px
      margin-right: 6px

    .item-title
      font-size: 14px
      font-weight: 400

    .item-dropdown-trigger-icon
      font-size: 18px
      line-height: 18px

  .item-mini
    min-width: 10rem
    padding: 2px 8px 2px 4px

    .item-on-indicator
      height: 4px
      width: 4px
      margin-right: 4px

    .item-title
      font-size: 13px
      font-weight: 400

    .item-dropdown-trigger-icon
      font-size: 18px
      line-height: 18px

  .item-selected
    position: relative
    background-color: $nearer-white
</style>
