<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <div class="measure-wide center">
      <sign-up-form class="mh5-ns" @sign-in-click="onSignInClick" />
    </div>
  </main>
</template>

<script>
  import { ROUTE_SIGNIN } from '../constants/route'
  import SignUpForm from './SignUpForm.vue'

  export default {
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
      analytics.track('Visited Sign Up Page')
    },
    methods: {
      onSignInClick() {
        this.$router.push(this.signin_route)
      }
    }
  }
</script>
