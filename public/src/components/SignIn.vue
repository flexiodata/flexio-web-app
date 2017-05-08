<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <form @submit.prevent class="measure-wide center">
      <div class="tc">
        <img src="../assets/logo-flexio-header.png" alt="Flex.io">
      </div>
      <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Sign in</legend>
      <div class="mh5-ns">
        <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
          {{error_msg}}
        </div>
        <div class="mv3">
          <input type="text" v-model="username" :class="input_cls" class="bg-black-10" placeholder="Email or username" disabled v-if="username_provided">
          <input type="text" v-model="username" :class="input_cls" placeholder="Email or username" v-else v-focus>
        </div>
        <div class="mv3">
          <input v-model="password" @keyup.enter="trySignIn" :class="input_cls" placeholder="Password" type="password" autocomplete=off spellcheck="false" v-if="username_provided" v-focus>
          <input v-model="password" @keyup.enter="trySignIn" :class="input_cls" placeholder="Password" type="password" autocomplete=off spellcheck="false" v-else>
          <router-link to="/forgotpassword" class="f8 fw6 black-60 link underline-hover dib">Forgot your password?</router-link>
        </div>
        <div class="mv3">
          <btn btn-lg btn-primary :disabled="is_submitting" @click="trySignIn" class="b ttu w-100">
            <span v-if="is_submitting">Signing in...</span>
            <span v-else>Sign in</span>
          </btn>
        </div>
      </div>
      <div class="tc f5 fw6 mv3">
        New to Flex.io?
        <router-link :to="signup_link" class="link dib blue underline-hover db">Sign up</router-link>
      </div>
    </form>
  </main>
</template>

<script>
  import api from '../api'
  import { ROUTE_SIGNUP } from '../constants/route'
  import Btn from './Btn.vue'
  import Redirect from './mixins/redirect'

  export default {
    mixins: [Redirect],
    components: {
      Btn
    },
    beforeRouteEnter(to, from, next) {
      next(vm => {
        // access to component instance via `vm`
        vm.verify_code = _.get(to, 'query.verify_code', '')
        vm.username = _.get(to, 'query.email', '')

        // if an email address has been provided to us, this indicates
        // the user has been invited to Flex.io; disable the email input
        // and show it at the top of the form -- all the user needs
        // to do is to enter their first/last name, username and
        // a password to create their account
        if (vm.username.length > 0)
          vm.username_provided = true
      })
    },
    data() {
      return {
        username: '',
        password: '',
        username_provided: false,
        is_submitting: false,
        error_msg: '',
        verify_code: '',
        input_cls: 'input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    computed: {
      signup_link() {
        return {
            name: ROUTE_SIGNUP,
            query: this.$route.query

        }
      }
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _
          .chain(this.$data)
          .pick(['username', 'password', 'verify_code'])
          .omitBy(_.isEmpty)
          .value()
      },
      trySignIn() {
        var attrs = this.getAttrs()

        this.is_submitting = true

        this.$store.dispatch('signIn', { attrs }).then(response => {
          if (response.ok)
          {
            this.is_submitting = false
            this.redirect()
          }
           else
          {
            this.is_submitting = false
            this.password = ''
            this.showErrors(_.get(response, 'data.errors'))
          }
        })
      },
      showErrors: function(errors) {
        if (_.isArray(errors) && errors.length > 0)
          this.error_msg = errors[0].message
      }
    }
  }
</script>
