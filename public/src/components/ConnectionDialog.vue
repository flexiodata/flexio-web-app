<template>
  <el-dialog v-bind="$props">
    <span slot="title" class="dialog-title"></span>

    <connection-info-configure-panel
      :is-new="true"
      :connection="{}"
      :show-footer="false"
    />

    <span slot="footer" class="dialog-footer">
      <el-button type="plain" @click="close">Cancel</el-button>
      <el-button type="primary" @click="close">Confirm</el-button>
    </span>
  </el-dialog>
</template>

<script>
  import { Dialog } from 'element-ui'
  import ConnectionInfoConfigurePanel from './ConnectionInfoConfigurePanel.vue'

  export default {
    extends: Dialog,
    components: {
      ConnectionInfoConfigurePanel
    },
    methods: {
      close() {
        this.$emit('update:visible', false)
      }
    }
  }
</script>
