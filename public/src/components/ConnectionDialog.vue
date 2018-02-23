<template>
  <el-dialog v-bind="$props">
    <span slot="title" class="dialog-title"></span>

    <connection-edit-panel
      title="New Connection"
      :show-steps="true"
      @close="close"
      @submit="submit"
    />

    <span slot="footer">
      <el-button class="ttu" type="plain" @click="close">Cancel</el-button>
      <el-button class="ttu" type="primary" @click="close">Confirm</el-button>
    </span>
  </el-dialog>
</template>

<script>
  import { Dialog } from 'element-ui'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'

  export default {
    extends: Dialog,
    components: {
      ConnectionEditPanel
    },
    methods: {
      close() {
        this.$emit('update:visible', false)
      },
      submit(connection) {
        this.$emit('submit', connection, this)
      }
    }
  }
</script>
