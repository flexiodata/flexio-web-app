<template>
  <div>
    <div
      class="tl pb3"
      v-if="showTitle"
    >
      <h3 class="fw6 f3 mt0 mb2" v-if="title.length > 0">{{title}}</h3>
      <h3 class="fw6 f3 mt0 mb2" v-else-if="ctype.length > 0">Connect to {{service_name}}</h3>
      <h3 class="fw6 f3 mt0 mb2" v-else>Choose a connection</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <BuilderComponentConnectionChooser
      :connection-identifier.sync="edit_connection.eid"
      :filter-by="filter_by"
      :show-result="is_before_active"
      @choose-connection="chooseConnection"
      v-on="$listeners"
      v-show="is_active || is_before_active"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import BuilderComponentConnectionChooser from './BuilderComponentConnectionChooser.vue'
  import MixinConnection from './mixins/connection'

  const getDefaultValues = () => {
    return {
      eid: ''
    }
  }

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: false
      },
      showTitle: {
        type: Boolean,
        default: true
      }
    },
    mixins: [MixinConnection],
    components: {
      BuilderComponentConnectionChooser
    },
    watch: {
      is_active: {
        handler: 'updateAllowNext',
        immediate: true
      },
      edit_connection: {
        handler: 'updateAllowNext',
        deep: true
      }
    },
    data() {
      return {
        edit_mode: 'add',
        edit_connection: getDefaultValues(),
        show_connection_dialog: false
      }
    },
    computed: {
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      show_description() {
        return this.is_active && this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      ceid() {
        return _.get(this.edit_connection, 'eid', '')
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      filter_by() {
        var ctype = this.ctype

        if (ctype.length > 0) {
          return function(item) {
            return _.get(item, 'connection_type', '') == ctype
          }
        } else {
          return undefined
        }
      },
      connections() {
        return this.getAvailableConnections()
      },
      connections_of_type() {
        return _.filter(this.connections, { connection_type: this.ctype })
      },
      has_connections() {
        if (this.ctype.length > 0) {
          return this.connections_of_type.length > 0
        }
        return this.connections.length > 0
      },
      has_available_connection() {
        return _.get(this.edit_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
      },
      service_name() {
        return this.$_Connection_getServiceName(this.ctype)
      },
    },
    methods: {
      ...mapGetters([
        'getAvailableConnections'
      ]),
      chooseConnection(connection) {
        var key = _.get(this.item, 'variable', 'connection_eid')
        var form_values = _.get(this.item, 'form_values', {})

        if (_.isNil(connection)) {
          form_values[key] = ''
          this.edit_connection = undefined
          this.$emit('item-change', form_values, this.index)
          this.$emit('update:connectionIdentifier', '')
        } else {
          var cid = _.get(connection, 'alias', '') || _.get(connection, 'eid', '')
          form_values[key] = cid
          this.edit_connection = _.cloneDeep(connection)
          this.$emit('item-change', form_values, this.index)
          this.$emit('update:connectionIdentifier', cid)
        }
      },
      updateAllowNext() {
        this.$emit('update:isNextAllowed', this.has_available_connection)
      }
    }
  }
</script>
