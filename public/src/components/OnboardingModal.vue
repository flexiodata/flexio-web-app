<template>
  <ui-modal
    size="large"
    ref="dialog"
    dismiss-on="close-button"
    :remove-header="true"
    @hide="updateUserConfig"
  >
    <div slot="header" class="w-100">
      <span class="f4">Welcome to Flex.io, {{first_name}}</span>
    </div>

    <div class="lh-copy cf">
      <div class="fr">
        <div class="ui-modal__close-button" @click="close"><button aria-label="Close" type="button" class="ui-close-button ui-close-button--size-normal ui-close-button--color-black"><div class="ui-close-button__icon"><span class="ui-icon material-icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18.984 6.422L13.406 12l5.578 5.578-1.406 1.406L12 13.406l-5.578 5.578-1.406-1.406L10.594 12 5.016 6.422l1.406-1.406L12 10.594l5.578-5.578z"></path></svg></span></div> <span class="ui-close-button__focus-ring"></span> <div class="ui-ripple-ink"></div></button></div>
      </div>
        <div class="tc">
          <h2 class="f3 mt2 mb3">Welcome to Flex.io, {{first_name}}!</h2>
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
        <div class="mt4 mb2 tc">
          <btn
            btn-lg
            btn-primary
            class="b ttu"
            @click="close"
          >Now build your own data feed</btn>
        </div>
    </div>
  </ui-modal>
</template>

<script>
  import { ROUTE_HOME_LEARN } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'
  import Flexio from 'flexio-sdk-js'
  import Btn from './Btn.vue'
  import OnboardingCodeEditor from './OnboardingCodeEditor.vue'

  export default {
    components: {
      Btn,
      OnboardingCodeEditor
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      pipe() {
        return _.find(this.getAllPipes(), (p) => { return _.includes(_.get(p, 'ename'), 'convert-csv-to-json') })
      },
      pipe_identifier() {
        return _.get(this.pipe, 'ename', '') || _.get(this.pipe, 'eid', '')
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
        return 'https://www.flex.io/api/v1/pipes/'+this.pipe_identifier+'/run?flexio_api_key='+this.api_key
      },
      example_curl() {
        return "curl -X POST 'https://www.flex.io/api/v1/pipes/"+this.pipe_identifier+"/run' -H 'Authorization: Bearer "+this.api_key+"'"
      },
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
      open() {
        this.$refs['dialog'].open()
      },
      close(close_type) {
        if (this.$route.name != ROUTE_HOME_LEARN)
          this.$router.push({ name: ROUTE_HOME_LEARN })

        this.$refs['dialog'].close()
      },
      tryFetchTokens() {
        this.$store.dispatch('fetchUserTokens', { eid: this.active_user_eid })
      },
      tryFetchPipes() {
        this.$store.dispatch('fetchPipes')
      },
      updateUserConfig() {
        return

        var cfg = _.get(this.getActiveUser(), 'config', {})
        if (_.isArray(cfg))
          cfg = {}
        cfg['app.prompt.onboarding.shown'] = true

        this.$store.dispatch('updateUser', { eid: this.active_user_eid, attrs: { config: cfg } })

        setTimeout(() => {
          analytics.identify(this.active_user_eid, { closed_onboarding_modal: 1 })
        }, 500)
      }
    }
  }
</script>
