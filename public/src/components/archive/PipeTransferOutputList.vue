<template>
  <div class="overflow-y-auto">
    <pipe-transfer-output-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      @delete="deleteOutput"
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
    },
    methods: {
      deleteOutput(input) {
        this.$emit('output-delete', input)
      }
    }
  }
</script>
