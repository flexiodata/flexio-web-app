<template>
  <div>
    <service-list
      list-type="input"
      :item-layout="itemLayout"
      @item-activate="chooseService"
    ></service-list>

    <!-- connection chooser modal -->
    <connection-chooser-modal
      ref="modal-connection-chooser"
      @choose-existing="chooseConnection"
      @choose-new="createPendingConnection"
      @hide="show_connection_chooser_modal = false"
      v-if="show_connection_chooser_modal"
    ></connection-chooser-modal>

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
  import { mapState, mapGetters } from 'vuex'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ServiceList from './ServiceList.vue'
  import ConnectionChooserModal from './ConnectionChooserModal.vue'
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
      ConnectionChooserModal,
      ConnectionPropsModal
    },
    data() {
      return {
        show_connection_chooser_modal: false,
        show_connection_props_modal: false
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      getConnectionsByType(connection_type) {
        return _.filter(this.getAllConnections(), { connection_type })
      },
      chooseConnection(item) {
        this.$emit('choose-input', item)
      },
      openConnectionChooserModal(attrs) {
        var ctype = _.get(attrs, 'connection_type', '')
        this.show_connection_chooser_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-chooser'].open(ctype) })
      },
      openConnectionModal(attrs) {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(attrs) })
      },
      chooseService(item) {
        if (item.is_service)
        {
          var ctype = _.get(item, 'connection_type', '')
          if (_.size(this.getConnectionsByType(ctype)) > 0)
            this.openConnectionChooserModal(item)
             else
            this.createPendingConnection(item)
        }
         else
        {
          this.chooseConnection(item)
        }
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
