<template>
  <div class="f6 pa3">
    <div class="mb3 bb b--black-05"></div>
    <div class="mb1">
      <span class="f4">{{pipe_name}}</span><span class="ml2 silver">({{pipe_eid}})</span>
    </div>
    <vue-trend
      :data="pipe_seq"
      :gradient="['#6fa8dc', '#42b983', '#2c3e50']"
      auto-draw
      smooth
      style="height: 100px"
    ></vue-trend>
  </div>
</template>

<script>
  import VueTrend from 'vuetrend'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      VueTrend
    },
    computed: {
      pipe_name() {
        return _.get(this.item, 'pipe.name', '')
      },
      pipe_eid() {
        return _.get(this.item, 'pipe.eid', '')
      },
      pipe_items() {
        return _.get(this.item, 'items', '')
      },
      pipe_seq() {
        return _.map(this.pipe_items, (s) => {
          return _.get(s, 'total_count', 0)
        })
      }
    }
  }
</script>
