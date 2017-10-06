<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading storage..."></spinner>
    </div>
  </div>
  <div v-else>
    <div class="flex flex-row h-100">
      <service-list
        class="br b--black-05 overflow-y-auto"
        layout="list"
        filter-items="storage"
        item-cls="bg-white pa3 pr5-l darken-05"
        :override-item-cls="true"
        @item-activate="onServiceActivate"
      />
      <div class="flex-fill">
        <div v-if="has_connection">
          <div class="flex flex-row pa2 pa3-ns">
            <div class="flex-fill flex flex-row items-center">
              <div class="f4 f3-m f2-l">{{sname}}</div>
            </div>
          </div>
          <file-chooser
            class="pa2 pa3-ns pt0 pt0-ns"
            :connection="connection"
          />
        </div>
        <div v-else-if="has_service">
          <div class="pa2 pa3-ns">
            <div class="f4 f3-m f2-l">{{sname}}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import Btn from './Btn.vue'
  import ServiceList from './ServiceList.vue'
  import FileChooser from './FileChooser.vue'
  import ConnectionConfigurePanel from './ConnectionConfigurePanel.vue'

  export default {
    components: {
      Spinner,
      Btn,
      ServiceList,
      FileChooser,
      ConnectionConfigurePanel
    },
    data() {
      return {
        service: {},
        connection: {}
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      }),
      stype() {
        return _.get(this.service, 'connection_type', '')
      },
      sname() {
        return _.get(this.service, 'service_name', '')
      },
      ctype() {
        return _.get(this.connection, 'connection_type', '')
      },
      has_service() {
        return this.stype.length > 0
      },
      has_connection() {
        return this.ctype.length > 0
      }
    },
    created() {
      this.tryFetchConnections()
    },
    methods: {
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      },
      onServiceActivate(item) {
        this.service = _.assign({}, item)
      }
    }
  }
</script>
