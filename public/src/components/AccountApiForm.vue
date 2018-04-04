<template>
  <div>
    <div class="pb2 bb b--black-10" v-if="showCreateButton">
      <el-button type="primary" size="mini" class="ttu b" @click="createApiKey">Create API Key</el-button>
    </div>
    <empty-item class="mv3" v-if="tokens.length == 0">
      <span slot="text">No API keys to show</span>
    </empty-item>
    <div
      class="flex flex-row items-center hide-child"
      :class="showOnlyOne ? '' : 'darken-05'"
      v-for="(token, index) in tokens"
      v-else
    >
      <div
        class="flex-fill pv2 ph3"
        :class="showOnlyOne ? 'f5 min-w5 mr3 bg-black-05' : ''"
      ><pre class="ma0"><code>{{token.access_code}}</code></pre></div>
      <div class="pv2 tr">
        <el-button
          type="primary"
          size="mini"
          class="hint--top"
          aria-label="Copy to Clipboard"
          :data-clipboard-text="token.access_code"
        >
          <span class="ttu b">Copy</span>
        </el-button>
      </div>
      <div class="pv2 ph3 tr" v-if="!showOnlyOne">
        <span
          class="pointer f3 lh-solid b child hint--top"
          aria-label="Delete API Key"
          @click="deleteKey(token)"
        >
          &times;
        </span>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import EmptyItem from './EmptyItem.vue'

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
    components: {
      EmptyItem
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      is_fetched() {
        return _.get(_.find(this.getAllUsers(), { eid: this.active_user_eid }), 'tokens_fetched', false)
      },
      is_fetching() {
        return _.get(_.find(this.getAllUsers(), { eid: this.active_user_eid }), 'tokens_fetching', true)
      },
      tokens() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0)
          return []

         return this.showOnlyOne ? [].concat([ _.first(tokens) ]) : tokens
      }
    },
    mounted() {
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters([,
        'getAllTokens',
        'getAllUsers'
      ]),
      tryFetchTokens() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchTokens', { eid: this.active_user_eid })
      },
      createApiKey() {
        this.$store.dispatch('createToken', { eid: this.active_user_eid, attrs: {} })
      },
      deleteKey(token) {
        this.$store.dispatch('deleteToken', { eid: this.active_user_eid, token_eid: token.eid })
      }
    }
  }
</script>
