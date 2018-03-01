<template>
  <el-dialog v-bind="$props">
    <pipe-edit-panel
      :title="$props.title"
      :pipe="$attrs.pipe"
      :mode="$attrs.mode"
      @close="close"
      @cancel="close"
      @submit="submit"
    />
  </el-dialog>
</template>

<script>
  import { Dialog } from 'element-ui'
  import PipeEditPanel from './PipeEditPanel.vue'

  export default {
    extends: Dialog,
    components: {
      PipeEditPanel
    },
    methods: {
      close() {
        this.$emit('update:visible', false)
      },
      submit(pipe) {
        this.$emit('submit', pipe, this)
      }
    }
  }
</script>
