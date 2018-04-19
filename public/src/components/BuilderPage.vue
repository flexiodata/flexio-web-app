<template>
  <div class="bg-nearer-white pa5 overflow-y-auto relative" :id="id">
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <spinner size="large" message="Loading..." />
    </div>
    <div
      class="center"
      style="max-width: 1280px"
      v-else-if="is_fetched"
    >
      <h1 class="db mv0 pb4 fw6 mid-gray tc">{{title}}</h1>
      <div class="fixed bg-white pv1 ph3 ba b--black-05" style="top: 70px; right: 30px">
        <pre class="code f6">{{code}}</pre>
      </div>
      <builder-list
        :container-id="id"
      />
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import BuilderList from './BuilderList.vue'

  const test_def = {
    "title": "Test Prompts",
    "description": "",
    "keywords": [],
    "connections": [],
    "content": "# Test prompts\n\nExample template for rendering ar\n",
    "prompts": [
      {
        "variable": "input",
        "ui": "input",
        "msg": "Enter a value",
        "value": ""
      },
      {
        "variable": "connection-chooser",
        "ui": "connection-chooser",
        "connection_type": "dropbox"
      },
      {
        "variable": "file-chooser",
        "ui": "file-chooser",
        "connection": "connection-chooser"
      },
      {
        "ui": "summary-page"
      }
    ],
    "pipe_language": "javascript",
    "pipe": "Flexio.pipe()\n  .echo('${input}')\n"
  }

  export default {
    components: {
      Spinner,
      BuilderList
    },
    watch: {
      slug: {
        handler: 'loadTemplate',
        immediate: true
      },
      active_prompt_idx: {
        handler: 'updateCode',
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
        is_fetched: state => state.builder.fetched,
        active_prompt_idx: state => state.builder.active_prompt_idx,
        title: state => state.builder.def.title,
        code: state => state.builder.code
      }),
      slug() {
        return _.get(this.$route, 'params.template', undefined)
      }
    },
    methods: {
      loadTemplate() {
        this.$store.commit('BUILDER__FETCHING_DEF', true)

        if (this.slug == 'test') {
          this.$store.commit('BUILDER__INIT_DEF', test_def)
          this.$store.commit('BUILDER__FETCHING_DEF', false)
        } else {
          axios.get('/def/templates/' + this.slug + '.json').then(response => {
            var def = response.data
            this.$store.commit('BUILDER__INIT_DEF', def)
            this.$store.commit('BUILDER__FETCHING_DEF', false)
          }).catch(error => {
            this.$store.commit('BUILDER__FETCHING_DEF', false)
          })
        }
      },
      updateCode() {
        this.$store.commit('BUILDER__UPDATE_CODE')
      }
    }
  }
</script>
