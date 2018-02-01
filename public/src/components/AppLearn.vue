<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Learn to create data feeds in a few simple steps</div>
      <div class="mt4 mb3 f5">
        <span class="dib ma1">I want to</span>
        <select class="pa1 ba b--black-10" v-model="active_item_id">
          <option
            :value="item.id"
            v-for="(item, index) in items"
          >
            {{ item.want.replace('I want to ', '') }}
          </option>
        </select>
      </div>
      <onboarding-item
        :item="active_item"
        :api-key="api_key"
        :sdk-options="sdk_options"
      />
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import OnboardingItem from './OnboardingItem.vue'

  const item1 = require('json-loader!yaml-loader!../data/onboarding/copy-file-directory-to-cloud-storage.yml')
  const item2 = require('json-loader!yaml-loader!../data/onboarding/collect-data-from-api.yml')
  const item3 = require('json-loader!yaml-loader!../data/onboarding/process-tabular-data.yml')
  const items = [item1, item2, item3]

  export default {
    components: {
      OnboardingItem
    },
    data() {
      return {
        items,
        active_item_id: ''
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      api_key() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0)
          return ''

        return _.get(tokens, '[0].access_code', '')
      },
      sdk_options() {
        return { baseUrl: 'https://' + window.location.hostname + '/api/v1' }
      },
      active_item() {
        var step = _.find(this.items, { id: this.active_item_id })
        return _.defaultTo(step, {})
      }
    },
    mounted() {
      this.active_item_id = _.get(this.items, '[0].id', '')
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters([,
        'getAllTokens'
      ]),
      tryFetchTokens() {
        this.$store.dispatch('fetchUserTokens', { eid: this.active_user_eid })
      }
    }
  }
</script>
