<template>
  <div
    class="bg-nearer-white ph4 pb4 overflow-y-auto relative"
    :id="doc_id"
  >
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <Spinner size="large" message="Loading..." />
    </div>
    <div
      class="center"
      style="max-width: 1024px; margin-bottom: 6rem"
      v-else-if="is_fetched"
    >
      <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
      <div class="flex flex-row items-center mv3 relative z-7 bg-nearer-white sticky">
        <h1 class="flex-fill mv0 pv3 fw6 mid-gray tc">{{title}}</h1>

        <el-button
          class="ttu b"
          type="plain"
          size="medium"
          v-if="false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          size="medium"
          v-if="false"
        >
          Save
        </el-button>
      </div>
      <div class="pa4 bg-white br2 css-dashboard-box">
        <h3 class="mt0 mb4 fw6 mid-gray">Properties</h3>
        <PipeDocumentForm />
        <h3 class="mv4 fw6 mid-gray">Configuration</h3>
        <CodeEditor2
          class="ba b--black-10 overflow-y-auto"
          lang="javascript"
          :options="{ minRows: 24, maxRows: 24 }"
          v-model="code"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeDocumentForm from './PipeDocumentForm.vue'
  import CodeEditor2 from './CodeEditor2.vue'

  export default {
    components: {
      Spinner,
      PipeDocumentForm,
      CodeEditor2
    },
    watch: {
      eid: {
        handler: 'loadPipe',
        immediate: true
      },
      is_fetched() {
        if (!this.is_fetched)
          return

        setTimeout(() => { stickybits('.sticky') }, 100)
      }
    },
    computed: {
      ...mapState({
        edit_pipe: state => state.pipe.edit_pipe,
        is_fetching: state => state.pipe.fetching,
        is_fetched: state => state.pipe.fetched
      }),
      eid() {
        return _.get(this.$route, 'params.eid', undefined)
      },
      doc_id() {
        return 'pipe-doc-' + this.eid
      },
      orig_pipe() {
        return this.getOriginalPipe()
      },
      title() {
        return _.get(this.orig_pipe, 'name', '')
      },
      code: {
        get () {
          return this.$store.state.pipe.edit_code
        },
        set (value) {
          this.$store.commit('pipe/UPDATE_CODE', value)
        }
      }
    },
    mounted() {
      setTimeout(() => { stickybits('.sticky') }, 100)
    },
    methods: {
      ...mapGetters('pipe', [
        'getOriginalPipe'
      ]),
      loadPipe() {
        this.$store.commit('pipe/FETCHING_PIPE', true)

        this.$store.dispatch('fetchPipe', { eid: this.eid }).then(response => {
          if (response.ok) {
            var pipe = response.data
            this.$store.commit('pipe/INIT_PIPE', pipe)
            this.$store.commit('pipe/FETCHING_PIPE', false)
          } else {
            this.$store.commit('pipe/FETCHING_PIPE', false)
          }
        })
      }
    }
  }
</script>
