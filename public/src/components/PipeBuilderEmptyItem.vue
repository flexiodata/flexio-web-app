<template>
  <div>
    <div v-if="false">
      <p class="mt0 ttu fw6 f7 moon-gray">Choose a starting connection</p>
      <ConnectionChooserList
        class="mb2 overflow-auto"
        layout="grid"
        :show-status="false"
        @item-activate="selectConnection"
      />
      <el-button
        class="ttu b"
        @click="show_connection_dialog = true"
      >
        Set up a new connection
      </el-button>
      <p class="mt4 ttu fw6 f7 moon-gray">&mdash; or start with one of the following tasks &mdash;</p>
    </div>
    <p class="mt0 ttu fw6 f7 moon-gray">Choose a starting task (e.g., click 'Execute' to add a function)</p>
    <BuilderItemTaskChooser
      :show-title="false"
      @task-chooser-select-task="selectTask"
    />

    <!-- connect to storage dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_dialog"
    >
      <ConnectionEditPanel
        :connection="edit_connection"
        :mode="edit_mode"
        @close="show_connection_dialog = false"
        @cancel="show_connection_dialog = false"
        @submit="tryUpdateConnection"
        v-if="show_connection_dialog"
      />
    </el-dialog>
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
      },

      // IT'S GETTING REALLY ANNOYING HAVING TO ADD THIS EVERYWHERE...
      tryUpdateConnection(attrs) {
        var eid = attrs.eid
        var is_pending = attrs.eid_status === OBJECT_STATUS_PENDING

        attrs = _.pick(attrs, ['name', 'alias', 'description', 'connection_info'])
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          var connection = response.body

          if (response.ok)
          {
            // TODO: shouldn't we do this in the ConnectionEditPanel?
            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs })

            if (is_pending)
            {
              var analytics_payload = _.pick(attrs, ['eid', 'name', 'alias', 'description', 'connection_type'])
              this.$store.track('Created Connection In New Pipe', analytics_payload)
            }

            this.show_connection_dialog = false

            this.$emit('insert-step', 0, {
              op: 'connect',
              connection: _.get(connection, 'eid', ''),
              alias: 'my-alias'
            })
          }
           else
          {
            this.$store.track('Created Connection In New Pipe (Error)')
          }
        })
      }
    }
  }
</script>
