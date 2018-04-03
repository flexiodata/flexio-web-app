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
    watch: {
      view: {
        handler: 'trackPage',
        immediate: true
      }
    },
    data() {
      return {
        view: this.initialView,
        user_info: {},
        user_eid: ''
      }
    },
    methods: {
      trackPage() {
        if (this.view == 'signup') {
          if (window.analytics) {
            window.analytics.track('Visited Sign Up Page', { label: window.location.pathname })
          }
        } else if (this.view == 'signin') {
          if (window.analytics) {
            window.analytics.track('Visited Sign In Page', { label: window.location.pathname })
          }
        }
      },
      getUserInfo(include_label) {
        var info = this.user_info
        var user_info = _.pick(info, ['first_name', 'last_name', 'email'])

        // add Segment-friendly keys
        _.assign(user_info, {
          firstName: _.get(info, 'first_name'),
          lastName: _.get(info, 'last_name'),
          username: _.get(info, 'user_name'),
          createdAt: _.get(info, 'created')
        })

        // add current pathname as 'label' (for Google Analytics)
        if (include_label === true) {
          _.assign(user_info, {
            label: window.location.pathname
          })
        }

        return user_info
      },
      onSignedIn(user_info) {
        this.$emit('signed-in')
        this.user_eid = _.get(user_info, 'eid', '')
        this.user_info = _.assign({}, this.user_info, user_info)

        // identify user
        if (window.analytics) {
          window.analytics.identify(this.user_eid, this.getUserInfo())
        }

        // track sign in
        setTimeout(() => {
          if (window.analytics) {
            window.analytics.track('Signed In', this.getUserInfo(true))
            setTimeout(function() { window.location = '/app' }, 500)
          }
        }, 100)
      },
      onSignedUp(user_info) {
        this.$emit('signed-up')
        this.user_eid = _.get(user_info, 'eid', '')
        this.user_info = _.assign({}, this.user_info, user_info)

        // identify user
        if (window.analytics) {
          window.analytics.identify(this.user_eid, this.getUserInfo())
        }
      },
      onSignedUpAndIn(user_info) {
        // track sign up
        setTimeout(() => {
          if (window.analytics) {
            window.analytics.track('Signed Up', this.getUserInfo(true))
            setTimeout(function() { window.location = '/app/learn' }, 500)
          }
        }, 100)
      }
    }
  }
</script>
