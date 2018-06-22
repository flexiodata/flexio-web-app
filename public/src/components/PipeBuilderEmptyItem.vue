<template>
  <div>
    <p class="ttu fw6 f7 moon-gray">Choose a starting connection</p>
    <ConnectionChooserList
      class="mb2 overflow-auto"
      layout="grid"
      @item-activate="selectConnection"
    />
    <el-button
      class="ttu b"
    >
      Set up a new connection
    </el-button>
    <p class="mt4 ttu fw6 f7 moon-gray">&mdash; or start with one of the following tasks &mdash;</p>
    <BuilderItemTaskChooser
      title="Choose a task"
      :ops="ops"
      @task-chooser-select-task="selectTask"
    />
  </div>
</template>

<script>
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import BuilderItemTaskChooser from './BuilderItemTaskChooser.vue'

  export default {
    components: {
      ConnectionChooserList,
      BuilderItemTaskChooser
    },
    data() {
      return {
        ops: ['request', 'execute', 'echo', 'email']
      }
    },
    methods: {
      selectConnection(connection) {
        this.$emit('insert-step', 0, {
          op: 'connect',
          connection: _.get(connection, 'eid', ''),
          alias: 'my-alias'
        })
      },
      selectTask(task) {
        this.$emit('insert-step', 0, task)
      }
    }
  }
</script>
