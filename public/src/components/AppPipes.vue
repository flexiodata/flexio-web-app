<template>
  <!-- fetching -->
  <div v-if="is_fetching || force_loading">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading pipes..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column overflow-y-scroll" :id="doc_id" v-else-if="is_fetched">
    <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
    <div class="mt4 relative z-7 bg-white sticky">
      <div class="center w-100 pa3 pl4-l pr4-l bb bb-0-l b--black-10 sticky" style="max-width: 1280px">
        <!-- control bar -->
        <div class="flex flex-row">
          <div class="flex-fill flex flex-row items-center">
            <h1 class="mv0 f2 fw4 mr3">{{title}}</h1>
          </div>
          <div class="flex-none flex flex-row items-center">
            <el-input
              class="w-100 mw5 mr3"
              placeholder="Search..."
              clearable
              prefix-icon="el-icon-search"
              @keydown.esc.native="filter = ''"
              v-model="filter"
            />
            <el-button type="primary" class="ttu fw6" @click="onNewPipeClick">New pipe</el-button>
          </div>
        </div>
      </div>
    </div>

    <!-- list -->
    <PipeList
      class="center w-100 pl4-l pr4-l pb4-l"
      style="max-width: 1280px; padding-bottom: 8rem"
      :filter="filter"
      :show-header="true"
      :show-selection-checkboxes="false"
      @item-duplicate="duplicatePipe"
      @item-delete="tryDeletePipe"
    />
  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import { ROUTE_PIPE_PAGE } from '../constants/route'
  import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PipeList from '@comp/PipeList'

  export default {
    metaInfo: {
      title: 'Pipes'
    },
    components: {
      Spinner,
      PipeList
    },
    watch: {
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      }
    },
    data() {
      return {
        doc_id: _.uniqueId('app-pipes-'),
        force_loading: false,
        filter: ''
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'pipes_fetching',
        'is_fetched': 'pipes_fetched'
      }),
      routed_user() {
        return this.$store.state.routed_user
      },
      title() {
        var ru = this.routed_user
        return ru && ru.length > 0 ? ru + '/' + 'pipes' : 'Pipes'
      }
    },
    mounted() {
      this.initSticky()
      this.tryFetchPipes()
      this.$store.track('Visited Pipes Page')
      this.force_loading = true
      setTimeout(() => { this.force_loading = false }, 10)
    },
    methods: {
      openPipe(eid) {
        // TODO: this component shouldn't have anything to do with the route or store state
        var ru = this.routed_user
        var user_identifier = ru && ru.length > 0 ? ru : null
        var identifier = eid
        this.$router.push({ name: ROUTE_PIPE_PAGE, params: { user_identifier, identifier } })
      },
      duplicatePipe(item) {
        var attrs = {
          copy_eid: item.eid,
          name: item.name
        }

        this.$store.dispatch('v2_action_createPipe', { attrs }).catch(error => {
          // TODO: add error handling?
        })
      },
      initSticky() {
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.doc_id,
            useStickyClasses: true
          })
        }, 100)
      },
      tryFetchPipes() {
        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('v2_action_fetchPipes', {}).catch(error => {
            // TODO: add error handling?
          })
        }
      },
      tryCreatePipe(attrs) {
        if (!_.isObject(attrs))
          attrs = { name: 'Untitled Pipe' }

        this.$store.dispatch('v2_action_createPipe', { attrs }).then(response => {
          var pipe = response.data
          var analytics_payload = _.pick(pipe, ['eid', 'name', 'alias', 'created'])
          this.$store.track('Created Pipe', analytics_payload)
          this.openPipe(pipe.eid)
        }).catch(error => {
          this.$store.track('Created Pipe (Error)')
        })
      },
      tryDeletePipe(attrs) {
        var eid = _.get(attrs, 'eid', '')
        var name = _.get(attrs, 'name', 'Pipe')

        this.$confirm('Are you sure you want to delete the pipe named "' + name + '"?', 'Really delete pipe?', {
          confirmButtonClass: 'ttu fw6',
          cancelButtonClass: 'ttu fw6',
          confirmButtonText: 'Delete pipe',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }).then(() => {
          this.$store.dispatch('v2_action_deletePipe', { eid }).catch(error => {
            // TODO: add error handling?
          })
        }).catch(() => {
          // do nothing
        })
      },
      onNewPipeClick() {
        /*
        // when creating a new pipe, start out with a basic Python 'Hello World' script
        var attrs = {
          name: 'Untitled Pipe',
          task: {
            op: 'sequence',
            items: [{
              op: 'execute',
              lang: 'python',
              code: 'IyBiYXNpYyBoZWxsbyB3b3JsZCBleGFtcGxlCmRlZiBmbGV4X2hhbmRsZXIoZmxleCk6CiAgICBmbGV4LmVuZCgiSGVsbG8sIFdvcmxkLiIpCg=='
            }]
          }
        }
        */

        // when creating a new pipe, start out with a basic read task
        var attrs = {
          name: 'Untitled Pipe',
          task: {
            op: 'sequence',
            items: [{
              op: 'read',
              title: 'Select Source'
            }]
          }
        }

        this.tryCreatePipe(attrs)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .sticky
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
