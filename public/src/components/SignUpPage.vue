<template>
  <main class="pa3 ph3-m pa5-ns bg-nearer-white black-60 overflow-auto">
    <div class="measure center">
      <sign-up-form
        class="br2 bg-white pa3 pa4-ns css-white-box"
        @sign-in-click="onSignInClick"
        @signed-in="onSignedIn"
      />
    </div>
  </main>
</template>

<script>
  import { ROUTE_SIGNIN } from '../constants/route'
  import SignUpForm from './SignUpForm.vue'
  import MixinRedirect from './mixins/redirect'

  export default {
    mixins: [MixinRedirect],
    components: {
      SignUpForm
    },
    computed: {
      signin_route() {
        return {
          name: ROUTE_SIGNIN,
          query: this.$route.query
        }
      }
    },
    mounted() {
      this.$store.track('Visited Sign Up Page')
    },
    methods: {
      onSignInClick() {
        this.$router.push(this.signin_route)
      },
      onSignedIn() {
        this.$_Redirect_redirect()
      }
    }
  }
</script>
