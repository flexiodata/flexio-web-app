<template>
  <div>
    <div class="pa2 bg-black-05" v-if="showConnectionChooserList && showConnectionChooserTitle">
      <div class="f6 fw6 ttu silver">My Connections</div>
    </div>
    <connection-chooser-list
      list-type="output"
      :item-layout="itemLayout"
      :connection-type-filter="connectionTypeFilter"
      :show-selection="showConnectionChooserSelection"
      :show-default-connections="showDefaultConnectionsInChooserList"
      @item-activate="chooseConnection"
      v-if="showConnectionChooserList"
    ></connection-chooser-list>
    <div class="pa2 bg-black-05" v-if="showServiceList && showServiceTitle">
      <div class="f6 fw6 ttu silver">Available Connections</div>
    </div>
    <service-list
      list-type="output"
      :item-layout="itemLayout"
      @item-activate="createPendingConnection"
      v-if="showServiceList"
    ></service-list>

    <!-- connection props modal -->
    <connection-props-modal
      ref="modal-connection-props"
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
    props: {
      'show-connection-chooser-list': {
        type: Boolean,
        default: true
      },
      'show-connection-chooser-title': {
        type: Boolean,
        default: true
      },
      'show-connection-chooser-selection': {
        type: Boolean,
        default: false
      },
      'show-default-connections-in-chooser-list': {
        type: Boolean,
        default: true
      },
      'show-service-list': {
        type: Boolean,
        default: true
      },
      'show-service-title': {
        type: Boolean,
        default: true
      },
      'connection-type-filter': {
        type: String
      },
      'item-layout': {
        type: String,
        default: 'list'
      }
    },
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
      openConnectionModal(attrs) {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(attrs) })
      },
      createPendingConnection(item) {
        var attrs = _.assign({}, this.connection, {
          name: item.service_name,
          connection_type: item.connection_type,
          eid_status: OBJECT_STATUS_PENDING
        })

        this.$store.dispatch('createConnection', { attrs }).then(response => {
          if (response.ok)
          {
            this.openConnectionModal(response.body)
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
        _.assign(attrs, { eid_status: OBJECT_STATUS_AVAILABLE })

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
