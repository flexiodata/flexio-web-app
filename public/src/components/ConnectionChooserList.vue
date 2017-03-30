<template>
  <div v-if="is_fetching">
    <spinner size="medium"></spinner>
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
        <div class="f6 fw6 mt2 ph2">New Connection</div>
      </div>
    </article>
    </div>
</template>

<script>
  import { CONNECTION_TYPE_BLANK_PIPE } from '../constants/connection-type'
  import { mapGetters } from 'vuex'
  import * as connections from '../constants/connection-info'
  import Spinner from './Spinner.vue'
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
      'show-add': {
        default: true,
        type: Boolean,
        required: false
      },
      'show-blank-pipe': {
        default: true,
        type: Boolean,
        required: false
      },
      'list-type': {
        default: 'input',
        type: String,
        required: false
      },
      'item-layout': {
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
        return [].concat(this.default_connections, this.getOurConnections())
      },
      is_fetched() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'connections_fetched', true)
      },
      is_fetching() {
        return _.get(_.find(this.getAllProjects(), { eid: this.projectEid }), 'connections_fetching', true)
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
