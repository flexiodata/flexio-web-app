<template>
  <div>
    <div class="pb2 bb b--black-10">
        <btn btn-md btn-primary class="ttu b" @click="createApiKey">Create API Key</btn>
    </div>
    <div>
      <empty-item class="mv3" v-if="tokens.length == 0">
        <span slot="text">No API keys to show</span>
      </empty-item>
      <table class="w-100" v-else>
        <tbody class="lh-copy">
          <tr
            class="darken-05 hide-child"
            v-for="(token, index) in tokens"
          >
            <td class="pv2a ph3 w-100">{{token.access_code}}</td>
            <td class="pv2a tr">
              <btn
                btn-md
                btn-primary
                class="hint--top"
                aria-label="Copy to Clipboard"
                :data-clipboard-text="token.access_code"
              >
                <span class="ttu b">Copy</span>
              </btn>
            </td>
            <td class="pv2a ph3 tr">
              <span
                class="pointer f3 lh-solid b child hint--top"
                aria-label="Delete API Key"
                @click="deleteKey(token)"
              >
                &times;
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import EmptyItem from './EmptyItem.vue'
  import Btn from './Btn.vue'

  export default {
    components: {
      EmptyItem,
      Btn
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      computed: {
        is_fetched() {
          return _.get(_.find(this.getAllUsers(), { eid: this.active_user_eid }), 'tokens_fetched', false)
        },
        is_fetching() {
          return _.get(_.find(this.getAllUsers(), { eid: this.active_user_eid }), 'tokens_fetching', true)
        }
      },
      tokens() {
        return this.getOurTokens()
      }
    },
    mounted() {
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters([
        'getAllTokens'
      ]),
      tryFetchTokens() {
        if (!this.is_fetched)
          this.$store.dispatch('fetchUserTokens', { eid: this.active_user_eid })
      },
      getOurTokens() {
        var me = this

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllTokens())
          .filter(function(t) { return _.get(t, 'user_eid') == me.active_user_eid })
          .sortBy([ function(t) { return new Date(t.created) } ])
          .reverse()
          .value()
      },
      createApiKey() {
        this.$store.dispatch('createUserToken', { eid: this.active_user_eid, attrs: {} })
      },
      deleteKey(token) {
        this.$store.dispatch('deleteUserToken', { eid: this.active_user_eid, token_eid: token.eid })
      }
    }
  }
</script>
