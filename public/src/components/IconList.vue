<template>
  <div class="w-100 flex flex-row flex-wrap">
    <div
      class="flex flex-column items-center justify-center item-container"
      :key="getItemId(item)"
      :title="getItemTitle(item)"
      @click="onItemClick(item)"
      v-for="item in filtered_items"
    >
      <div
        class="item-img flex flex-column justify-center"
        v-if="item.connection_type"
      >
        <ServiceIcon
          class="item-img-icon"
          :type="item.connection_type"
          :title="getItemTitle(item)"
        />
      </div>
      <div
        class="item-img flex flex-column justify-center"
        v-else-if="item.url"
      >
        <ServiceIcon
          class="item-img-icon"
          :url="item.url"
          :title="getItemTitle(item)"
        />
      </div>
      <i
        class="item-icon material-icons"
        :title="getItemTitle(item)"
        v-else-if="item.icon"
      >
        {{item.icon}}
      </i>
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
        default: []
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
          case 'services': return this.services
        }

        return []
      },
      filtered_items() {
        return this.filterBy ? _.filter(this.computed_items, this.filterBy) : this.computed_items
      },
    },
    methods: {
      getItemId(item) {
        return item.id || item.connection_type
      },
      getItemTitle(item) {
        return item.title || item.service_name || ''
      },
      onItemClick(item) {
        this.$emit('item-click', item)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .item-container
    border-radius: 4px
    cursor: pointer
    padding: 24px 16px
    width: 20%
    &:hover
      background-color: $black-10

  .item-img
    width: 64px
    height: 64px

  .item-img-icon
    border-radius: 4px

  .item-icon
    font-size: 64px

  .item-label
    font-weight: 600
    margin-top: 12px

</style>
