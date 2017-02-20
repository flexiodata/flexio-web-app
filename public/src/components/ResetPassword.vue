<template>
  <main class="pa3 ph3-m pa5-ns black-60 overflow-auto">
    <form @submit.prevent class="measure-wide center">
      <div class="tc">
        <img src="../assets/logo-flexio-header.png" alt="Flex.io">
      </div>
      <legend class="f3 tc ph0 mh0 mv3 black-80 w-100">Reset Password</legend>
      <div v-if="is_sent" class="mh5-ns">
        <div class="mv3 lh-copy">
          <p>Your password has been successfully reset. To continue using Flex.io, you will need to sign in again.</p>
          <router-link to="/signin" class="link ph4 pv2a b lh-title white bg-blue b--blue darken-10 ttu tc db">Sign in</router-link>
        </div>
      </div>
      <div v-else class="mh5-ns">
        <div class="mv3 lh-copy">
          <p>Please enter a new password for your account.</p>
        </div>
        <div v-if="error_msg" class="mv3 ph3 pv2a lh-title fw6 br1 white bg-dark-red">
          {{error_msg}}
        </div>
        <div v-show="false">
          <input v-model="email" type="hidden">
          <input v-model="code" type="hidden">
        </div>
        <div class="mv3">
          <input v-model="password" v-focus :class="input_cls" placeholder="Password" type="password" autocomplete=off>
        </div>
        <div class="mv3">
          <input v-model="password2" @keyup.enter="changePassword" :class="input_cls" placeholder="Retype Password" type="password" autocomplete=off>
        </div>
        <div class="mv3">
          <btn btn-lg btn-primary :disabled="is_submitting" @click="changePassword" class="b ttu w-100">
            <span v-if="is_submitting">Submitting...</span>
            <span v-else>Change password</span>
          </btn>
        </div>
      </div>
    </form>
  </main>
</template>

<script>
  import api from '../api'
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    beforeRouteEnter(to, from, next) {
      next(vm => {
        // access to component instance via `vm`
        vm.email = _.get(to, 'query.email', '')
        vm.code = _.get(to, 'query.code', '')

        // now that we have them, we can remove the query params from the url
        vm.$router.replace(to.path)
      })
    },
    data() {
      return {
        email: '',
        code: '',
        password: '',
        password2: '',
        is_submitting: false,
        is_sent: false,
        error_msg: '',
        input_cls: 'input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue lh-title ph3 pv2a w-100'
      }
    },
    methods: {
      getAttrs() {
        // assemble non-empty values for submitting to the backend
        return _
          .chain(this.$data)
          .pick(['email', 'code', 'password', 'password2'])
          .omitBy(_.isEmpty)
          .value()
      },
      changePassword() {
        var me = this
        var attrs = this.getAttrs()

        this.is_submitting = true

        api.resetPassword({ attrs }).then((response) => {
          // success callback
          me.is_submitting = false
          me.is_sent = true
        }, (response) => {
          // error callback
          me.is_submitting = false
          me.showErrors(_.get(response, 'data.errors'))
        })
      },
      showErrors: function(errors) {
        if (_.isArray(errors) && errors.length > 0)
          this.error_msg = errors[0].message
      }
    }
  }
</script>
