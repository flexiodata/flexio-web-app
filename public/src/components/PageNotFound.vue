<template>
  <div class="flex flex-column justify-center items-center bg-nearer-white overflow-auto">
    <div class="center tc pv4 ph4 mw7">
      <div class="dib" style="font-size: 4.5rem">
        <div class="flex flex-row items-center justify-center w4 h4 br-100 bg-dark-gray white">
          <div class="rotate-90 pb2 fw6">:-\</div>
        </div>
      </div>
      <h1 class="fw6 lh-title mv4">Page not found</h1>
      <h2 class="fw4 orange mb3">You might not have permissions to see this page.</h2>
      <p class="mv3">
        <el-button
          type="text"
          class="blue fw6"
          @click="signOut"
        >
          <div class="flex flex-row items-center">
            <span>Log in with a different user</span>
            <i class="material-icons">arrow_right_alt</i>
          </div>
        </el-button>
      </p>
      <p class="mt3 mb0">
        <router-link to="/" class="el-button el-button--primary no-underline ttu fw6" style="min-width: 11rem">Back to home</router-link>
      </p>
    </div>
  </div>
</template>

<script>
  import { ROUTE_SIGNIN_PAGE } from '../constants/route'

  export default {
    metaInfo() {
      return {
        title: 'Page not found'
      }
    },
    methods: {
      signOut() {
        // NOTE: we need to route back to the sign in page before we actually dispatch
        //       the `users/signOut` call, otherwise a 404 page will show momentarily
        this.$router.push({ name: ROUTE_SIGNIN_PAGE })
        this.$store.dispatch('users/signOut')
      }
    }
  }
</script>
