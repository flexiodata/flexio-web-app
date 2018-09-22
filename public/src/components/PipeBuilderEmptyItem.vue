<template>
  <div>
    <p class="mt0 ttu fw6 f7 moon-gray">Choose a starting task (e.g., click 'Execute' to add a function)</p>
    <BuilderItemTaskChooser
      :show-title="false"
      @task-chooser-select-task="selectTask"
    />
  </div>
</template>

<script>
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import BuilderItemTaskChooser from './BuilderItemTaskChooser.vue'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'

  export default {
    components: {
      ConnectionChooserList,
      BuilderItemTaskChooser,
      ConnectionEditPanel
    },
    data() {
      return {
        edit_mode: 'add',
        edit_connection: undefined,
        show_connection_dialog: false,
        ops: ['request', 'execute', 'echo']
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
