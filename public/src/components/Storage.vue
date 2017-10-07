<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading storage items..."></spinner>
    </div>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="pa3 ph4-l bb b--black-05">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2">Storage</div>
        </div>
        <div class="flex-none flex flex-row items-center">
          <btn btn-md btn-primary class="btn-add ttu b ba">New</btn>
        </div>
      </div>
    </div>

    <div class="flex flex-row h-100">
      <connection-chooser-list
        class="br b--black-05 overflow-y-auto"
        layout="list"
        filter-items="storage"
        item-cls="bg-white pa3 pr5-l darken-05"
        :show-default-connections="false"
        :override-item-cls="true"
        @item-activate="onConnectionActivate"
      />
      <div class="flex-fill">
        <file-chooser
          class="pa1"
          :connection="connection"
          v-if="has_connection"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import Btn from './Btn.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import FileChooser from './FileChooser.vue'
  import ConnectionConfigurePanel from './ConnectionConfigurePanel.vue'

  export default {
    components: {
      Spinner,
      Btn,
      ConnectionChooserList,
      FileChooser,
      ConnectionConfigurePanel
    },
    data() {
      return {
        connection: {},
        connection: {}
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      cname() {
        return _.get(this.connection, 'name', '')
      },
      has_connection() {
        return this.ctype.length > 0
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
          .sortBy([ function(p) { return p.name } ])
          .reverse()
          .value()
      },
      onConnectionActivate(item) {
        this.connection = _.assign({}, item)
      }
    }
  }
</script>
