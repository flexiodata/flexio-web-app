<template>
  <div class="flex flex-column justify-center items-center bg-nearer-white overflow-auto">
    <div class="center tc pv4 ph4 mw7">
      <div class="dib" style="font-size: 4rem">
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
        <router-link to="/" class="el-button el-button--large el-button--primary no-underline ttu fw6">Back to home</router-link>
      </p>
    </div>
  </div>
</template>

<script>
  import { ROUTE_SIGNIN_PAGE } from '../constants/route'

  export default {
    methods: {
      signOut() {
        this.$store.dispatch('v2_action_signOut').then(response => {
          this.$router.push({ name: ROUTE_SIGNIN_PAGE })
        }).catch(error => {
          // TODO: add error handling?
        })
      }
    }
  }
</script>
