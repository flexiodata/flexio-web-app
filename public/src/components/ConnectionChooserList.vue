<template>
  <div class="flex flex-row justify-center items-center min-h3" v-if="is_fetching">
    <spinner size="medium"></spinner>
    <span class="ml2 f5">Loading...</span>
  </div>
  <div v-else>
    <connection-chooser-item
      v-for="(input_service, index) in input_services"
      :item="input_service"
      :index="index"
      :layout="layout"
      :connection-eid="connection_eid"
      :connection-type="connection_type"
      :show-selection="showSelection"
      @activate="onItemActivate"
    ></connection-chooser-item>
    <article
      class="css-connection-ghost dib relative mw5 h4 w4 black-40 br2 pa1 mv2 mh1 v-top darken-10"
      @click="onAddClick"
      v-if="showAdd"
    >
      <div class="css-connection-inner-border absolute absolute--fill ba bw1 br2 b--black-10 b--dashed"></div>
      <div class="tc css-valign cursor-default">
        <i slot="icon" class="material-icons md-48">add_circle</i>
        <div class="f6 fw6 mt2 ph2">{{add_button_label}}</div>
      </div>
    </article>
  </div>
</template>

<script>
  import { CONNECTION_TYPE_BLANK_PIPE } from '../constants/connection-type'
  import * as connections from '../constants/connection-info'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'
  import EmptyItem from './EmptyItem.vue'

  export default {
    props: {
      'connection-type': {
        type: String,
        required: false
      },
      'list-type': {
        type: String,
        default: 'input'
      },
      'show-add': {
        type: Boolean,
        default: false
      },
      'add-button-label': {
        type: String,
        default: ''
      },
      'show-blank-pipe': {
        type: Boolean,
        default: false
      },
      'show-default-connections': {
        type: Boolean,
        default: true
      },
      'connection-type-filter': {
        type: String,
        default: ''
      },
      'show-selection': {
        type: Boolean,
        default: false
      },
      'layout': {
        type: String, // 'list' or 'grid'
        default: 'grid'
      }
    },
    components: {
      Spinner,
      ConnectionChooserItem,
      EmptyItem
    },
    watch: {
      connectionType: function(val, old_val) {
        this.connection_type = val
      }
    },
    data() {
      return {
        connection_eid: '',
        connection_type: ''
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      default_connections() {
        if (!this.showDefaultConnections)
          return []

        var items = _
          .chain(connections)
          .filter({ is_service: false })
          .filter({ [this.listType==='output'?'is_output':'is_input']: true })
          .map((c) => {
            return _.assign({}, c, {
              name: c.service_name
            })
          })
          .value()

        if (this.showBlankPipe !== true)
          items = _.reject(items, (c) => { return c.connection_type == CONNECTION_TYPE_BLANK_PIPE })

        return items
      },
      input_services() {
        var items = [].concat(this.default_connections, this.getOurConnections())

        if (this.connectionTypeFilter.length == 0)
          return items

        return _.filter(items, { connection_type: this.connectionTypeFilter })
      },
      add_button_label() {
        return this.addButtonLabel.length > 0 ? this.addButtonLabel : 'New Connection'
      }
    },
    created() {
      this.tryFetchConnections()
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      getOurConnections() {

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllConnections())
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
      },
      onAddClick() {
        this.$emit('add')
      },
      onItemActivate(item) {
        this.connection_eid = _.get(item, 'eid', '')
        this.connection_type = _.get(item, 'connection_type', '')
        this.$emit('item-activate', item)
      }
    }
  }
</script>

<style lang="less">
  .css-valign {
    position: relative;
    top: 50%;
    transform: translateY(-50%);
  }

  .css-connection-ghost:not(.css-disabled) {
    &:hover,
    &:focus,
    &:active {
      color: #000;

      .css-connection-inner-border {
        border-style: solid;
      }
    }
  }
</style>
