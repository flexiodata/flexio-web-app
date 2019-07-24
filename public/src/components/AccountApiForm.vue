<template>
  <div>
    <!-- fetching -->
    <div v-if="is_fetching">
      <div class="mv2 br2 pv3 ph3 bg-nearer-white ba b--black-05">
        <Spinner size="medium" message="Loading tokens..." />
      </div>
    </div>

    <!-- fetched -->
    <slot name="empty" v-else-if="tokens.length == 0">
      <div class="f6 blankslate">
        <em>No API keys to show</em>
        <div class="mt4">
          <el-button
            type="primary"
            class="ttu fw6"
            @click="createApiKey"
          >
            Generate API Key
          </el-button>
        </div>
      </div>
    </slot>
    <div v-else>
      <div
        class="mv2 f6 br2 pv2 ph3 bg-nearer-white ba b--black-05 flex flex-row items-center hide-child"
        :key="token.access_code"
        v-for="(token, index) in tokens"
      >
        <div class="flex-fill">
          <pre class="ma0"><code>{{token.access_code}}</code></pre>
        </div>
        <el-button
          type="plain"
          class="hint--top"
          aria-label="Copy to Clipboard"
          size="tiny"
          :data-clipboard-text="token.access_code"
        >
          <span class="ttu fw6">Copy</span>
        </el-button>
        <div
          class="ml3 hint--top"
          aria-label="Delete API Key"
        >
          <ConfirmPopover
            class="pointer"
            placement="bottom-end"
            message="Are you sure you want to delete this API key?"
            :offset="-4"
            :class="{
              'child black-30 hover-black-60': delete_popover_token != token,
              'black': delete_popover_token == token
            }"
            @show="delete_popover_token = token"
            @hide="delete_popover_token = ''"
            @confirm-click="deleteKey(token)"
          />
        </div>
      </div>
      <div class="mt3 mb2">
        <el-button
          type="primary"
          class="ttu fw6"
          @click="createApiKey"
        >
          Generate API Key
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import ConfirmPopover from '@/components/ConfirmPopover'

  export default {
    components: {
      Spinner,
      ConfirmPopover
    },
    data() {
      return {
        delete_popover_token: ''
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        is_fetching: state => state.tokens.is_fetching,
        is_fetched: state => state.tokens.is_fetched,
        active_team_name: state => state.teams.active_team_name
      }),
      tokens() {
        return this.getAllTokens()
      }
    },
    created() {
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters('tokens', {
        'getAllTokens': 'getAllTokens'
      }),
      tryFetchTokens() {
        var team_name = this.active_team_name

        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('tokens/fetch', { team_name })
        }
      },
      createApiKey() {
        var team_name = this.active_team_name
        this.$store.dispatch('tokens/create', { team_name })
      },
      deleteKey(token) {
        var eid = token.eid
        var team_name = this.active_team_name

        this.$store.dispatch('tokens/delete', { team_name, eid })
      }
    }
  }
</script>
