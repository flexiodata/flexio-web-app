<template>
  <div id="us">
    <div class="mt1 pa2">
      <el-radio-group v-model="view" size="tiny">
        <el-radio-button label="pipes">Pipes</el-radio-button>
        <el-radio-button label="connections">Connections</el-radio-button>
      </el-radio-group>
    </div>
    <div v-if="view == 'pipes'">
      <div v-for="p in pipes" class="pointer pa2 bb b--black-05 darken-05 overflow-auto">
        {{ p.name }}
      </div>
    </div>
    <div v-if="view == 'connections'">
      <div v-for="c in connections" class="pointer pa2 bb b--black-05 darken-05 overflow-auto">
        {{ c.name }}
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  export default {
    data() {
      return {
        view: 'pipes'
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
      ])
    }
  }
</script>

<style lang="stylus" scoped>
  #us
    font-size: 13px
</style>
