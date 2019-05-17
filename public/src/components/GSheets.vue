<template>
  <div id="us" class="flex flex-column">
    <div class="flex-none pa2 header" v-show="false">
      <el-radio-group v-model="view" size="tiny">
        <el-radio-button label="pipes">Pipes</el-radio-button>
        <el-radio-button label="connections">Connections</el-radio-button>
      </el-radio-group>
    </div>
    <transition-group
      tag="div"
      class="flex-fill container"
      mode="in-out"
      :name="selected_item ? 'slide-right' : 'slide-left'"
    >
      <div key="detail" class="pa3" v-if="selected_item">
        <div>
          <el-button type="text" size="small" style="padding: 0" @click="selected_item = null">&laquo; Back</el-button>
        </div>
        <h3 class="pb1 bb b--black-05">{{selected_item.name}}</h3>
        <h4>Description</h4>
        <p>{{selected_item.description}}</p>
        <h4>Connection</h4>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Incidunt minima voluptatibus laborum necessitatibus.</p>
        <h4>Data sets</h4>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ullam vel temporibus deleniti sapiente, aliquam architecto, alias minus porro ipsum itaque dolores nesciunt asperiores nihil distinctio facere libero quam neque, veritatis.</p>
        <h4>Examples</h4>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Provident, repellat minus fugiat tempore accusamus expedita amet enim ducimus qui praesentium eius quisquam neque odio saepe eum at molestias odit sed.</p>
      </div>
      <div key="pipes" v-else-if="view == 'pipes'">
        <div v-for="p in pipes" class="list-item" @click="showDetail(p)">
          {{ p.name }}
        </div>
      </div>
      <div key="connections" v-else-if="view == 'connections'">
        <div v-for="c in connections" class="list-item" @click="showDetail(c)">
          {{ c.name }}
        </div>
      </div>
      <div key="padding" class="footer-padding"></div>
    </transition-group>
    <div class="footer flex flex-row items-center justify-center">
      <a
        href="https://www.flex.io"
        title="Visit Flex.io website"
        class="no-underline gray hover-black"
        target="_blank"
      >
        <span class="relative" style="font-size: 10px; top: -1px">Powered by</span> <img src="../assets/logo-flexio-14x49.png" style="height: 10px">
      </a>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import JsonDetailsPanel from '@comp/JsonDetailsPanel'

  export default {
    components: {
      JsonDetailsPanel
    },
    data() {
      return {
        view: 'pipes',
        selected_item: null
      }
    },
    computed: {
      pipes() {
        return this.getAllPipes()
      },
      connections() {
        return this.getAllConnections()
      }
    },
    mounted() {
      this.$store.dispatch('v2_action_fetchPipes', {}).catch(error => {
        // TODO: add error handling?
      })
    },
    methods: {
      ...mapGetters([
        'getAllPipes',
        'getAllConnections'
      ]),
      showDetail(item) {
        this.selected_item = item
      },
      sendMessage(msg) {
        // Make sure you are sending a string, and to stringify JSON
        window.parent.postMessage(msg, '*');
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'
  $footer-height = 22px

  p
    line-height: 1.5

  #us
    font-size: 13px

  .header
    background-color: #616161

  .footer
    position: absolute
    bottom: 0
    left: 0
    right: 17px
    height: $footer-height
    background-color: #fff
    text-align: center

  .footer-padding
    height: $footer-height

  .container
    overflow-x: hidden
    overflow-y: auto

  .list-item
    cursor: pointer
    padding: 0.5rem
    border-bottom: 1px solid rgba(0,0,0,0.05)
    &:hover
      border-bottom-color: transparent
      color: #fff
      background-color: $blue

  .slide-left-leave-active,
  .slide-left-enter-active
    transition: 0.15s
  .slide-left-enter
    transform: translate(-100%, 0)
  .slide-left-leave-to
    transform: translate(100%, 0)

  .slide-right-leave-active,
  .slide-right-enter-active
    transition: 0.15s
  .slide-right-enter
    transform: translate(100%, 0)
  .slide-right-leave-to
    transform: translate(-100%, 0)
</style>
