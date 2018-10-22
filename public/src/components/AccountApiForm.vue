<template>
  <div>
    <slot name="empty" v-if="tokens.length == 0">
      <div class="pv2 ph3 tc f6">
        <em>No API keys to show</em>
      </div>
    </slot>
    <div v-else>
      <div
        class="flex flex-row items-center hide-child"
        :class="showOnlyOne ? '' : 'br2 hover-bg-nearer-white'"
        v-for="(token, index) in tokens"
      >
        <div
          class="flex-fill pv2 ph3"
          :class="showOnlyOne ? 'br2 min-w5 mr2 bg-nearer-white' : ''"
        >
          <pre class="ma0"><code>{{token.access_code}}</code></pre>
        </div>
        <div class="pv2 tr">
          <el-button
            type="plain"
            class="hint--top"
            aria-label="Copy to Clipboard"
            :size="showOnlyOne ? 'small' : 'mini'"
            :data-clipboard-text="token.access_code"
          >
            <span class="ttu b">Copy</span>
          </el-button>
        </div>
        <div class="pv2 ph3 tr" v-if="!showOnlyOne">
          <span
            class="pointer f3 lh-solid black-30 hover-black-60 child hint--top"
            aria-label="Delete API Key"
            @click="deleteKey(token)"
          >
            &times;
          </span>
        </div>
      </div>
    </div>
    <div class="mt2 mb1" :class="{ 'tc': tokens.length == 0 }" v-if="showCreateButton">
      <el-button
        type="primary"
        class="ttu b"
        @click="createApiKey"
      >
        Generate API Key
      </el-button>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  export default {
    props: {
      'show-create-button': {
        type: Boolean,
        default: true
      },
      'show-only-one': {
        type: Boolean,
        default: false
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
          this.$store.dispatch('v2_action_fetchTokens', {})
        }
      },
      createApiKey() {
        this.$store.dispatch('v2_action_createToken', {}).then(response => {
          this.$store.track('Created API Key')
        })
      },
      deleteKey(token) {
        this.$store.dispatch('deleteToken', { eid: token.eid })
      }
    }
  }
</script>
