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
      style="max-width: 60rem; margin-bottom: 6rem"
      v-else-if="is_fetched"
    >
      <div class="flex flex-row items-center bg-nearer-white mv3 relative z-1 sticky">
        <h1 class="flex-fill mv0 pv3 fw6 mid-gray">{{title}}</h1>

        <el-button
          class="ttu b"
          type="plain"
          size="medium"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          size="medium"
        >
          Save
        </el-button>
      </div>
      <div>
        <h3 class="mv4 fw6 mid-gray">Properties</h3>
        <PipeDocumentForm />
        <h3 class="mv4 fw6 mid-gray">Configuration</h3>
        <CodeEditor2
          class="ba b--black-10"
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
    data() {
      return {
        code: 'function() {\n  var test = "it works!"\n}'
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
