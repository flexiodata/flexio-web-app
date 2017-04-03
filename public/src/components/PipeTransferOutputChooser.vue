<template>
  <div>
    <div class="pa2 bg-black-05">
      <div class="f6 fw6 ttu silver">My Connections</div>
    </div>
    <div class="relative">
      <connection-chooser-list
        list-type="output"
        item-layout="list"
        :project-eid="projectEid"
        :show-add="false"
        :show-blank-pipe="false"
        @item-activate="chooseConnection"
      ></connection-chooser-list>
    </div>
    <div class="pa2 bg-black-05">
      <div class="f6 fw6 ttu silver">Available Connections</div>
    </div>
    <service-list
      item-layout="list"
      @item-activate="createPendingConnection"
    ></service-list>

    <!-- connection props modal -->
    <connection-props-modal
      ref="modal-connection-props"
      :project-eid="projectEid"
      :show-steps="false"
      @submit="tryUpdateConnection"
      @hide="show_connection_props_modal = false"
      v-if="show_connection_props_modal"
    ></connection-props-modal>
  </div>
</template>

<script>
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ServiceList from './ServiceList.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'

  export default {
    props: ['project-eid'],
    components: {
      ConnectionChooserList,
      ServiceList,
      ConnectionPropsModal
    },
    data() {
      return {
        show_connection_props_modal: false
      }
    },
    methods: {
      chooseConnection(item) {
        this.$emit('choose-output', item)
      },
      createPendingConnection(item) {
        this.show_connection_props_modal = true

        var attrs = _.extend({}, this.connection, {
          name: item.service_name,
          connection_type: item.connection_type,
          parent_eid: this.projectEid,
          eid_status: OBJECT_STATUS_PENDING
        })

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            this.$refs['modal-connection-props'].open(response.body)
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryUpdateConnection(attrs, modal) {
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'ename', 'description', 'token', 'host', 'port', 'username', 'password', 'database'])
        _.extend(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

        // update the connection and make it available
        this.$store.dispatch('updateConnection', { eid, attrs }).then(response => {
          if (response.ok)
          {
            this.chooseConnection(response.body)
            modal.close()

            // try to connect to the connection
            this.$store.dispatch('testConnection', { eid, attrs }).then(response2 => {
              if (response.ok)
              {
                // TODO: should we actually choose the connection here instead?
              }
               else
              {
                // TODO: add error handling
              }
            })
          }
           else
          {
            // TODO: add error handling
          }
        })
      }
    }
  }
</script>
