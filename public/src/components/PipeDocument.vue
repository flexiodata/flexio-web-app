<template>
  <div>
    <!-- header -->
    <div class="flex flex-row">
      {{pipe.name}}
    </div>

    <!-- content -->
    <div>
      <pre>{{pipe.task}}</pre>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      pipe: {
        type: Object,
        default: () => {}
      }
    },
  }
</script>
