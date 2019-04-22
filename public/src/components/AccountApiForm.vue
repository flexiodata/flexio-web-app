<template>
  <div>
    <slot name="empty" v-if="tokens.length == 0">
      <div class="f6 blankslate">
        <em>No API keys to show</em>
        <div
          class="mt4"
          v-if="showCreateButton"
        >
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
            :offset="9"
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
    </div>
    <div
      class="mt3 mb2"
      v-if="showCreateButton && tokens.length > 0"
    >
      <el-button
        type="primary"
        class="ttu fw6"
        @click="createApiKey"
      >
        Generate API Key
      </el-button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import ConfirmPopover from '@comp/ConfirmPopover'

  export default {
    props: {
      showCreateButton: {
        type: Boolean,
        default: true
      },
      showOnlyOne: {
        type: Boolean,
        default: false
      }
    },
    components: {
      ConfirmPopover
    },
    data() {
      return {
        delete_popover_token: ''
      }
    },
    computed: {
      ...mapState({
        'is_fetching': 'tokens_fetching',
        'is_fetched': 'tokens_fetched'
      }),
      tokens() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0) {
          return []
        }

        return this.showOnlyOne ? [].concat([ _.first(tokens) ]) : tokens
      }
    },
    mounted() {
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters([,
        'getAllTokens'
      ]),
      tryFetchTokens() {
        if (!this.is_fetched) {
          this.$store.dispatch('v2_action_fetchTokens', {}).catch(error => {
            // TODO: add error handling?
          })
        }
      },
      createApiKey() {
        this.$store.dispatch('v2_action_createToken', {}).then(response => {
          this.$store.track('Created API Key')
        }).catch(error => {
          // TODO: add error handling?
        })
      },
      deleteKey(token) {
        this.$store.dispatch('v2_action_deleteToken', { eid: token.eid }).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>
