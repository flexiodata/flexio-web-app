<template>
  <div>
    <div v-if="is_input_task">
      Input task...
    </div>
    <div v-else-if="is_output_task">
      <connection-chooser-list
        list-type="output"
        :project-eid="active_project_eid"
        :type-filter="ctype"
        :show-add="true"
        :add-button-label="add_button_label"
        @item-activate="chooseConnection"
        @add="addConnection"
      ></connection-chooser-list>
    </div>
    <div v-else>
      Other task...
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import * as connections from '../constants/connection-info'
  import ConnectionChooserList from './ConnectionChooserList.vue'
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
      ConnectionChooserList
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
        return 'Connect to '+name
      }
    },
    methods: {
      chooseConnection() {
        alert('Choose connection!')
      },
      addConnection() {
        alert('Add connection!')
      }
    }
  }
</script>
