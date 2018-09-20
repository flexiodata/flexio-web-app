<template>
  <div>
    <template v-if="showResult">
      <div class="mb2 bt b--black-10"></div>
      <table class="w-100">
        <tbody>
          <FileChooserItem
            :item="file"
            :index="file_index"
            v-for="(file, file_index) in files"
          />
        </tbody>
      </table>
      <div class="mt2 bt b--black-10"></div>
    </template>
    <div v-show="!showResult">
      <FileChooser
        class="bb b--light-gray"
        style="max-height: 24rem"
        :connection="store_connection"
        @open-folder="updateFolder"
        @selection-change="selectionChange"
        v-bind="$attrs"
        v-if="store_connection"
      />
      <div class="flex flex-row items-center mt2 f7" v-if="is_single_folder_select || is_single_file_select">
        <div class="mr2" v-if="is_single_folder_select">Folder:</div>
        <div class="mr2" v-else-if="is_single_file_select">File name:</div>
        <div class="flex-fill ba b--black-10 silver bg-near-white cursor-not-allowed" style="padding: 7px">
          <span v-if="folder_path.length > 0">{{folder_path}}</span><span v-else>(None selected)</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import util from '../utils'
  import FileChooser from './FileChooser.vue'
  import FileChooserItem from './FileChooserItem.vue'
  import MixinConnection from './mixins/connection'

  const VFS_TYPE_DIR = 'DIR'

  export default {
    inheritAttrs: false,
    props: {
      connectionIdentifier: {
        type: String
      },
      showResult: {
        type: Boolean,
        default: false
      },
      allowMultiple: {
        type: Boolean
      },
      allowFolders: {
        type: Boolean
      },
      foldersOnly: {
        type: Boolean
      },
      filetypeFilter: {
        type: String
      }
    },
    mixins: [MixinConnection],
    components: {
      FileChooser,
      FileChooserItem
    },
    data() {
      return {
        files: []
      }
    },
    computed: {
      store_connection() {
        return this.$_Connection_getConnectionByIdentifier(this.connectionIdentifier)
      },
      is_single_folder_select() {
        return this.foldersOnly === true && this.allowMultiple === false
      },
      is_single_file_select() {
        return this.foldersOnly !== true && this.allowMultiple === false
      },
      folder_path() {
        if (this.is_single_folder_select || this.is_single_file_select) {
          return _.get(this.files, '[0].path', '')
        }
        return ''
      }
    },
    methods: {
      updateFolder(path) {
        if (this.foldersOnly !== true) {
          return
        }

        var name = path.substr(path.lastIndexOf('/') + 1)
        var remote_path = '/' + util.afterNth(path, '/', 2)

        var folder = {
          name,
          path,
          remote_path,
          size: null,
          modified: '',
          type: VFS_TYPE_DIR
        }

        var files = [folder]
        this.files = files
        this.$emit('open-folder', files, path)
      },
      selectionChange(files, path) {
        if (this.is_single_folder_select && !_.isNil(path)) {
          return
        }

        var files = _.map(files, (f) => {
          return _.omit(f, ['is_selected'])
        })

        this.files = files
        this.$emit('selection-change', files, path)
      },
      getSelectedFiles() {
        return this.files
      }
    }
  }
</script>
