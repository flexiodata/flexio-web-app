<template>
  <article :class="cls" @click="onClick">
    <div class="tc css-valign" v-if="layout == 'grid'">
      <service-icon :type="ctype" class="dib v-mid br2 square-5" />
      <div class="mid-gray f6 fw6 mt2 cursor-default">{{item.name}}</div>
    </div>
    <div class="flex flex-row items-center" v-else>
      <i class="material-icons mid-gray md-18 b mr3" v-if="showSelectionCheckmark && is_selected">check</i>
      <i class="material-icons mid-gray md-18 b mr3" style="color: transparent" v-else-if="showSelectionCheckmark">check</i>
      <service-icon :type="ctype" class="br1 square-3 mr3" />
      <div class="mid-gray f5 fw6 cursor-default">{{item.name}}</div>
    </div>
  </article>
</template>

<script>
  import ServiceIcon from './ServiceIcon.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'layout': {
        type: String,
        default: 'list'
      },
      'connection-eid': {
        type: String,
        required: false
      },
      'show-selection-checkmark': {
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
      ctype() {
        return _.get(this.item, 'connection_type', '')
      },
      is_selected() {
        return this.eid.length > 0 ? this.connectionEid == this.eid : false
      },
      cls() {
        var sel_cls = this.is_selected ? 'bg-light-gray' : 'bg-white'

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
