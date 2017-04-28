<template>
  <div v-if="is_input_task">
    Input task...
  </div>
  <div v-else-if="is_output_task">
    <div class="mw6">
      <pipe-transfer-output-chooser
        ref="output-chooser"
        class="bg-white bt b--light-gray"
        :project-eid="active_project_eid"
        :connection-type-filter="ctype"
        :show-connection-chooser-title="false"
        :show-service-list="false"
        @choose-output="chooseOutput"
      ></pipe-transfer-output-chooser>
      <div class="mt3">
        <btn
          btn-md
          btn-primary
          class="ttu b"
          @click="addOutput()"
        >{{add_button_label}}</btn>
      </div>
    </div>
  </div>
  <div v-else>
    Other task...
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import PipeTransferOutputChooser from './PipeTransferOutputChooser.vue'
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
    mixins: [taskItemHelper],
    components: {
      Btn,
      PipeTransferOutputChooser
    },
    computed: {
      ...mapState([
        'active_project_eid'
      ]),
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      add_button_label() {
        var name = _.result(this.cinfo, 'service_name', '')
        return 'Add New '+name+' Connection'
      }
    },
    methods: {
      chooseOutput(item) {
        this.$emit('choose-output', item)
      },
      addOutput() {
        this.$refs['output-chooser'].createPendingConnection(this.cinfo)
      }
    }
  }
</script>
