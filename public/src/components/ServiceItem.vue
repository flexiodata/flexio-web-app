<template>
  <article
    :class="cls"
    @click="onClick"
  >
    <div class="flex flex-row items-center" v-if="layout == 'list'">
      <ServiceIcon :type="item.connection_type" class="br1 square-3 mr3" />
      <div class="f5 fw6 cursor-default">{{item.service_name}}</div>
    </div>
    <div class="tc css-valign" v-else>
      <ServiceIcon :type="item.connection_type" class="dib v-mid br2 square-5" />
      <div class="f6 fw6 mt2 cursor-default">{{item.service_name}}</div>
    </div>
  </article>
</template>

<script>
  import ServiceIcon from '@/components/ServiceIcon'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      layout: {
        type: String,
        default: 'list' // 'grid' or 'list'
      }
    },
    components: {
      ServiceIcon
    },
    computed: {
      cls() {
        if (this.layout == 'list') {
          return 'bg-white pa3 bb b--light-gray darken-05'
        } else {
          return 'dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10'
        }
      }
    },
    methods: {
      onClick: _.debounce(function() {
        this.$emit('activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>
