<template>
  <div class="overflow-y-auto">
    <pipe-transfer-transform-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
    >
    </pipe-transfer-transform-item>
  </div>
</template>

<script>
  import PipeTransferTransformItem from './PipeTransferTransformItem.vue'

  export default {
    props: ['tasks'],
    components: {
      PipeTransferTransformItem
    }
  }
</script>
