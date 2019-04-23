<template>
  <div id="app" class="app">
    <div class="app-nav" :class="{ 'sticky': sticky_header }">
      <div class="flex flex-row items-center container">
        <div class="flex-fill">
          <router-link to="/pipes" class="mr3 dib link v-mid min-w3 hint--bottom" aria-label="Home">
            <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
          </router-link>
          <router-link to="/pipes" class="fw6 f6 ttu link nav-link" data-tour-step="pipe-onboarding-8">Pipes</router-link>
          <router-link to="/connections" class="fw6 f6 ttu link nav-link">Connections</router-link>
          <router-link to="/storage" class="fw6 f6 ttu link nav-link">Storage</router-link>
          <router-link to="/activity" class="fw6 f6 ttu link nav-link">Activity</router-link>
        </div>
        <UserDropdown />
      </div>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
  import stickybits from 'stickybits'
  import AppNavbar from '@comp/AppNavbar'
  import UserDropdown from '@comp/UserDropdown'

  export default {
    name: 'App',
    metaInfo: {
      // all titles will be injected into this template
      titleTemplate: (chunk) => {
        // if undefined or blank then we don't need the pipe
        return chunk ? `${chunk} | Flex.io` : 'Flex.io';
      },
      meta: [
        {
          vmid: 'description',
          name: 'description',
          content: 'Flex.io enables you to stitch together serverless functions with out-of-the-box helper tasks that take the pain out of OAuth, notifications, scheduling, local storage, library dependencies and other "glue" code.'
        }
      ]
    },
    components: {
      AppNavbar,
      UserDropdown
    },
    data() {
      return {
        sticky_header: false
      }
    },
    mounted() {
      setTimeout(() => {
        stickybits('.sticky', {
          useStickyClasses: true
        })
      }, 500)
    }
  }
</script>

<style lang="stylus">
  @import '../stylesheets/variables.styl'
  @import '../stylesheets/style'

  body
    overflow-y: scroll

  .container
    max-width: 1200px
    margin: 0 auto

  .app-nav
    z-index: 10
    background-color: #fff
    height: 80px
    .container
      height: 80px

  .nav-link
    margin: 0 12px
    padding-top: 4px
    padding-bottom: 2px
    border-bottom: 2px solid transparent
    color: $body-color
    transition: border 0.3s ease-in-out
    &:first-child
      margin-left: 0
    &.router-link-active
    &:hover
      border-bottom-color: $blue

  .sticky
    border-bottom: 1px solid rgba(0,0,0,0.05)
    transition: all 0.15s ease

  .sticky.js-is-sticky
  .sticky.js-is-stuck
    border-bottom: 1px solid rgba(0,0,0,0.1)
    box-shadow: 0 4px 16px -6px rgba(0,0,0,0.2)
</style>
