<template>
  <div class="overflow-y-auto">
    <admin-summary-item
      v-for="(item, index) in items"
      :item="item"
      :index="index"
    ></admin-summary-item>
  </div>
</template>

<script>
  import AdminSummaryItem from './AdminSummaryItem.vue'

  export default {
    components: {
      AdminSummaryItem
    },
    data() {
      return {
        items: [{
          type: 'processes',
          title: 'Pipes run over the last month'
        }/*,{
          type: 'tasks',
          title: 'Task usage in pipes'
        },{
          type: 'users',
          title: 'Users'
        }*/]
      }
    }
  }
</script>
