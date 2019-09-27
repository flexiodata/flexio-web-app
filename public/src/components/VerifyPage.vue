<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column items-center justify-center h-100">
      <div
        class="measure f3 tc lh-copy"
        v-if="is_verified"
      >
        <i class="el-icon-success dark-green" style="font-size: 3rem"></i>
        <p>Thank you for verifying your email address!</p>
        <p>You will be redirected to the sign in page in just a moment...</p>
      </div>
      <div
        class="measure f3 tc lh-copy"
        v-else-if="error_msg.length > 0"
      >
        <p>{{error_msg}}</p>
        <p class="mt3 mb0">
          <router-link to="/signup/verify" class="el-button el-button--primary no-underline ttu fw6" style="min-width: 11rem">Get another verification code</router-link>
        </p>
      </div>
      <Spinner
        size="large"
        message="Verifying account..."
        v-else
      />
    </div>
  </div>
</template>

<script>
  import { ROUTE_APP_ONBOARDING } from '@/constants/route'
  import api from '@/api'
  import Spinner from 'vue-simple-spinner'

  export default {
    components: {
      Spinner
    },
    data() {
      return {
        is_verified: false,
        error_msg: ''
      }
    },
    mounted() {
      this.$store.dispatch('users/signOut', { silent: true }).then(response => {
        api.verifyAccount(this.$route.query).then(response => {
          this.is_verified = true
          setTimeout(() => {
            this.$router.replace({
              name: ROUTE_APP_ONBOARDING,
              query: this.$route.query
            })
          }, 3000)
        }).catch(error => {
          this.error_msg = _.get(error, 'response.data.error.message', '')
        })
      }).catch(error => {
        this.error_msg = _.get(error, 'response.data.error.message', '')
      })
    }
  }
</script>
