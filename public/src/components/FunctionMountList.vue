<template>
  <div class="w-100 flex flex-row flex-wrap">
    <div
      class="flex flex-column items-center justify-center mount-item"
      :key="mount.id"
      :title="mount.title"
      @click="onItemClick(mount)"
      v-for="mount in mounts"
    >
      <div
        class="mount-img flex flex-column justify-center"
        v-if="mount.url"
      >
        <ServiceIcon
          :url="mount.url"
          :title="mount.title"
        />
      </div>
      <i
        class="mount-icon material-icons"
        :title="mount.title"
        v-else-if="mount.icon"
      >
        {{mount.icon}}
      </i>
      <div class="mount-label">{{mount.title}}</div>
    </div>
  </div>
</template>

<script>
  import ServiceIcon from '@/components/ServiceIcon'

  const getDefaultState = (component) => {
    return {
      mounts: [
        { id: 'hubspot',   url: 'hubspot.com',      title: 'HubSpot'     },
        { id: 'youtube',   url: 'youtube.com',      title: 'YouTube'     },
        { id: 'wikipedia', url: 'en.wikipedia.org', title: 'Wikipedia'   },
        { id: 'facebook',  url: 'facebook.com',     title: 'Facebook'    },
        { id: 'twitter',   url: 'twitter.com',      title: 'Twitter'     },
        { id: 'amazon',    url: 'amazon.com',       title: 'Amazon'      },
        { id: 'imdb',      url: 'imdb.com',         title: 'IMDB'        },
        { id: 'reddit',    url: 'reddit.com',       title: 'Reddit'      },
        { id: 'pinterest', url: 'pinterest.com',    title: 'Pinterest'   },
        { id: 'ebay',      url: 'ebay.com',         title: 'Ebay'        },
        { id: 'custom',    icon: 'settings',        title: 'Self-Hosted' },
      ]
    }
  }

  export default {
    components: {
      ServiceIcon
    },
    data() {
      return getDefaultState()
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

  .mount-item
    border-radius: 4px
    cursor: pointer
    padding: 24px 16px
    width: 20%
    &:hover
      background-color: $black-10

  .mount-img
    border-radius: 4px
    width: 64px
    height: 64px

  .mount-icon
    font-size: 64px

  .mount-label
    font-weight: 600
    margin-top: 12px

</style>
