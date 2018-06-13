<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <div v-show="show_controls">
      <FileChooser
        class="bb b--light-gray"
        style="max-height: 24rem"
        :connection="store_connection"
        @open-folder="updateFolder"
        @selection-change="updateFiles"
        v-bind="chooser_options"
        v-if="ceid"
      />

      <div class="flex flex-row items-center mt2 f7" v-if="is_single_folder_select || is_single_file_select">
        <div class="mr2" v-if="is_single_folder_select">Folder:</div>
        <div class="mr2" v-else-if="is_single_file_select">File name:</div>
        <div class="flex-fill ba b--black-10 silver bg-near-white cursor-not-allowed" style="padding: 7px">
          <span v-if="folder_path.length > 0">{{folder_path}}</span><span v-else>(None selected)</span>
        </div>
      </div>
    </div>

    <div v-if="show_summary">
      <div class="mb2 bt b--black-10"></div>
      <table class="w-100">
        <tbody>
          <FileChooserItem
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
  import FileChooser from './FileChooser.vue'
  import FileChooserItem from './FileChooserItem.vue'

  const VFS_TYPE_DIR = 'DIR'

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
      FileChooser,
      FileChooserItem
    },
    data() {
      return {
        is_changed: false
      }
    },
    computed: {
      ...mapState({
        mode: state => state.builder.mode,
        prompts: state => state.builder.prompts,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      builder__is_prompt_mode() {
        return this.mode == 'prompt'
      },
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_before_active() {
        return this.index < this.active_prompt_idx
      },
      is_single_folder_select() {
        return this.item.folders_only === true && this.item.allow_multiple === false
      },
      is_single_file_select() {
        return this.item.folders_only !== true && this.item.allow_multiple === false
      },
      show_controls() {
        return !this.builder__is_prompt_mode || this.is_active
      },
      show_description() {
        return this.show_controls && this.description.length > 0
      },
      show_summary() {
        return this.builder__is_prompt_mode && this.is_before_active
      },
      title() {
        var def_title = ''
        var item = this.item

        if (item.folders_only === true) {
          def_title = item.allow_multiple !== false ? 'Choose folders' : 'Choose a folder'
        } else {
          def_title = item.allow_multiple !== false ? 'Choose files' : 'Choose a file'
        }

        return _.get(this.item, 'title', def_title)
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      prompt() {
        var prompt_id = _.get(this.item, 'id', '')
        return _.find(this.prompts, { id: prompt_id }, {})
      },
      folder_path() {
        if (this.is_single_folder_select || this.is_single_file_select)
          return _.get(this.prompt, 'files[0].path', '')

        return ''
      },
      ceid() {
        return _.get(this.prompt, 'connection_eid', null)
      },
      connections() {
        return this.getAllConnections()
      },
      store_connection() {
        return _.find(this.connections, { eid: this.ceid }, null)
      },
      chooser_options() {
        var opts = _.pick(this.item, ['allow_multiple', 'allow_folders', 'folders_only', 'filetype_filter'])
        return _.mapKeys(opts, (val, key) => { return _.kebabCase(key) })
      }
    },
    methods: {
      ...mapGetters([
        'getAllConnections'
      ]),
      updateFolder(path) {
        if (this.item.folders_only !== true)
          return

        var afterFirst = function(str, char, cnt) {
          if (!_.isNumber(cnt)) { cnt = 1 }
          var retval = str.substr(str.indexOf('/') + 1)
          return cnt <= 1 ? retval : afterFirst(retval, char, cnt-1)
        }

        var name = path.substr(path.lastIndexOf('/') + 1)
        var remote_path = '/' + afterFirst(path, '/', 2)

        var folder = {
          name,
          path,
          remote_path,
          size: null,
          modified: '',
          type: VFS_TYPE_DIR
        }

        this.$store.commit('builder/UPDATE_ACTIVE_ITEM', { files: [ folder ] })
        this.is_changed = true
      },
      updateFiles(files, path) {
        if (this.is_single_folder_select && !_.isNil(path)) {
          return
        }

        var store_files = _.map(files, (f) => {
          return _.omit(f, ['is_selected'])
        })

        this.$store.commit('builder/UPDATE_ACTIVE_ITEM', { files: store_files })
        this.is_changed = true
      }
    }
  }
</script>
