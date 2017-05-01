<template>
  <div>
    <div class="mb4 mw6" v-if="item.type=='connection' && is_input_task">
      <div class="mb3" >Choose the connection for <span class="b">{{item.variable_name}}</span>:</div>
      <pipe-transfer-input-chooser
        ref="input-chooser"
        class="bg-white bt b--light-gray"
        :project-eid="active_project_eid"
        :connection-type-filter="ctype"
        :show-connection-chooser-title="false"
        :show-service-list="false"
        @choose-input="chooseInput"
      ></pipe-transfer-input-chooser>
      <div class="mt3">
        <btn btn-md btn-primary class="ttu b" @click="addInput">{{add_button_label}}</btn>
      </div>
    </div>
    <div class="mb4 mw6" v-else-if="item.type=='connection' && is_output_task">
      <div class="mb3" >Choose the connection for <span class="b">{{item.variable_name}}</span>:</div>
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
        <btn btn-md btn-primary class="ttu b" @click="addOutput">{{add_button_label}}</btn>
      </div>
    </div>
    <div v-else>
      <div class="mb3">Enter the value for <span class="b">{{item.variable_name}}</span>:</div>
      <ui-textbox
        class="mw6"
        autocomplete="off"
        :label="item.variable_name"
        floating-label
        help=" "
        v-model="val"
      ></ui-textbox>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import * as connections from '../constants/connection-info'
  import Btn from './Btn.vue'
  import PipeTransferInputChooser from './PipeTransferInputChooser.vue'
  import PipeTransferOutputChooser from './PipeTransferOutputChooser.vue'
  import TaskItemHelper from './mixins/task-item-helper'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'index': {
        type: Number,
        required: true
      },
      'task-item': {
        type: Object,
        required: true
      }
    },
    mixins: [TaskItemHelper],
    components: {
      Btn,
      PipeTransferInputChooser,
      PipeTransferOutputChooser
    },
    watch: {
      val: function(val, old_val) {
        console.log(val)
      }
    },
    data() {
      return {
        val: this.item.default_val
      }
    },
    computed: {
      ...mapState([
        'active_project_eid'
      ]),
      task() {
        return this.taskItem
      },
      cinfo() {
        return _.find(connections, { connection_type: this.ctype })
      },
      add_button_label() {
        var name = _.result(this.cinfo, 'service_name', '')
        return 'Add New '+name+' Connection'
      }
    },
    methods: {
      chooseInput(item) {
        this.$emit('choose-input', item)
      },
      chooseOutput(item) {
        this.$emit('choose-output', item)
      },
      addInput() {
        this.$refs['input-chooser'].createPendingConnection(this.cinfo)
      },
      addOutput() {
        this.$refs['output-chooser'].createPendingConnection(this.cinfo)
      }
    }
  }
</script>
