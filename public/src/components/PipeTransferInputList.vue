<template>
  <div class="overflow-y-auto">
    <pipe-transfer-input-item
      v-for="(task, index) in tasks"
      :item="task"
      :index="index"
      @delete="deleteInput"
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
    },
    methods: {
      deleteInput(input) {
        this.$emit('input-delete', input)
      }
    }
  }
</script>
