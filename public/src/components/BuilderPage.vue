<template>
  <div class="bg-nearer-white pa5 overflow-y-auto" :id="id">
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <spinner size="large" message="Loading..." />
    </div>
    <builder-list
      class="center"
      style="max-width: 1280px"
      :container-id="id"
      v-else-if="is_fetched"
    />
  </div>
</template>

<script>
  import axios from 'axios'
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import BuilderList from './BuilderList.vue'

  export default {
    components: {
      Spinner,
      BuilderList
    },
    watch: {
      slug: {
        handler: 'loadTemplate',
        immediate: true
      }
    },
    data() {
      return {
        id: _.uniqueId('builder-page-')
      }
    },
    computed: {
      ...mapState({
        is_fetching: state => state.builder.fetching,
        is_fetched: state => state.builder.fetched
      }),
      slug() {
        return _.get(this.$route, 'params.template', undefined)
      }
    },
    methods: {
      loadTemplate() {
        this.$store.commit('BUILDER__FETCHING_DEF', true)

        axios.get('/def/templates/' + this.slug + '.json').then(response => {
          var def = response.data
          this.$store.commit('BUILDER__INIT_DEF', def)
          this.$store.commit('BUILDER__FETCHING_DEF', false)
        }).catch(error => {
          this.$store.commit('BUILDER__FETCHING_DEF', false)
        })
      }
    }
  }
</script>
