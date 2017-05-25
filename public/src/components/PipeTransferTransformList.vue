<template>
  <div :class="iconsOnly ? 'flex flex-row items-center' : ''">
    <pipe-transfer-transform-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      :icon-only="iconsOnly"
    >
    </pipe-transfer-transform-item>
  </div>
</template>

<script>
  import PipeTransferTransformItem from './PipeTransferTransformItem.vue'

  export default {
    props: {
      'tasks': {
        type: Array,
        required: true
      },
      'icons-only': {
        type: Boolean,
        default: false
      }
    },
    components: {
      PipeTransferTransformItem
    }
  }
</script>
