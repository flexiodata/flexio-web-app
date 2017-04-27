<template>
  <div>
    <div>{{ctype}}</div>
  </div>
</template>

<script>
  import taskItemHelper from './mixins/task-item-helper'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'variables': {
        type: Array,
        required: true
      }
    },
    mixins: [taskItemHelper]
  }
</script>
