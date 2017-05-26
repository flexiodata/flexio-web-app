<template>
  <ui-modal
    ref="dialog"
    title=" "
    dismiss-on="close-button"
    class="ui-modal-onboarding"
    @hide="updateUserConfig"
  >
    <div class="w-100 tc">
      <div class="f2 mb3" style="margin-top: -1rem">Welcome to Flex.io</div>
      <div class="mv3 f5 fw6 black-60">Click on one of the example templates to get started building your first pipe.</div>
      <div class="mv4 center" style="max-width: 1280px">
        <div class="flex flex-column flex-row-l">
          <onboarding-tile-item
            v-for="(item, index) in items"
            :item="item"
            :index="index"
          ></onboarding-tile-item>
        </div>
      </div>
      <div class="mv3 fw6 black-60">
        You can also watch a quick 3-minute getting started video in the <a class="black-60" href="/docs/web-app/#overview" target="_blank">Help Docs</a>.
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
    'backup-files-from-dropbox-to-amazon-s3': {
      title: 'Backup Files from Dropbox to Amazon S3',
      short_description: 'Sometimes it is useful to automatically backup your files from one web-based file storage system (e.g., Dropbox) to another (e.g., Amazon S3). You can quickly set up a pipe to automate this process and schedule your backup as needed.',
      highlight_steps: [ctypes.CONNECTION_TYPE_DROPBOX, ctypes.CONNECTION_TYPE_AMAZONS3]
    },
    'create-a-search-application-for-a-static-website': {
      title: 'Create a Search Application for a Static Website',
      short_description: 'Static website (e.g., GitHub Pages) are fantastic; they are simple, fast and secure. However, the one thing you often lose is the ability to generate dynamic content.  With a Flex.io pipe, you can claw back some of those database-like features.  For this example, we created a small application within a blog post that enabled users to quickly search a large CSV file of podcast episode information.',
      highlight_steps: [ctypes.CONNECTION_TYPE_HTTP, ttypes.TASK_TYPE_FILTER, ctypes.CONNECTION_TYPE_STDOUT]
    },
    'reformat-a-csv-file-to-a-specified-template': {
      title: 'Reformat a CSV file to a Specified Template',
      short_description: "Often, when you go to upload a file or merge it into a system, you'll need to use a specified format. In this example, we'll assume our file requires only three fields called: `name`, `address` and `email`.  The pipe will take the full file and create some calculations that concatenate columns.  Finally, it'll remove superfluous columns and rename a field to get everything in the appropriate format.",
      highlight_steps: [ctypes.CONNECTION_TYPE_HTTP, ttypes.TASK_TYPE_SELECT, ttypes.TASK_TYPE_EMAIL_SEND]
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
      close() {
        this.$refs['dialog'].close()
        this.updateUserConfig()
      },
      updateUserConfig() {
        var cfg = _.get(this.getActiveUser(), 'config', {})
        if (_.isArray(cfg))
          cfg = {}
        cfg['app.prompt.tour.shown'] = true

        this.$store.dispatch('updateUser', { eid: this.active_user_eid, attrs: { config: cfg } })

        analytics.track('Closed Onboarding Modal')

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
