<template>
  <div>
    <process-progress-item
      v-for="(subprocess, index) in subprocesses"
      :item="subprocess"
      :index="index"
    >
    </process-progress-item>
  </div>
</template>

<script>
  import ProcessProgressItem from './ProcessProgressItem.vue'

  export default {
    props: ['process'],
    components: {
      ProcessProgressItem
    },
    computed: {
      subprocesses() {
        return _.get(this.process, 'subprocesses', [])
      }
    }
  }
</script>
