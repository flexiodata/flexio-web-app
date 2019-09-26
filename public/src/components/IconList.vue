<template>
  <div>
    <div
      class="item-container"
      :class="allowSelection ? 'item-container-selectable' : ''"
    >
      <div class="w-100 flex flex-row flex-wrap">
        <div
          class="flex flex-column items-center justify-center item-wrapper"
          :class="isSelected(item) ? 'item-selected' : ''"
          :key="getItemKey(item)"
          :title="getItemTitle(item)"
          @click="onItemClick(item)"
          v-for="item in filtered_items"
        >
          <i class="el-icon-success item-checkmark"></i>
          <div class="item-img flex flex-column justify-center">
            <ServiceIcon
              class="item-img-icon"
              :type="getItemConnectionType(item)"
              :url="getItemUrl(item)"
              :title="getItemTitle(item)"
            />
          </div>
          <div class="item-label w-100">
            <div class="tc truncate">{{getItemTitle(item)}}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import mounts from '@/data/mount-items.yml'
  import { CONNECTION_TYPE_FLEX }  from '@/constants/connection-type'
  import * as services from '@/constants/connection-info'
  import ServiceIcon from '@/components/ServiceIcon'

  const getDefaultState = () => {
    return {
      mounts,
      services
    }
  }

  export default {
    props: {
      items: {
        type: [String, Array], // 'mounts', 'services', []
        default: () => []
      },
      selectedItems: {
        type: Array,
        default: () => []
      },
      allowSelection: {
        type: Boolean,
        default: false
      },
      allowMultiple: {
        type: Boolean,
        default: false
      },
      filterBy: {
        type: Function
      }
    },
    components: {
      ServiceIcon
    },
    data() {
      return getDefaultState()
    },
    computed: {
      computed_items() {
        if (Array.isArray(this.items)) {
          return this.items
        }

        switch (this.items) {
          case 'mounts': return this.mounts
          case 'services': return _.reject(this.services, { connection_type: CONNECTION_TYPE_FLEX })
        }

        return []
      },
      selected_item_keys() {
        return _.map(this.selectedItems, item => this.getItemKey(item))
      },
      filtered_items() {
        return this.filterBy ? _.filter(this.computed_items, this.filterBy) : this.computed_items
      },
    },
    methods: {
      getItemKey(item) {
        return item.repository || item.connection_type
      },
      getItemTitle(item) {
        return _.get(item, 'connection.title') || _.get(item, 'service_name') || ''
      },
      getItemConnectionType(item) {
        return _.get(item, 'connection_type')
      },
      getItemUrl(item) {
        return _.get(item, 'connection.icon') || _.get(item, 'url')
      },
      isSelected(item) {
        return this.selected_item_keys.indexOf(this.getItemKey(item)) >= 0 ? true : false
      },
      onItemClick(item) {
        var selected_items = [].concat(this.selectedItems)

        if (this.allowMultiple) {
          if (this.isSelected(item)) {
            _.remove(selected_items, si => this.getItemKey(si) == this.getItemKey(item))
          } else {
            selected_items = selected_items.concat([item])
          }
        } else {
          selected_items = [].concat([item])
        }

        this.$emit('update:selectedItems', selected_items)
        this.$emit('item-click', item)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .item-checkmark
    color: $blue
    display: none
    position: absolute
    top: 8px
    right: 8px

  .item-img
    font-size: 64px
    width: 74px
    height: 74px
    padding: 5px
    background-color: #fff
    border-radius: 6px

  .item-img-icon
    border-radius: 4px

  .item-label
    font-weight: 600
    margin-top: 10px

  .item-wrapper
    border: 2px solid transparent
    border-radius: 4px
    cursor: pointer
    padding: 24px 16px
    position: relative
    width: 20%
    &:hover
      background-color: rgba(0,0,0,0.1)
      .item-img
        box-shadow: 0 1px 2px -1px rgba(0,0,0,0.2)

  .item-container
    position: relative

  .item-container-selectable
    margin-left: -8px
    margin-right: -8px

    .item-wrapper
      border: 2px solid transparent
      width: calc(20% - 16px)
      margin: 8px

    .item-selected
      border-color: $blue
      .item-checkmark
        display: block
</style>
