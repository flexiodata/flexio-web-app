<template>
  <tr
    class="darken-05 no-select cursor-default"
    :class="cls"
    @click="onClick"
    @click.ctrl="onCtrlClick"
    @click.shift="onShiftClick"
    @dblclick.stop="onDblClick"
  >
    <td class="css-item">
      <div class="flex flex-row items-center">
        <img v-if="item.is_dir" src="../assets/file-icon/folder-open-16.png" class="dib" alt="Folder">
        <img v-else src="../assets/file-icon/file-16.png" class="dib" alt="File">
        <span class="dib ml1 f7">{{item.name}}</span>
      </div>
    </td>
    <td class="css-item f7 tr">{{modified}}</td>
    <td class="css-item f7 tr">
      <span v-if="item.is_dir">&nbsp;</span>
      <span v-else-if="!has_filesize">&nbsp;</span>
      <span v-else>{{size}}</span>
    </td>
  </tr>
</template>

<script>
  import * as ctypes from '../constants/connection-type'
  import moment from 'moment'
  import filesize from 'filesize'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number
      },
      connection: {
        type: Object
      }
    },
    data() {
      return {
        double_click: false
      }
    },
    computed: {
      cls() {
        return this.item.is_selected ? 'bg-black-10' : 'bg-white'
      },
      modified() {
        var m = this.item.modified
        return m ? moment(m).format('l LT') : ''
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      has_filesize() {
        switch (this.ctype)
        {
          case ctypes.CONNECTION_TYPE_AMAZONS3:
          case ctypes.CONNECTION_TYPE_DROPBOX:
          case ctypes.CONNECTION_TYPE_GOOGLEDRIVE:
          case ctypes.CONNECTION_TYPE_SFTP:
            return true
        }

        return false
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

<style scoped>
  .css-item {
    padding: 0.125rem 0.25rem;
  }
</style>
