<template>
  <ui-modal
    size="large"
    ref="dialog"
    dismiss-on="close-button"
    :remove-header="isOnboarding"
  >
    <div slot="header" class="w-100">
      <span class="f4">Deploy '{{pipe_name}}'</span>
    </div>

    <div class="lh-copy cf">
      <div v-if="isOnboarding">
        <div class="fr">
          <div class="ui-modal__close-button" @click="close"><button aria-label="Close" type="button" class="ui-close-button ui-close-button--size-normal ui-close-button--color-black"><div class="ui-close-button__icon"><span class="ui-icon material-icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18.984 6.422L13.406 12l5.578 5.578-1.406 1.406L12 13.406l-5.578 5.578-1.406-1.406L10.594 12 5.016 6.422l1.406-1.406L12 10.594l5.578-5.578z"></path></svg></span></div> <span class="ui-close-button__focus-ring"></span> <div class="ui-ripple-ink"></div></button></div>
        </div>

        <div class="tc">
          <div class="dib">
            <h2 class="flex flex-row items-center f3 mt0 mb3"><i class="material-icons v-mid dark-green mr2">check_circle</i> Success, your pipe has been saved!</h2>
          </div>
        </div>
      </div>
      <h3>Deploy as an API endpoint:</h3>
      <div class="mh3">
        <onboarding-code-editor
          label="HTTP"
          cls="relative"
          copy-prefix=""
          :is-editable="false"
          :buttons="['copy']"
          :code="example_href"
        />
      </div>
      <div class="mh3 mt3">
        <onboarding-code-editor
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
        <onboarding-code-editor
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
      <help-items
        class="mv3"
        help-message="I need help getting started with Flex.io..."
        :items="['quick-start', 'sdk-and-cli', 'api-docs', 'templates', 'help']"
        :item-cls="'f6 fw6 ttu br2 ma1 pv3 w4 pointer silver hover-blue bg-near-white darken-05'"
      ></help-items>
    </div>
  </ui-modal>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Flexio from 'flexio-sdk-js'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    props: {
      'is-onboarding': {
        type: Boolean,
        default: true
      }
    },
    components: {
      OnboardingCodeEditor,
      HelpItems
    },
    data() {
      return {
        pipe: {},
        pipe_name: '',
        pipe_identifier: ''
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
      pipe_code() {
        var code = "Flexio.setup('"+this.api_key+"')\n\n"
        code += Flexio.pipe(this.pipe.task).toCode()
        return code
      },
      example_href() {
        return 'https://' + location.hostname + '/api/v1/pipes/' + this.pipe_identifier + '/run?flexio_api_key=' + this.api_key
      },
      example_curl() {
        return 'curl ' + this.example_href
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
        this.$store.dispatch('fetchUserTokens', { eid: this.active_user_eid })
      },
      open(item) {
        this.pipe = _.assign({}, item)
        this.pipe_name = _.get(item, 'name', '')
        this.pipe_identifier = _.get(item, 'ename', '') || _.get(item, 'eid', '')


        this.$refs['dialog'].open()
        return this
      },
      close() {
        this.$refs['dialog'].close()
      }
    }
  }
</script>
