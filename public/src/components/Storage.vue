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
        :selected-item="connection"
        @item-activate="onServiceActivate"
      />
      <div class="flex-fill">
        <div v-if="has_connection">
          <div class="flex flex-row pa2 pa3-ns bb b--black-10 bg-nearer-white">
            <div class="flex-fill flex flex-row items-center">
              <div class="f2 dn db-ns mr3">{{sname}}</div>
            </div>
            <div class="flex-none flex flex-row items-center">
              <btn btn-md btn-primary class="btn-add ttu b ba">New</btn>
            </div>
          </div>
          <file-chooser
            class="pa2"
            :connection="connection"
          />
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
      sname() {
        return _.get(this.connection, 'service_name', '')
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
        this.connection = _.assign({}, item)
      }
    }
  }
</script>
