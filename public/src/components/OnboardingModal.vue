<template>
  <ui-modal
    ref="dialog"
    class="ui-modal-onboarding"
    :remove-header="true"
    :remove-close-button="true"
    :dismissible="false"
    @hide="updateUserConfig"
  >
    <div class="relative w-100 tc">
      <div class="absolute top-0 right-0">
        <ui-icon-button
          icon="close"
          type="secondary"
          @click="close('close')"
        ></ui-icon-button>
      </div>
      <div class="f2 pt4 mbv">Welcome to Flex.io</div>
      <div class="mv3 f5 fw6 black-60 lh-copy mw7 center">Below are three simple templates to show how pipes work. Click on one below to get started, and then you can modify them with your own inputs and commands.</div>
      <div class="mv3 center" style="max-width: 1280px">
        <div class="flex flex-column flex-row-l">
          <onboarding-tile-item
            v-for="(item, index) in items"
            :item="item"
            :index="index"
            @use-template-click="close('template')"
          ></onboarding-tile-item>
        </div>
      </div>
      <div class="mv3 f7 fw6 black-60">
        Prefer to live dangerously? Skip these templates and <a href="#" class="blue" @click.prevent="close('skip')">jump straight into the deep end</a>. Click on the chat button at the bottom right to call a lifeguard. :)
      </div>
    </div>
  </ui-modal>
</template>

<script>
  import * as ctypes from '../constants/connection-type'
  import * as ttypes from '../constants/task-type'
  import { mapState, mapGetters } from 'vuex'
  import OnboardingTileItem from './OnboardingTileItem.vue'
  import Btn from './Btn.vue'

  const items = {
    'transform-an-image-with-python': {
      title: 'Transform an Image with Python',
      short_description: 'This template grabs a web-based image and shrinks it using Python. Then, it emails the new thumbnail as an attachment.',
      highlight_steps: [ctypes.CONNECTION_TYPE_HTTP, ttypes.TASK_TYPE_EXECUTE, ctypes.CONNECTION_TYPE_EMAIL]
    },
    'convert-a-dirty-csv-to-clean-json': {
      title: 'Convert a Dirty CSV to Clean JSON',
      short_description: 'This template takes a CSV file and concatenates various columns. Then, it removes and renames various columns and, finally, converts the file into a tidy JSON output.',
      highlight_steps: [ctypes.CONNECTION_TYPE_HTTP, ttypes.TASK_TYPE_CONVERT, ctypes.CONNECTION_TYPE_EMAIL]
    },
    'add-search-to-a-static-website': {
      title: 'Add Search to a Static Website',
      short_description: 'This pipe powers a search application on a static site using the Flex.io API. The website user inputs a search term, which is passed to the pipe as a variable. The pipe then returns the filtered data set to the website, which displays the result.',
      highlight_steps: [ctypes.CONNECTION_TYPE_HTTP, ttypes.TASK_TYPE_FILTER, ctypes.CONNECTION_TYPE_STDOUT]
    }
  }

  export default {
    components: {
      OnboardingTileItem,
      Btn
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      items() {
        // add slug and filenames based on they object's key
        return _.map(items, (val, key) => {
          return _.assign({}, val, {
            slug: key,
            markdown_file: key+'.md',
            json_file: key+'.json'
          })
        })
      },
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      open() {
        this.$refs['dialog'].open()
      },
      close(close_type) {
        if (close_type == 'close')
          analytics.track('Closed Onboarding Modal: Close Button', { close_type })
           else if (close_type == 'skip')
          analytics.track('Closed Onboarding Modal: Skip Button', { close_type })

        this.$refs['dialog'].close()
      },
      updateUserConfig() {
        var cfg = _.get(this.getActiveUser(), 'config', {})
        if (_.isArray(cfg))
          cfg = {}
        cfg['app.prompt.tour.shown'] = true

        this.$store.dispatch('updateUser', { eid: this.active_user_eid, attrs: { config: cfg } })

        setTimeout(() => {
          analytics.identify(this.active_user_eid, { closed_onboarding_modal: 1 })
        }, 500)
      }
    }
  }
</script>

<style lang="less">
  .ui-modal-onboarding {
    .ui-modal__container {
      width: 1280px;
    }
  }
</style>
