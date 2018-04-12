<template>
  <div class="mid-gray">
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showHeader">
        <div class="flex-fill tc">
          <span class="f3 b">Welcome to Flex.io, {{first_name}}!</span>
        </div>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
    </div>

    <div>
      <div class="tc dn">
        <div class="dib">
          <h2 class="f3 mt0 mb3">Welcome to Flex.io, {{first_name}}!</h2>
        </div>
      </div>
      <h3>This is your API key:</h3>
      <div class="mh3">
        <onboarding-code-editor
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :show-buttons="false"
          :code="api_key"
        />
      </div>
      <h3>Here's a simple pipe we created for you:</h3>
      <div class="mh3">
        <onboarding-code-editor
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :show-buttons="false"
          :code="pipe_code"
        />
      </div>
      <h3>Try running it:</h3>
      <div class="mh3">
        <onboarding-code-editor
          label="HTTP"
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :buttons="['copy']"
          :code="example_href"
        />
      </div>
      <div class="mh3 mt3">
        <onboarding-code-editor
          label="cURL"
          copy-prefix=""
          cls="relative"
          :is-editable="false"
          :buttons="['copy']"
          :code="example_curl"
        />
      </div>
      <div class="mt4 mb3 tc">
        <el-button type="primary" size="large" class="ttu b" @click="$emit('close')">Now build your own data feed</el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Flexio from 'flexio-sdk-js'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  export default {
    props: {
      'show-header': {
        type: Boolean,
        default: true
      }
    },
    components: {
      OnboardingCodeEditor
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      pipe() {
        return _.find(this.getAllPipes(), (p) => { return _.includes(_.get(p, 'alias'), 'convert-csv-to-json') })
      },
      pipe_identifier() {
        return _.get(this.pipe, 'alias', '') || _.get(this.pipe, 'eid', '')
      },
      pipe_code() {
        if (this.pipe_identifier.length == 0)
          return 'Loading...'

        return Flexio.pipe(this.pipe.task).toCode()
      },
      first_name() {
        return _.get(this.getActiveUser(), 'first_name', '')
      },
      api_key() {
        var tokens = this.getAllTokens()

        if (tokens.length == 0)
          return ''

        return _.get(tokens, '[0].access_code', '')
      },
      example_href() {
        return 'https://' + location.hostname + '/api/v2/' + this.active_user_eid + '/pipes/' + this.pipe_identifier + '/run?flexio_api_key=' + this.api_key
      },
      example_curl() {
        return "curl -X POST 'https://" + location.hostname + "/api/v2/" + this.active_user_eid + "/pipes/" + this.pipe_identifier + "/run' -H 'Authorization: Bearer " + this.api_key + "'"
      }
    },
    mounted() {
      this.tryFetchTokens()
      this.tryFetchPipes()
    },
    methods: {
      ...mapGetters([
        'getActiveUser',
        'getAllPipes',
        'getAllTokens'
      ]),
      tryFetchTokens() {
        this.$store.dispatch('fetchTokens')
      },
      tryFetchPipes() {
        this.$store.dispatch('fetchPipes')
      }
    }
  }
</script>
