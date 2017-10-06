<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading storage..."></spinner>
    </div>
  </div>
  <div v-else>
    <div class="flex flex-row h-100">
      <service-list
        class="br b--black-05"
        layout="list"
        item-cls="bg-white pa3 pr5-l darken-05"
        :override-item-cls="true"
      />
      <div class="flex-fill">
        <file-chooser class="pa3" />
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ServiceList from './ServiceList.vue'
  import FileChooser from './FileChooser.vue'

  export default {
    components: {
      Spinner,
      ServiceList,
      FileChooser
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'connections_fetching',
        'is_fetched': 'connections_fetched'
      })
    },
    created() {
      this.tryFetchConnections()
    },
    methods: {
      tryFetchConnections() {
        if (!this.is_fetched && !this.is_fetching)
          this.$store.dispatch('fetchConnections')
      }
    }
  }
</script>
