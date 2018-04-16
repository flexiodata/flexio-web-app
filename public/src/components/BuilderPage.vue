<template>
  <div class="bg-nearer-white pv4 overflow-y-auto">
    <div class="center mw7">
      <div class="mv5" v-if="is_fetching">
        <spinner size="large" message="Loading template..." />
      </div>
      <div v-else-if="is_fetched">
        <h1 class="fw6 f3 mid-gray mb4">{{title}}</h1>
        <div class="bg-white pa4 css-dashboard-box">
          <div class="tc" v-if="active_prompt_ui == 'connection-chooser'">
            <ServiceIcon class="square-5" :type="active_prompt_connection_type" />
            <h2 class="fw6 f4 mid-gray mt2">Connect to {{active_prompt_service_name}}</h2>
            <div class="mv4 pa4 br2 ba b--black-05">
              <ConnectionAuthenticationPanel
                :connection="active_prompt_connection"
                v-if="active_prompt_connection"
              />
              <div class="tl" v-else>
                <connection-chooser-list
                  class="mb4"
                  layout="list"
                  :connection-type-filter="active_prompt_connection_type"
                  :show-selection="true"
                  :show-selection-checkmark="true"
                  @item-activate="chooseConnection"
                  v-if="active_prompt_connections.length > 0"
                />
                <el-button
                  class="ttu b"
                  type="primary"
                  @click="setUpConnection"
                >
                  Set up a new connection
                </el-button>
              </div>
            </div>
          </div>
          <div class="tc mv4 pa4 br2 ba b--black-05" v-else-if="active_prompt_ui == 'file-chooser'">
            File Chooser
          </div>
          <div class="flex flex-row justify-end">
            <el-button
              class="ttu b"
              type="plain"
              @click="$store.commit('BUILDER__GO_PREV_ITEM')"
              v-show="!is_first_prompt"
            >
              Go Back
            </el-button>
            <el-button
              class="ttu b"
              type="primary"
              :disabled="!can_continue"
              @click="$store.commit('BUILDER__GO_NEXT_ITEM')"
            >
              Continue
            </el-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import { mapState, mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import Spinner from 'vue-simple-spinner'
  import ServiceIcon from './ServiceIcon.vue'
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'
  import MixinConnectionInfo from './mixins/connection-info'

  export default {
    mixins: [MixinConnectionInfo],
    components: {
      Spinner,
      ServiceIcon,
      ConnectionChooserList,
      ConnectionAuthenticationPanel
    },
    watch: {
      template_slug: {
        handler: 'updateTemplate',
        immediate: true
      }
    },
    computed: {
      ...mapState({
        title: state => state.builder.title,
        prompts: state => state.builder.prompts,
        active_prompt: state  => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx,
        is_fetching: state => state.builder.fetching,
        is_fetched: state => state.builder.fetched
      }),
      connections() {
        return this.getAllConnections()
      },
      template_slug() {
        return _.get(this.$route, 'params.template', undefined)
      },
      active_prompt_ui() {
        return _.get(this.active_prompt, 'ui', '')
      },
      is_first_prompt() {
        return this.active_prompt_idx == 0
      },
      is_last_prompt() {
        return this.active_prompt_idx == this.prompts.length - 1
      },
      active_prompt_connection() {
        var connection_eid = _.get(this.active_prompt, 'connection_eid', '')
        return _.get(this.$store, 'state.objects.'+connection_eid, null)
      },
      active_prompt_connection_eid() {
        return _.get(this.active_prompt, 'connection_eid', '')
      },
      active_prompt_connection_type() {
        return _.get(this.active_prompt, 'connection_type', '')
      },
      active_prompt_service_name() {
        return this.getConnectionInfo(this.active_prompt_connection_type, 'service_name')
      },
      active_prompt_connections() {
        var connection_type = this.active_prompt_connection_type
        return _.filter(this.connections, { connection_type })
      },
      can_continue() {
        if (this.active_prompt_ui == 'connection-chooser') {
          return _.get(this.active_prompt_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
        }

        return true
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      setUpConnection() {
        if (this.active_prompt_ui != 'connection-chooser')
          return

        if (_.isNil(this.active_prompt_connection_eid)) {
          var attrs = {
            //eid_status: OBJECT_STATUS_PENDING,
            name: this.active_prompt_service_name,
            connection_type: this.active_prompt_connection_type
          }
          this.$store.dispatch('createConnection', { attrs }).then(response => {
            var connection = response.body
            this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
          })
        }
      },
      chooseConnection(connection) {
        this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
      },
      updateTemplate() {
        this.$store.commit('BUILDER__FETCHING_DEF', true)

        axios.get('/def/templates/' + this.template_slug + '.json').then(response => {
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
