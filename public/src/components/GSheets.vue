<template>
  <div id="us" class="flex flex-column">
    <div class="flex-none pa2" v-show="false">
      <el-radio-group v-model="view" size="tiny">
        <el-radio-button label="pipes">Pipes</el-radio-button>
        <el-radio-button label="connections">Connections</el-radio-button>
      </el-radio-group>
    </div>
    <div class="flex-fill overflow-auto">
      <div class="pa3" v-if="selected_item">
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
      <div v-else-if="view == 'pipes'">
        <div v-for="p in pipes" class="pointer ph2 pv2 bb b--black-05 darken-05 overflow-auto" @click="showDetail(p)">
          {{ p.name }}
        </div>
      </div>
      <div v-else-if="view == 'connections'">
        <div v-for="c in connections" class="pointer ph2 pv2 bb b--black-05 darken-05 overflow-auto" @click="showDetail(c)">
          {{ c.name }}
        </div>
      </div>
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
  #us
    font-size: 13px

  .gsheets-sidebar-header
    background-color: #616161

  p
    line-height: 1.5
</style>
