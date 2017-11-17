<template>
  <flexio-modal
    title="Create Your Free Account"
    container-cls="relative"
    container-style="width: 32rem"
    :show-header="false"
    :show-footer="false"
    @cancel="$emit('cancel')"
    @submit="$emit('submit')"
  >
    <div class="pointer f3 lh-solid b child black-30 hover-black-60 mt2 mr3 absolute top-0 right-0" @click="$emit('cancel')">&times;</div>
    <div class="pv3 ph2">
      <sign-up-form
        @sign-in-click="view = 'signin'"
        @signed-up="onSignedUp"
        @signed-in="onSignedUpAndIn"
        v-if="view === 'signup'"
      />
      <sign-in-form
        @sign-up-click="view = 'signup'"
        @forgot-password-click="view = 'forgotpassword'"
        @signed-in="onSignedIn"
        v-else-if="view === 'signin'"
      />
      <forgot-password-form
        @sign-up-click="view = 'signup'"
        @sign-in-click="view = 'signin'"
        @requested-password="$emit('requested-password')"
        v-else-if="view === 'forgotpassword'"
      />
      <sign-up-modal-success
        :eid="user_eid"
        @close-click="$emit('cancel')"
        v-else-if="view === 'sign-up-success'"
      />
    </div>
  </flexio-modal>
</template>

<script>
  import _ from 'lodash'
  import FlexioModal from './FlexioModal.vue'
  import SignUpForm from './SignUpForm.vue'
  import SignInForm from './SignInForm.vue'
  import ForgotPasswordForm from './ForgotPasswordForm.vue'
  import SignUpModalSuccess from './SignUpModalSuccess.vue'

  export default {
    props: {
      'initial-view': {
        type: String,
        default: 'signup'
      }
    },
    components: {
      FlexioModal,
      SignUpForm,
      SignInForm,
      ForgotPasswordForm,
      SignUpModalSuccess
    },
    data() {
      return {
        view: this.initialView,
        user_info: {},
        user_eid: ''
      }
    },
    methods: {
      onSignedIn(user_info) {
        this.$emit('signed-in')
        this.user_eid = _.get(user_info, 'eid', '')
        this.user_info = _.assign({}, this.user_info, user_info)

        this.fireIdentify()

        setTimeout(function() {
          if (window.analytics)
            window.analytics.track('Signed In', _.omit(this.user_info, ['password']))
            setTimeout(function() { window.location = '/app' }, 300)
        }, 50)
      },
      onSignedUp(user_info) {
        this.$emit('signed-up')
        this.user_eid = _.get(user_info, 'eid', '')
        this.user_info = _.assign({}, this.user_info, user_info)

        this.fireIdentify()

        setTimeout(function() {
          if (window.analytics)
            window.analytics.track('Signed Up', _.omit(this.user_info, ['password']))
        }, 50)
      },
      onSignedUpAndIn(user_info) {
        this.$emit('signed-up-signed-in')
        this.view = 'sign-up-success'
      },
      fireIdentify() {
        if (window.analytics)
        {
          var info = this.user_info
          var user_traits = _.pick(info, ['first_name', 'last_name', 'email'])

          // add Segment-friendly keys
          _.assign(user_traits, {
            firstName: _.get(info, 'first_name'),
            lastName: _.get(info, 'last_name'),
            username: _.get(info, 'user_name'),
            createdAt: _.get(info, 'created')
          })

          window.analytics.identify(this.user_eid, user_traits)
        }
      }
    }
  }
</script>
