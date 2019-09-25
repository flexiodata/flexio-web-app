<template>
  <div class="w-100 flex flex-row flex-wrap">
    <div
      class="flex flex-column items-center justify-center item-container"
      :key="getItemKey(item)"
      :title="getItemTitle(item)"
      @click="onItemClick(item)"
      v-for="item in filtered_items"
    >
      <div class="item-img flex flex-column justify-center">
        <ServiceIcon
          class="item-img-icon"
          :type="getItemConnectionType(item)"
          :url="getItemUrl(item)"
          :title="getItemTitle(item)"
        />
      </div>
      <div class="item-label">{{getItemTitle(item)}}</div>
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
      onItemClick(item) {
        this.$emit('item-click', item)
      }
    }
  }
</script>

<style lang="stylus" scoped>
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

  .item-container
    border-radius: 4px
    cursor: pointer
    padding: 24px 16px
    width: 20%
    &:hover
      background-color: rgba(0,0,0,0.1)
      .item-img
        box-shadow: 0 1px 2px -1px rgba(0,0,0,0.2)
</style>
