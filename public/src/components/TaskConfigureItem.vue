<template>
  <div>
    <div class="mb3" v-for="(v, index) in variables">
      <div class="mb4 mw6" v-if="v.type=='connection' && is_input_task">
        <div class="mb3" >Choose the connection for <span class="b">{{v.variable_name}}</span>:</div>
        <pipe-transfer-input-chooser
          class="bg-white bt b--light-gray"
          :project-eid="active_project_eid"
          :connection-type-filter="ctype"
          :show-connection-chooser-title="false"
          :show-service-list="false"
          @choose-input="chooseOutput"
        ></pipe-transfer-input-chooser>
        <div class="mt3">
          <btn btn-md btn-primary class="ttu b" @click="addInput">{{add_button_label}}</btn>
        </div>
      </div>
      <div class="mb4 mw6" v-if="v.type=='connection' && is_output_task">
        <div class="mb3" >Choose the connection for <span class="b">{{v.variable_name}}</span>:</div>
        <pipe-transfer-output-chooser
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
        <div>Enter the value for <span class="b">{{v.variable_name}}</span>:</div>
        <ui-textbox
          class="mw6"
          autocomplete="off"
          :label="v.variable_name"
          floating-label
          help=" "
          value=""
        ></ui-textbox>
      </div>
    </div>

    <div class="flex flex-row items-center mt2" v-if="isActivePromptTask">
      <div class="flex-fill"></div>
      <btn btn-md class="b ttu blue mr2" @click="$emit('go-prev-prompt')">Back</btn>
      <btn btn-md class="b ttu white bg-blue" @click="$emit('go-next-prompt')">Next</btn>
    </div>

    <pipe-transfer-output-chooser
      ref="output-chooser"
      class="bg-white bt b--light-gray"
      style="max-width: 1px; max-height: 1px; overflow: hidden"
      :project-eid="active_project_eid"
      :connection-type-filter="ctype"
      :show-connection-chooser-title="false"
      :show-service-list="false"
      @choose-output="chooseOutput"
    ></pipe-transfer-output-chooser>
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
      },
      'active-prompt-idx': {
        type: Number,
        default: 0
      },
      'is-active-prompt-task': {
        type: Boolean,
        default: false
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
