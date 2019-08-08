<template>
  <div class="bg-nearer-white">
    <div class="flex flex-column items-center justify-center h-100">
      <div
        class="measure f3 tc lh-copy"
        v-if="error_msg.length > 0"
      >
        <p>{{error_msg}}</p>
      </div>
      <Spinner
        size="large"
        message="Signing out..."
        v-else
      />
    </div>
  </div>
</template>

<script>
  import { ROUTE_SIGNIN_PAGE } from '@/constants/route'
  import Spinner from 'vue-simple-spinner'

  export default {
    components: {
      Spinner
    },
    data() {
      return {
        error_msg: ''
      }
    },
    mounted() {
      this.$store.dispatch('users/signOut', { silent: true }).then(response => {
        this.$router.replace({ name: ROUTE_SIGNIN_PAGE })
      }).catch(error => {
        this.error_msg = _.get(error, 'response.data.error.message', '')
      })
    }
  }
</script>
