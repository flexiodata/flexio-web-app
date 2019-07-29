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
  import { ROUTE_SIGNIN_PAGE } from '@/constants/route'
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
      this.$store.dispatch('users/signOut').then(response => {
        api.verifyAccount(this.$route.query).then(response => {
          this.is_verified = true
          setTimeout(() => {
            this.$router.replace({
              name: ROUTE_SIGNIN_PAGE,
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
