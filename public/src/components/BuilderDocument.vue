<template>
  <div
    class="bg-nearer-white ph4 overflow-y-scroll relative"
    style="padding-bottom: 8rem"
    :id="doc_id"
  >
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <Spinner size="large" message="Loading..." />
    </div>
    <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
    <div
      class="mt4 mb3 relative z-7 bg-nearer-white"
      v-if="is_fetched"
    >
      <div
        class="flex flex-row items-center center tc"
        style="max-width: 1440px"
      >
        <h1 class="flex-fill mv0 pv3 fw6 mid-gray">{{title}}</h1>
      </div>
    </div>
    <div
      class="center"
      style="max-width: 1440px"
      v-if="is_fetched"
    >
      <div class="flex flex-row">
        <BuilderList
          class="flex-fill"
          :container-id="doc_id"
          :show-insert-buttons="false"
        />
        <div
          class="dn db-l ml4 pa3 bg-white br2 css-white-box sticky"
          style="min-width: 20rem; max-width: 33%; max-height: 30rem"
        >
          <div class="h-100 flex flex-column">
            <div class="flex flex-row items-center pb2 mb2 bb b--black-10">
              <div class="flex-fill fw6 mid-gray">Output</div>
            </div>
            <CodeEditor
              class="flex-fill"
              lang="javascript"
              :options="{
                lineNumbers: false,
                readOnly: true
              }"
              v-model="code"
            />
          </div>
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
  import CodeEditor from './CodeEditor.vue'

  // easy way to get rid of a bunch of elements for quick testing
  //test_def.prompts = _.filter(test_def.prompts, { element: 'form' })
  import test_def from '../data/builder/test-def.yml'

  var pipe_arr = [ "Flexio.pipe()" ]

  var buildPipeCode = (arr) => {
    _.each(arr, p => {
      if (p.element == 'form') {
        buildPipeCode(p.form_items)
      } else if (p.variable) {
        var echo_str = "echo('" + p.variable + ": ${" + p.variable + "}')"
        pipe_arr.push(echo_str)
      }
    })
  }

  buildPipeCode(test_def.prompts)

  test_def.code_language = 'javascript'
  test_def.code = pipe_arr.join('\n  .')

  export default {
    components: {
      Spinner,
      BuilderList,
      CodeEditor
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
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      }
    },
    data() {
      return {
        doc_id: _.uniqueId('builder-doc-')
      }
    },
    computed: {
      ...mapState({
        is_fetching: state => state.builder.fetching,
        is_fetched: state => state.builder.fetched,
        active_prompt: state => state.builder.active_prompt,
        title: state => state.builder.def.title
      }),
      slug() {
        return _.get(this.$route, 'params.template', undefined)
      },
      code: {
        get() {
          return this.$store.state.builder.code
        },
        set(value) {
          // read only
        }
      }
    },
    methods: {
      loadTemplate() {
        this.$store.commit('builder/FETCHING_DEF', true)

        if (this.slug == 'test') {
          this.$store.commit('builder/INIT_DEF', test_def)
          this.$store.commit('builder/FETCHING_DEF', false)
          this.initSticky()
        } else {
          axios.get('/def/templates/' + this.slug + '.json').then(response => {
            var def = response.data
            this.$store.commit('builder/INIT_DEF', def)
            this.$store.commit('builder/FETCHING_DEF', false)
            this.$store.track('Started Template', {
              title: def.title
            })
          }).catch(error => {
            this.$store.commit('builder/FETCHING_DEF', false)
          })
        }
      },
      updateCode() {
        this.$store.commit('builder/UPDATE_CODE')
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.doc_id,
            useStickyClasses: true,
            stickyBitStickyOffset: 32
          })
        }, 100)
      }
    }
  }
</script>
