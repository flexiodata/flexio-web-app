<template>
  <div>
    <pipe-transfer-input-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
    >
    </pipe-transfer-input-item>
  </div>
</template>

<script>
  import PipeTransferInputItem from './PipeTransferInputItem.vue'

  export default {
    props: ['tasks'],
    components: {
      PipeTransferInputItem
    }
  }
</script>
