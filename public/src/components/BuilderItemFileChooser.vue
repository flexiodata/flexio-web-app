<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="is_active && description.length > 0"
    >
    </div>
    <div v-if="is_active">
      <file-chooser
        class="bb b--light-gray"
        style="max-height: 24rem"
        :connection="store_connection"
        @selection-change="updateFiles"
        v-bind="chooser_options"
        v-if="ceid"
      />
    </div>
    <div v-else-if="is_before_active">
      <div class="mb2 bt b--black-10"></div>
      <table class="w-100">
        <tbody>
          <file-chooser-item
            :item="file"
            :index="file_index"
            v-for="(file, file_index) in item.files"
          />
        </tbody>
      </table>
      <div class="mt2 bt b--black-10"></div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState, mapGetters } from 'vuex'
  import TaskIcon from './TaskIcon.vue'
  import FileChooser from './FileChooser.vue'
  import FileChooserItem from './FileChooserItem.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    components: {
      TaskIcon,
      FileChooser,
      FileChooserItem
    },
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_before_active() {
        return this.index < this.active_prompt_idx
      },
      title() {
        return _.get(this.item, 'title', 'Choose files')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      ceid() {
        return _.get(this.item, 'connection_eid', null)
      },
      connections() {
        return this.getAllConnections()
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      chooser_options() {
        var opts = _.pick(this.item, ['folders_only', 'allow_multiple', 'allow_folders'])
        return _.mapKeys(opts, (val, key) => { return _.kebabCase(key) })
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      updateFiles(files) {
        var store_files = _.map(files, (f) => {
          return _.omit(f, ['is_selected'])
        })

        this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { files: store_files })
      }
    }
  }
</script>
