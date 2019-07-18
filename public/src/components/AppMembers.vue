<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column" v-else-if="is_fetched">
    Members
  </div>
</template>

<script>
  export default {
    metaInfo() {
      return {
        title: 'Members'
      }
    },
    data() {
      return {
        is_fetching: false,
        is_fetched: true
      }
    }
  }
</script>
