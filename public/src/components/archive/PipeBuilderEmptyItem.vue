<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mt0 mb2">Choose a starting step</h3>
    </div>
    <BuilderItemTaskChooser
      :show-title="false"
      @task-chooser-select-task="selectTask"
    />
  </div>
</template>

<script>
  import BuilderItemTaskChooser from '@/components/BuilderItemTaskChooser'

  export default {
    components: {
      BuilderItemTaskChooser
    },
    methods: {
      selectTask(task) {
        this.$emit('insert-step', 0, task)
      }
    }
  }
</script>
