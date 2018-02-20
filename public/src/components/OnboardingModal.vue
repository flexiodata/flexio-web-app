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

        <h2 class="flex flex-row items-center f3 mt0"><i class="material-icons v-mid dark-green mr2">tag_faces</i> Welcome to Flex.io, {{first_name}}!</h2>
        <p class="mt4">Here is an API key to get you started:</p>
        <div class="flex flex-row">
          <div class="flex-fill pv2 ph3 f3 tc b black code ba b--black-10">{{api_key}}</div>
        </div>
        <h3>Try it out</h3>
        <p>Here's a sample Flex.io API endpoint.</p>
        <ol>
          <li>
            <h4 class="">HTTP</h4>
            <p>Click the link to run the example pipe and see the output:</p>
            <a
              target="_blank"
              class="blue"
              :class="pipe_identifier.length == 0 ? 'o-40 no-pointer-events' : ''"
              :href="example_href"
            >{{example_href}}</a>
          </li>
          <li>
            <h4 class="">cURL</h4>
            <p>Copy and paste the following cURL call into your command line to run the example pipe and see the output:</p>
            <onboarding-code-editor
              copy-prefix=""
              :cls="pipe_identifier.length == 0 ? 'o-40 no-pointer-events relative' : 'relative'"
              :is-editable="false"
              :buttons="['copy']"
              :code="example_curl"
            />
          </li>
        </ol>
        <hr class="mv4 bb-0 b--black-10">
        <p>Now that you've got a sense of deployment, let's build your first data feed!</p>
        <div class="cf">
          <btn
            btn-md
            btn-primary
            class="b ttu fr"
            @click="close"
          >Let's build a data feed!</btn>
        </div>
    </div>
  </ui-modal>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
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
      pipe_identifier() {
        var pipe = _.find(this.getAllPipes(), (p) => { return _.includes(_.get(p, 'ename'), 'pivot-table') })

        return _.get(pipe, 'ename', '') || _.get(pipe, 'eid', '')
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
