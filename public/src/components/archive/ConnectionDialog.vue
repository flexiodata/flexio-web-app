<template>
  <el-dialog v-bind="$props">
    <connection-edit-panel
      title="New Connection"
      :show-steps="true"
      @close="close"
      @cancel="close"
      @submit="submit"
    />
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
