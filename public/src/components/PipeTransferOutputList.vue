<template>
  <div class="overflow-y-auto">
    <pipe-transfer-output-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
    >
    </pipe-transfer-output-item>
  </div>
</template>

<script>
  import PipeTransferOutputItem from './PipeTransferOutputItem.vue'

  export default {
    props: ['tasks'],
    components: {
      PipeTransferOutputItem
    }
  }
</script>
