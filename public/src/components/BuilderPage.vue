<template>
  <div class="bg-nearer-white pv4 overflow-y-auto">
    <div class="center mw7">
      <h1 class="fw6 f3 mid-gray mb4">{{title}}</h1>
      <div class="bg-white pa4 css-dashboard-box">
        <div class="tc" v-if="active_prompt_ui == 'connection-chooser'">
          <ServiceIcon class="square-5" :type="active_prompt_connection_type" />
          <h2 class="fw6 f4 mid-gray mt2">Connect to <ServiceName :type="active_prompt_connection_type" /></h2>
        </div>
        <div class="tc mv4 pa4 br2 ba b--black-05" v-if="active_prompt_ui == 'connection-chooser' && active_prompt_connection">
          <ConnectionAuthenticationPanel :connection="active_prompt_connection" />
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
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { CONNECTION_STATUS_AVAILABLE } from '../constants/connection-status'
  import ServiceIcon from './ServiceIcon.vue'
  import ServiceName from './ServiceName.vue'
  import ConnectionAuthenticationPanel from './ConnectionAuthenticationPanel.vue'

  export default {
    components: {
      ServiceIcon,
      ServiceName,
      ConnectionAuthenticationPanel
    },
    watch: {
      active_prompt_idx() {
        if (this.active_prompt_ui != 'connection-chooser')
          return

        if (_.isNil(this.active_prompt_connection_eid)) {
          var attrs = { connection_type: this.active_prompt_connection_type }
          this.$store.dispatch('createConnection', { attrs }).then(response => {
            var connection = response.body
            this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { connection_eid: connection.eid })
          })
        }
      }
    },
    computed: {
      ...mapState({
        title: state => state.builder.title,
        prompts: state => state.builder.prompts,
        active_prompt: state  => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
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
      can_continue() {
        if (this.active_prompt_ui == 'connection-chooser') {
          return _.get(this.active_prompt_connection, 'connection_status', '') == CONNECTION_STATUS_AVAILABLE
        }

        return true
      }
    },
    mounted() {
      this.$store.commit('BUILDER__INIT_ITEMS')
    }
  }
</script>
