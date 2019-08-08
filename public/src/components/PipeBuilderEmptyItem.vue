<template>
  <div>
    <p class="mt0 ttu fw6 f7 moon-gray">Choose a starting step</p>
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
