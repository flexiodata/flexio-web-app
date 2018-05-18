<template>
  <div>
    <EmptyItem class="mv4" v-if="tokens.length == 0">
      <span slot="text">No API keys to show</span>
    </EmptyItem>
    <div v-else>
      <div
        class="flex flex-row items-center hide-child"
        :class="showOnlyOne ? '' : 'br2 darken-05'"
        v-for="(token, index) in tokens"
      >
        <div
          class="flex-fill pv2 ph3"
          :class="showOnlyOne ? 'f5 min-w5 mr3 bg-black-05' : ''"
        ><pre class="ma0"><code>{{token.access_code}}</code></pre></div>
        <div class="pv2 tr">
          <el-button
            type="plain"
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
            class="pointer f3 lh-solid black-30 hover-black-60 child hint--top"
            aria-label="Delete API Key"
            @click="deleteKey(token)"
          >
            &times;
          </span>
        </div>
      </div>
    </div>
    <div :class="tokens.length == 0 ? 'tc' : 'mt3'" v-if="showCreateButton">
      <el-button type="primary" class="ttu b" @click="createApiKey">Create API Key</el-button>
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
      ...mapState({
        'is_fetching': 'tokens_fetching',
        'is_fetched': 'tokens_fetched'
      }),
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
        'getAllTokens'
      ]),
      tryFetchTokens() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchTokens')
      },
      createApiKey() {
        this.$store.dispatch('createToken')
      },
      deleteKey(token) {
        this.$store.dispatch('deleteToken', { eid: token.eid })
      }
    }
  }
</script>
