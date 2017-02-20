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
      <span v-else>{{size}}</span>
    </td>
  </tr>
</template>

<script>
  import moment from 'moment'
  import filesize from 'filesize'

  export default {
    props: ['item'],
    computed: {
      cls() {
        return this.item.is_selected ? 'bg-black-10' : 'bg-white'
      },
      modified() {
        var m = this.item.modified
        return m ? moment(m).format('l LT') : ''
      },
      size(s) {
        // show 1 KB as minimium size
        var s = this.item.size
        return s && s > 0 ? filesize(Math.max(s, 1024)) : '0 KB'
      }
    },
    methods: {
      onClick(evt) {
        this.$emit('click', this.item, evt)
      },
      onCtrlClick(evt) {
        this.$emit('ctrl-click', this.item, evt)
      },
      onShiftClick(evt) {
        this.$emit('shift-click', this.item, evt)
      },
      onDblClick() {
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
