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

        <h2 class="flex flex-row items-center f3 mt0"><i class="material-icons v-mid dark-green mr2">check_circle</i> Success!</h2>

        <p>The <strong>{{pipe_name}}</strong> pipe has been added to your account and is now in your pipe list.</p>
        <p>To deploy your pipe in the wild, try one of these options:</p>
      </div>
      <p class="mt0" v-else>To deploy your pipe in the wild, try one of these options:</p>

      <div class="ml3">
        <h4 class="mb2">cURL:</h4>
        <div class="marked">
          <code class="db">curl -X POST 'https://www.flex.io/api/v1/pipes/{{pipe_identifier}}/run' -H 'Authorization: Bearer {{api_key}}'</code>
        </div>

        <h4 class="mb2">HTTP:</h4>
        <div class="marked">
          <pre><code>$.ajax({
  type: 'POST',
  url: 'http://www.flex.io/api/v1/pipes/{{pipe_identifier}}/run',
  beforeSend: function(xhr) {
    xhr.setRequestHeader('Authorization', 'Bearer {{api_key}}')
  }
})</code></pre>
        </div>
        <h4 class="mb2">CRON:</h4>
        <p class="mt0">You may schedule your pipe to run as desired from the drop-down menu in the pipe list.</p>
      </div>
      <hr class="mv4 bb-0 b--black-10">
      <p>If you have any questions about deployment, please send us a note using the chat button at the bottom right of the screen; we're more than happy to help! Thanks.</p>
    </div>
  </ui-modal>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  export default {
    props: {
      'is-onboarding': {
        type: Boolean,
        default: true
      }
    },
    data() {
      return {
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
