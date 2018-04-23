<template>
  <div class="bg-nearer-white pa4 overflow-y-auto relative" :id="id">
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <spinner size="large" message="Loading..." />
    </div>
    <div
      class="center mv4"
      style="max-width: 1440px"
      v-else-if="is_fetched"
    >
      <h1 class="db mv0 pb4 fw6 mid-gray tc">{{title}}</h1>
      <div class="flex flex-row">
        <builder-list
          class="flex-fill"
          :container-id="id"
          :show-insert-buttons="false"
        />
        <div
          class="dn db-l ml4 pa3 bg-white br2 overflow-auto css-dashboard-box sticky"
          style="max-height: 30rem; min-width: 20rem; max-width: 33%"
        >
          <div class="ttu b silver f7 pb2 mb3 bb b--black-10">Output:</div>
          <pre class="ma0 code f6">{{code}}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import stickybits from 'stickybits'
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
        "element": "form",
        "items": [
          {
            "element": "input",
            "type": "text",
            "variable": "form_input1",
            "label": "Enter a value",
            "value": ""
          },
          {
            "element": "input",
            "type": "email",
            "variable": "form_input2",
            "label": "Enter a value",
            "value": ""
          },
          {
            "element": "input",
            "type": "number",
            "variable": "form_input3",
            "label": "Enter a value",
            "value": ""
          },
          {
            "element": "input",
            "type": "date",
            "variable": "form_input4",
            "label": "Enter a value",
            "value": ""
          }
        ]
      },
      {
        "element": "input",
        "type": "text",
        "variable": "input",
        "label": "Enter a value",
        "value": ""
      },
      {
        "element": "connection-chooser",
        "variable": "connection-chooser",
        "connection_type": "dropbox"
      },
      {
        "element": "file-chooser",
        "variable": "file-chooser",
        "connection": "connection-chooser"
      },
      {
        "element": "summary-page"
      }
    ],
    "pipe_language": "javascript",
    "pipe": "Flexio.pipe()\n  .echo('${input}')\n  .echo('${connection-chooser}')\n  .echo('${file-chooser}')\n"
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
      active_prompt: {
        handler: 'updateCode',
        immediate: true
      },
      is_fetched() {
        setTimeout(() => { stickybits('.sticky') }, 100)
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
        active_prompt: state => state.builder.active_prompt,
        title: state => state.builder.def.title,
        code: state => state.builder.code
      }),
      slug() {
        return _.get(this.$route, 'params.template', undefined)
      }
    },
    mounted() {
      setTimeout(() => { stickybits('.sticky') }, 100)
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
