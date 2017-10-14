<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="flex flex-row items-center" v-if="layout == 'list'">
      <i class="material-icons mid-gray md-18 b mr3" v-if="itemShowCheckmark && is_selected">check</i>
      <i class="material-icons mid-gray md-18 b mr3" style="color: transparent" v-else-if="itemShowCheckmark">check</i>
      <service-icon :type="ctype" class="br1 square-3 mr3"></service-icon>
      <div class="mid-gray f5 fw6 cursor-default">{{cname}}</div>
    </div>
    <div class="tc css-valign" v-else>
      <service-icon :type="ctype" class="dib v-mid br2 square-5"></service-icon>
      <div class="mid-gray f6 fw6 mt2 cursor-default">{{cname}}</div>
    </div>
  </article>
</template>

<script>
  import ServiceIcon from './ServiceIcon.vue'

  export default {
    props: {
      'layout': {
        type: String,
        default: 'list' // 'list' or 'grid'
      },
      'item': {
        type: Object,
        required: true
      },
      'item-cls': {
        type: String,
        default: ''
      },
      'item-style': {
        type: String,
        default: ''
      },
      'item-selected': {
        type: Object,
        default: () => { return {} }
      },
      'item-selected-cls': {
        type: String,
        default: 'bg-light-gray'
      },
      'item-show-checkmark': {
        type: Boolean,
        default: false
      }
    },
    components: {
      ServiceIcon
    },
    computed: {
      eid() {
        return _.get(this.item, 'eid', '')
      },
      cname() {
        return _.get(this.item, 'name', '')
      },
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      is_selected() {
        return this.eid.length > 0
          ? _.get(this.itemSelected, 'eid') === this.eid
          : _.get(this.itemSelected, 'connection_type') === this.ctype
      },
      cls() {
        var sel_cls = this.is_selected ? this.itemSelectedCls : ''

        if (this.itemCls.length > 0)
          return this.itemCls + ' ' + sel_cls

        if (_.get(this, 'layout', '') == 'list')
          return 'bg-white pa3 bb b--light-gray darken-05 ' + sel_cls
           else
          return 'dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10 ' + sel_cls
      }
    },
    methods: {
      onClick: _.debounce(function() {
        this.$emit('activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>

<style lang="less">
  .css-valign {
    position: relative;
    top: 50%;
    transform: translateY(-50%);
  }
</style>
