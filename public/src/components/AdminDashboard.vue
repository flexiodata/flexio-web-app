<template>
  <div class="overflow-y-auto">
    <admin-pipe-summary></admin-pipe-summary>
    <admin-task-summary></admin-task-summary>
  </div>
</template>

<script>
  import AdminPipeSummary from './AdminPipeSummary.vue'
  import AdminTaskSummary from './AdminTaskSummary.vue'

  export default {
    components: {
      AdminPipeSummary,
      AdminTaskSummary
    }
  }
</script>
