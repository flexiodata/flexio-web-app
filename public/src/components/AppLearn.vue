<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Let's build a data feed</div>
      <p class="mw7 lh-copy">Welcome to our interactive tutorial. Choose a topic from the drop-down menu to get started.<br>Then, follow the steps below to get a deployable pipe in just a few minutes.</p>
      <div class="mv3 f5 lh-copy">
        <span class="dib mr1">I want to</span>
        <el-select
          class="f5"
          placeholder="select an onboarding item"
          style="width: 360px"
          @change="onOnboardingItemChange"
          v-model="active_item_id"
        >
          <el-option
            v-for="item in items"
            :key="item.id"
            :label="item.want.replace('I want to ', '')"
            :value="item.id">
          </el-option>
        </el-select>
      </div>
      <onboarding-item
        :item="active_item"
        :api-key="api_key"
        :sdk-options="sdk_options"
        :username="active_username"
      />
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import items from '../data/onboarding'
  import OnboardingItem from './OnboardingItem.vue'

  export default {
    components: {
      OnboardingItem
    },
    data() {
      return {
        items: _.values(items),
        active_item_id: ''
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      active_username() {
        return _.get(this.getActiveUser(), 'username', '')
      },
      api_key() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0)
          return ''

        return _.get(tokens, '[0].access_code', '')
      },
      sdk_options() {
        switch (window.location.hostname) {
          case 'localhost':    return { host: 'localhost', insecure: true }
          case 'test.flex.io': return { host: 'test.flex.io' }
          case 'www.flex.io':  return { host: 'www.flex.io' }
        }

        return {}
      },
      active_item() {
        var item = _.find(this.items, { id: this.active_item_id })
        return _.defaultTo(item, {})
      }
    },
    mounted() {
      this.active_item_id = _.get(this.items, '[0].id', '')
      this.tryFetchTokens()
    },
    methods: {
      ...mapGetters([,
        'getAllTokens',
        'getActiveUser'
      ]),
      tryFetchTokens() {
        this.$store.dispatch('v2_action_fetchTokens', {})
      },
      onOnboardingItemChange(id) {
        var item = _.find(this.items, { id })

        //this.$store.track('Switched Onboarding Item', {
        //  title: item.name
        //})
      }
    }
  }
</script>
