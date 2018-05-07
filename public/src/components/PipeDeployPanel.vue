<template>
  <div class="mid-gray">
    <div class="w-100 mb4">
      <div class="cf" v-if="isOnboarding">
        <i class="el-icon-close pointer f3 black-30 hover-black-60 fr" @click="$emit('close')"></i>
        <div class="tc">
          <div class="dib">
            <h2 class="flex flex-row items-center f3 mt0 mb3"><i class="el-icon-success dark-green f3 mr2"></i> Success, your pipe has been saved!</h2>
          </div>
        </div>
      </div>
      <div class="flex flex-row items-center" v-else>
        <span class="flex-fill f4">Deploy '{{pipe_name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
    </div>

    <div class="lh-copy">
      <h3>Deploy as an API endpoint:</h3>
      <div class="mh3">
        <OnboardingCodeEditor
          label="HTTP"
          cls="relative"
          copy-prefix=""
          :is-editable="false"
          :buttons="['copy']"
          :code="example_href"
        />
      </div>
      <div class="mh3 mt3">
        <OnboardingCodeEditor
          label="cURL"
          cls="relative"
          copy-prefix=""
          :is-editable="false"
          :buttons="['copy']"
          :code="example_curl"
        />
      </div>
      <h3>Deploy in Javascript:</h3>
      <div class="mh3">
        <OnboardingCodeEditor
          label="Javascript"
          cls="relative"
          copy-prefix=""
          :is-editable="false"
          :buttons="['copy']"
          :code="pipe_code"
        />
      </div>
      <h3>Schedule from the app:</h3>
      <p class="mh3">You may schedule your pipe to run as desired from the drop-down menu in the pipe list.</p>
      <hr class="mv4 bb-0 b--black-10">
      <HelpItems
        class="mv3"
        help-message="I need help getting started with Flex.io..."
        :items="['quick-start', 'sdk-and-cli', 'api-docs', 'templates', 'help']"
        :item-cls="'f6 fw6 ttu br2 ma1 pv3 w4 pointer silver hover-blue bg-near-white darken-05'"
      />
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { Dialog } from 'element-ui'
  import Flexio from 'flexio-sdk-js'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    props: {
      'pipe': {
        type: Object,
        default: () => { return {} }
      },
      'is-onboarding': {
        type: Boolean,
        default: false
      }
    },
    components: {
      OnboardingCodeEditor,
      HelpItems
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
      pipe_name() {
        return _.get(this.pipe, 'name', '')
      },
      pipe_identifier() {
        return _.get(this.pipe, 'alias', '') || _.get(this.pipe, 'eid', '')
      },
      pipe_code() {
        var code = "Flexio.setup('"+this.api_key+"')\n\n"
        code += Flexio.pipe(this.pipe.task).toCode()
        code += '\n  .run()'
        return code
      },
      example_href() {
        return 'https://api.flex.io/v1/me/pipes/' + this.pipe_identifier + '/run?flexio_api_key=' + this.api_key
      },
      example_curl() {
        return "curl -X POST 'https://api.flex.io/v1/me/pipes/" + this.active_user_eid + "/pipes/" + this.pipe_identifier + "/run' -H 'Authorization: Bearer " + this.api_key + "'"
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
        this.$store.dispatch('fetchTokens')
      }
    }
  }
</script>
