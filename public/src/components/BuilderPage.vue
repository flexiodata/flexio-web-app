<template>
  <div class="bg-nearer-white pv4">
    <div class="center mw7">
      <h1 class="fw6 f3 mid-gray mb4">Test</h1>
      <div class="css-dashboard-box pa2">
        <div v-if="true">
          <pre class="f7">{{test}}</pre>
        </div>
        <button class="ttu b blue" type="button" @click="$store.commit('BUILDER__GO_PREV_ITEM')" v-if="false">Prev</button>
        <button class="ttu b blue" type="button" @click="$store.commit('BUILDER__GO_NEXT_ITEM')">Next</button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  export default {
    data() {
      return {
        output: {
          name: '',
          task: {
            op: 'sequence',
            params: {
              items: []
            }
          }
        }
      }
    },
    computed: {
      ...mapState([
        'builder'
      ]),
      test() {
        return JSON.stringify(this.builder.active_item, null, 2)
      }
    },
    mounted() {
      this.$store.commit('BUILDER__INIT_ITEMS')
    }
  }
</script>
