<template>
  <tr
    class="no-select cursor-default darken-05"
    :class="cls"
    @click="onClick"
    @click.ctrl="onCtrlClick"
    @click.shift="onShiftClick"
    @dblclick.stop="onDblClick"
  >
    <td class="css-item">
      <div class="flex flex-row items-center">
        <img v-if="is_dir" src="../assets/file-icon/folder-open-16.png" class="dib" alt="Folder">
        <img v-else src="../assets/file-icon/file-16.png" class="dib" alt="File">
        <span class="dib ml1 f7 truncate">{{item.name}}</span>
      </div>
    </td>
    <td class="css-item f7 tr nowrap">{{modified}}</td>
    <td class="css-item f7 tr nowrap">
      <span v-if="is_dir">&nbsp;</span>
      <span v-else-if="!has_filesize">&nbsp;</span>
      <span v-else>{{size}}</span>
    </td>
  </tr>
</template>

<script>
  import moment from 'moment'
  import filesize from 'filesize'

  const VFS_TYPE_DIR = 'DIR'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number
      }
    },
    data() {
      return {
        double_click: false
      }
    },
    computed: {
      cls() {
        return this.item.is_selected ? 'bg-light-gray' : 'bg-white'
      },
      modified() {
        var m = this.item.modified
        return m ? moment(m).format('l LT') : ''
      },
      has_filesize() {
        return this.size === null ? false : true
      },
      is_dir() {
        return _.get(this.item, 'type') == VFS_TYPE_DIR ? true : false
      },
      size() {
        // show empty string for null sizes
        var s = _.get(this.item, 'size', null)
        if (_.isNil(s))
          return ''

        // show 1 KB as minimium size
        return s && s > 0 ? filesize(Math.max(s, 1024)) : '0 KB'
      }
    },
    methods: {
      onClick(evt) {
        if (!this.double_click)
          this.$emit('click', this.item, evt)
        this.double_click = false
      },
      onCtrlClick(evt) {
        this.$emit('ctrl-click', this.item, evt)
      },
      onShiftClick(evt) {
        this.$emit('shift-click', this.item, evt)
      },
      onDblClick() {
        this.double_click = true
        this.$emit('dblclick', this.item)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .css-item
    padding: 0.125rem 0.25rem
</style>
