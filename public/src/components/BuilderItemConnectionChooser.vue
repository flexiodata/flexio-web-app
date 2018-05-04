<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-if="title.length > 0">{{title}}</h3>
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-else-if="ctype.length > 0">Connect to {{service_name}}</h3>
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-else>Choose a connection</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="is_active && description.length > 0"
    >
    </div>
    <div v-show="is_active">
      <p class="ttu fw6 f7 moon-gray" v-if="has_connections">Use an existing connection</p>
      <ConnectionChooserList
        class="mb3 bt bb b--light-gray overflow-auto"
        style="max-height: 277px"
        :connection="store_connection"
        :connection-type-filter="ctype"
        :show-selection-checkmark="true"
        @item-activate="chooseConnection"
        @item-fix="fixConnection"
        v-if="has_connections"
      />
      <p class="ttu fw6 f7 moon-gray" v-if="has_connections">&mdash; or &mdash;</p>
      <div class="mt3">
        <el-button
          class="ttu b"
          type="plain"
          @click="createPendingConnection"
        >
          Set up a new connection
        </el-button>
      </div>
    </div>
    <div v-if="is_before_active">
      <ConnectionChooserItem
        class="mb3 bt bb b--black-10"
        :item="store_connection"
        :connection-eid="ceid"
        :show-selection-checkmark="true"
      />
    </div>

    <!-- connect to storage dialog -->
    <el-dialog
      custom-class="no-header no-footer"
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
  import marked from 'marked'
  import { mapState, mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import { OBJECT_STATUS_AVAILABLE, OBJECT_STATUS_PENDING } from '../constants/object-status'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'
  import MixinConnectionInfo from './mixins/connection-info'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    mixins: [MixinConnectionInfo],
    components: {
      ConnectionEditPanel,
      ConnectionChooserList,
      ConnectionChooserItem
    },
    data() {
      return {
        edit_mode: 'add',
        edit_connection: undefined,
        show_connection_dialog: false
      }
    },
    computed: {
      ...mapState({
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_before_active() {
        return this.index < this.active_prompt_idx
      },
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      ceid() {
        return _.get(this.item, 'connection_eid', null)
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      connections() {
        return this.getAvailableConnections()
      },
      connections_of_type() {
        return _.filter(this.connections, { connection_type: this.ctype })
      },
      has_connections() {
        if (this.ctype.length > 0)
          return this.connections_of_type.length > 0

        return this.connections.length > 0
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      service_name() {
        return this.getConnectionInfo(this.ctype, 'service_name')
      },
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      chooseConnection(connection) {
        this.$store.commit('builder/UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
      },
      fixConnection(connection) {
        this.chooseConnection(connection)
        this.edit_mode = 'edit'
        this.edit_connection = connection
        this.show_connection_dialog = true
      },
      createPendingConnection() {
        if (this.ctype.length > 0) {
          var attrs = {
            eid_status: OBJECT_STATUS_PENDING,
            name: this.service_name,
            connection_type: this.ctype
          }

          this.$store.dispatch('createConnection', { attrs }).then(response => {
            var connection = response.body
            this.edit_mode = 'add'
            this.edit_connection = connection
            this.show_connection_dialog = true
          })
        } else {
          this.show_connection_dialog = true
        }
      },
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
              this.$store.track('Created Connection In Template Builder', analytics_payload)
            }

            this.chooseConnection(connection)
            this.show_connection_dialog = false
          }
           else
          {
            this.$store.track('Created Connection In Template Builder (Error)')
          }
        })
      },
    }
  }
</script>
