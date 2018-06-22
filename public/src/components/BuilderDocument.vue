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
      <div class="flex flex-row items-center center tc mw-doc">
        <h1 class="flex-fill mv0 pv3 fw6 mid-gray">{{title}}</h1>
      </div>
    </div>
    <div
      class="center mw-doc"
      v-if="is_fetched"
    >
      <div class="flex flex-row">
        <BuilderList
          class="flex-fill"
          builder-mode="wizard"
          :items="prompts"
          :container-id="doc_id"
          :active-item-idx.sync="active_prompt_idx"
          :show-insert-buttons="false"
          :show-edit-buttons="false"
          :show-delete-buttons="false"
          @item-prev="goPrev"
          @item-next="goNext"
          @item-change="updateItemState"
          @create-pipe="createPipe"
          @open-pipe="openPipe"
        />
        <div
          class="dn db-l ml4 pa3 bg-white br2 css-white-box sticky"
          style="min-width: 20rem; max-width: 33%; max-height: 30rem"
          v-show="false"
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
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_PIPES } from '../constants/route'
  import Flexio from 'flexio-sdk-js'
  import Spinner from 'vue-simple-spinner'
  import BuilderList from './BuilderList.vue'
  import CodeEditor from './CodeEditor.vue'

  import test_def from '../data/builder/test-def.yml'
  // easy way to get rid of a bunch of elements for quick testing
  //test_def.prompts = _.filter(test_def.prompts, { element: 'form' })

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
      active_prompt_idx: {
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
        def: state => state.builder.def,
        title: state => state.builder.def.title,
        is_fetching: state => state.builder.fetching,
        is_fetched: state => state.builder.fetched,
        active_prompt_idx: state => state.builder.active_prompt_idx,
        prompts: state => state.builder.prompts,
        pipe: state => state.builder.pipe
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
      },
      save_code() {
        var name = _.get(this.def, 'title', 'Untitled Pipe')
        return this.code + '.save({ name: "' + name + '" }, callback)'
      },
      api_key() {
        return this.getSdkKey()
      },
      sdk_options() {
        return this.getSdkOptions()
      }
    },
    methods: {
      ...mapGetters([
        'getSdkKey',
        'getSdkOptions'
      ]),
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
      goPrev() {
        this.$store.commit('builder/GO_PREV_ITEM')
      },
      goNext() {
        this.$store.commit('builder/GO_NEXT_ITEM')
      },
      createPipe() {
        var pipe_fn = (Flexio, callback) => {
          eval(this.save_code)
        }

        Flexio.setup(this.api_key, this.sdk_options)

        pipe_fn.call(this, Flexio, (err, response) => {
          // TODO: error reporting?
          var pipe = response
          this.$store.commit('builder/CREATE_PIPE', pipe)
          this.$store.track('Finished Template', {
            title: this.def.title
          })
        })
      },
      openPipe() {
        var eid = this.pipe.eid
        this.$router.push({ name: ROUTE_PIPES, params: { eid } })
      },
      updateItemState(values, index) {
        this.$store.commit('builder/UPDATE_ATTRS', values)
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
