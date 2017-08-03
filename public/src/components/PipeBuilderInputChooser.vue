<template>
  <div>
    <service-list
      list-type="input"
      :item-layout="itemLayout"
      @item-activate="chooseService"
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
  import ServiceList from './ServiceList.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'

  export default {
    props: {
      'item-layout': {
        type: String,
        default: 'list'
      }
    },
    components: {
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
        this.$emit('choose-input', item)
      },
      openConnectionModal(attrs) {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(attrs) })
      },
      chooseService(item) {
        if (item.is_service)
          this.createPendingConnection(item)
           else
          this.chooseConnection(item)
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
