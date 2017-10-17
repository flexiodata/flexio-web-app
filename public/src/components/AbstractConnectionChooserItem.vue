<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
    @mouseenter="onMouseEnter"
    @mouseover="onMouseOver"
    @mouseleave="onMouseLeave"
  >
    <div class="tc css-valign cursor-default" v-if="layout == 'grid'">
      <service-icon :type="ctype" class="dib v-mid br2 square-5"></service-icon>
      <div class="mid-gray f6 fw6 mt2">{{cname}}</div>
    </div>
    <div class="flex flex-row items-center cursor-default" v-else>
      <i class="material-icons mid-gray md-18 b mr3" v-if="showCheckmark && is_selected">check</i>
      <i class="material-icons mid-gray md-18 b mr3" style="color: transparent" v-else-if="showCheckmark && !is_selected">check</i>
      <service-icon :url="url" :type="ctype" class="br1 square-3 mr3"></service-icon>
      <div class="flex-fill flex flex-column">
        <div class="mid-gray f5 fw6 cursor-default">{{cname}}</div>
        <div class="light-silver mt1 f8" v-if="showUrl && url.length > 0">{{url}}</div>
      </div>
      <div class="code light-silver f7 ml2 ml3-ns" v-if="showIdentifier && identifier.length > 0">{{identifier}}</div>
      <div class="ml2 ml3-ns" v-if="showDropdown">
        <a
          ref="dropdownTrigger"
          tabindex="0"
          class="dib pointer pa1 light-silver hover-black"
          :class="is_hover || is_dropdown_open ? '' : 'invisible'"
          @click.stop
        ><i class="material-icons v-mid">more_vert</i></a>

        <ui-popover
          trigger="dropdownTrigger"
          ref="dropdown"
          dropdown-position="bottom right"
          @open="is_dropdown_open = true"
          @close="is_dropdown_open = false"
          v-if="is_hover || is_dropdown_open"
        >
          <ui-menu
            contain-focus
            has-icons

            :options="[{
              id: 'edit',
              label: 'Edit',
              icon: 'edit'
            },{
              id: 'delete',
              label: 'Delete',
              icon: 'delete'
            }]"

            @select="onDropdownItemClick"
            @close="$refs.dropdown.close()"
          ></ui-menu>
        </ui-popover>
      </div>
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
      'selected-item': {
        type: Object,
        default: () => { return {} }
      },
      'selected-cls': {
        type: String,
        default: 'bg-light-gray'
      },
      'show-checkmark': {
        type: Boolean,
        default: true
      },
      'show-identifier': {
        type: Boolean,
        default: true
      },
      'show-url': {
        type: Boolean,
        default: true
      },
      'show-dropdown': {
        type: Boolean,
        default: false
      }
    },
    components: {
      ServiceIcon
    },
    data() {
      return {
        is_hover: false,
        is_dropdown_open: false
      }
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
      url() {
        return _.get(this.item, 'connection_info.url', '')
      },
      identifier() {
        var cid = _.get(this.item, 'ename', '')
        return cid.length > 0 ? cid : _.get(this.item, 'eid', '')
      },
      is_selected() {
        return this.eid.length > 0
          ? _.get(this.selectedItem, 'eid') === this.eid
          : _.get(this.selectedItem, 'connection_type') === this.ctype
      },
      cls() {
        var sel_cls = this.is_selected ? this.selectedCls : ''

        if (this.itemCls.length > 0)
          return this.itemCls + ' ' + sel_cls

        if (_.get(this, 'layout', '') == 'list')
          return 'pa3 bb b--black-05 darken-05 ' + sel_cls
           else
          return 'dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10 ' + sel_cls
      }
    },
    methods: {
      onMouseEnter() {
        this.is_hover = true
      },
      onMouseLeave() {
        this.is_hover = false
      },
      onMouseOver() {
        this.is_hover = true
      },
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'edit':      return this.$emit('edit', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      },
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
