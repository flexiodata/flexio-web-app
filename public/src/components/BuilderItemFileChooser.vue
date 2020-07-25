<template>
  <div
    :class="item.class"
    v-show="visible"
  >
    <div
      class="tl pb3"
      v-show="title.length > 0"
    >
      <h3 class="fw6 f3 ma0">{{title}}</h3>
    </div>
    <div
      class="pb3 markdown"
      v-html="description"
      v-show="description.length > 0"
    >
    </div>
    <FileChooser
      :connection="connection"
      :selected-items.sync="selected_files"
      v-if="has_connection"
    />
    <div
      v-else
    >
      <em>Invalid connection</em>
    </div>
    <ButtonBar
      class="mt4"
      @cancel-click="onCancelClick"
      @submit-click="onSubmitClick"
      v-bind="$attrs"
      v-show="showFooter"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import FileChooser from '@/components/FileChooser'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = () => {
    return {
      edit_values: {},
      selected_files: [],
    }
  }

  // make sure 'gfm' and 'breaks' are both set to true
  // to have Markdown render the same as it does on GitHub
  marked.setOptions({
    gfm: true,
    breaks: true,
    pedantic: false,
    sanitize: false,
    smartLists: true,
    smartypants: true
  })

  export default {
    inheritAttrs: false,
    props: {
      item: {
        type: Object,
        required: true
      },
      visible: {
        type: Boolean,
        default: true
      },
      defaultValues: {
        type: Object,
        default: () => {}
      },
      showFooter: {
        type: Boolean,
        default: true
      }
    },
    components: {
      FileChooser,
      ButtonBar
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_values: {
        handler: 'emitFormValues',
        immediate: true,
        deep: true
      },
      selected_files: {
        handler: 'updateEditValues',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        var desc = _.get(this.item, 'description', '')
        return _.isString(desc) && desc.length > 0 ? marked(desc) : ''
      },
      form_items() {
        return _.get(this.item, 'form_items', [])
      },
      form_values() {
        var obj = {}
        _.each(this.form_items, item => {
          var val = item.value
          val = _.isArray(val) || _.isObject(val) ? _.cloneDeep(val) : val
          obj[item.name] = val
        })
        return obj
      },
      connection() {
        // this file chooser builder item *MUST* come after the builder item
        // that creates the connection
        var connection_key = _.get(this.item, 'connection_form_item_name', '')
        return _.find(this.defaultValues, (val, key) => {
          return key === connection_key
        })
      },
      has_connection() {
        return _.get(this.connection, 'eid', '').length > 0
      }
    },
    methods: {
      initSelf() {
        var edit_values = _.assign({}, this.form_values, this.defaultValues)

        // reset our local component data
        _.assign(this.$data, getDefaultState(), { edit_values })
      },
      getMarkdown(val) {
        return marked(val)
      },
      emitFormValues() {
        this.$emit('values-change', this.edit_values, this.item)
      },
      updateEditValues() {
        if (this.form_items.length == 1) {
          var key = _.get(this.form_items, '[0].name', '')
          if (key.length > 0) {
            var new_values = {}
            new_values[key] = _.map(this.selected_files, f => _.pick(f, ['id', 'name', 'path', 'full_path']))
            new_values[key] = _.compact(new_values[key])
            this.edit_values = _.assign({}, this.edit_values, new_values)
          }
        }
      },
      onCancelClick() {
        this.$emit('cancel-click', this.edit_values)
      },
      onSubmitClick() {
        this.$emit('submit-click', this.edit_values)
      },
    }
  }
</script>
