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
      :layout="itemLayout"
      :connection-type="connectionType"
      @activate="onItemActivate"
    >
    </connection-chooser-item>
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
  import { mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConnectionChooserItem from './ConnectionChooserItem.vue'
  import EmptyItem from './EmptyItem.vue'

  export default {
    props: {
      'project-eid': {
        type: String,
        required: true
      },
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
      'connection-type-filter': {
        type: String
      },
      'item-layout': {
        type: String // 'list' or 'grid'
      }
    },
    components: {
      Spinner,
      ConnectionChooserItem,
      EmptyItem
    },
    computed: {
      default_connections() {
        var me = this

        var items = _
          .chain(connections)
          .filter({ is_service: false })
          .filter({ [me.listType==='output'?'is_output':'is_input']: true })
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

        if (_.isNil(this.connectionTypeFilter))
          return items

        return _.filter(items, { connection_type: this.connectionTypeFilter })
      },
      is_fetched() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'connections_fetched', true)
      },
      is_fetching() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'connections_fetching', true)
      },
      add_button_label() {
        return this.addButtonLabel.length > 0 ? this.addButtonLabel : 'New Connection'
      }
    },
    created() {
      if (!this.projectEid)
        return

      this.tryFetchConnections()
    },
    methods: {
      ...mapGetters([
        'getAllConnections',
        'getAllProjects'
      ]),
      tryFetchConnections() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchConnections', this.projectEid)
      },
      getOurConnections() {
        var project_eid = this.projectEid

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllConnections())
          .filter(function(p) { return _.get(p, 'project.eid') == project_eid })
          .sortBy([ function(p) { return new Date(p.created) } ])
          .reverse()
          .value()
      },
      onAddClick() {
        this.$emit('add')
      },
      onItemActivate(item) {
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
