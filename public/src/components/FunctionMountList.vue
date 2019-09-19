<template>
  <div class="w-100 flex flex-row flex-wrap">
    <div
      class="flex flex-column items-center justify-center item-container"
      :key="item.id"
      :title="item.title"
      @click="onItemClick(item)"
      v-for="item in internal_items"
    >
      <div
        class="item-img flex flex-column justify-center"
        v-if="item.url"
      >
        <ServiceIcon
          class="item-img-icon"
          :url="item.url"
          :title="item.title"
        />
      </div>
      <i
        class="item-icon material-icons"
        :title="item.title"
        v-else-if="item.icon"
      >
        {{item.icon}}
      </i>
      <div class="item-label">{{item.title}}</div>
    </div>
  </div>
</template>

<script>
  import mounts from '@/data/mount-items.yml'
  import ServiceIcon from '@/components/ServiceIcon'

  const getDefaultState = () => {
    return {
      mounts
    }
  }

  export default {
    props: {
      items: {
        type: [String, Array], // 'mounts', 'services', []
        default: []
      }
    },
    components: {
      ServiceIcon
    },
    data() {
      return getDefaultState()
    },
    computed: {
      internal_items() {
        if (Array.isArray(this.items)) {
          return this.items
        }

        switch (this.items) {
          case 'mounts': return this.mounts
        }
      }
    },
    methods: {
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
