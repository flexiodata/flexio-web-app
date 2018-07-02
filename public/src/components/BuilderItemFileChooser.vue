<template>
  <div>
    <div class="tl pb3"
      v-if="showTitle"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <BuilderComponentFileChooser
      :connection-identifier="item.connection_eid"
      :show-result="is_before_active"
      @open-folder="updateFiles"
      @selection-change="updateFiles"
      v-bind="chooser_options"
      v-on="$listeners"
      v-show="is_active || is_before_active"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import BuilderComponentFileChooser from './BuilderComponentFileChooser.vue'

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
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: false
      },
      showTitle: {
        type: Boolean,
        default: true
      }
    },
    components: {
      BuilderComponentFileChooser
    },
    watch: {
      is_active: {
        handler: 'updateAllowNext',
        immediate: true
      },
      files: {
        handler: 'updateAllowNext',
        deep: true
      }
    },
    data() {
      return {
        files: [],
        is_changed: false
      }
    },
    computed: {
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      show_description() {
        return this.show_controls && this.description.length > 0
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
      folder_path() {
        if (this.is_single_folder_select || this.is_single_file_select) {
          return _.get(this.files, '[0].path', '')
        }

        return ''
      },
      chooser_options() {
        var opts = _.pick(this.item, ['allow_multiple', 'allow_folders', 'folders_only', 'filetype_filter'])
        return _.mapKeys(opts, (val, key) => { return _.kebabCase(key) })
      }
    },
    methods: {
      updateFiles(files, path) {
        this.files = files
        this.is_changed = true

        var key = _.get(this.item, 'variable', 'files')
        var form_values = {}
        form_values[key] = this.files
        this.$emit('item-change', form_values, this.index)
      },
      updateAllowNext() {
        var allow = this.files.length > 0
        this.$emit('update:isNextAllowed', allow)
      }
    }
  }
</script>
