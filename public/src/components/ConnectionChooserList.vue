<template>
  <div class="flex flex-row justify-center items-center min-h3" v-if="is_fetching">
    <Spinner size="medium" />
    <span class="ml2 f5">Loading...</span>
  </div>
  <div v-else>
    <ConnectionChooserItem
      v-for="(input_service, ctype) in input_services"
      :key="ctype"
      :item="input_service"
      :layout="layout"
      :class="itemCls"
      :override-cls="overrideItemCls"
      :connection-eid="connection_eid"
      :connection-type="connection_type"
      :show-selection="showSelection"
      :show-selection-checkmark="showSelectionCheckmark"
      @activate="onItemActivate"
    />
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
      'connection': {
        type: Object,
        default: () => { return null }
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
      'connection-type-filter': {
        type: String,
        default: ''
      },
      'show-default-connections': {
        type: Boolean,
        default: false
      },
      'show-selection': {
        type: Boolean,
        default: false
      },
      'show-selection-checkmark': {
        type: Boolean,
        default: false
      },
      'layout': {
        type: String, // 'list' or 'grid'
        default: 'grid'
      },
      'item-cls': {
        type: String,
        default: ''
      },
      'override-item-cls': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Spinner,
      ConnectionChooserItem,
      EmptyItem
    },
    watch: {
      connection(val) {
        this.onItemActivate(val)
      },
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

        return items
      },
      input_services() {
        var items = [].concat(this.default_connections, this.getAvailableConnections())

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
        'getAvailableConnections'
      ]),
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
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
