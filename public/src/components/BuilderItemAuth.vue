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
    <ConnectionEditPanel
      :active-step="'authentication'"
      :connection.sync="edit_connection"
      :show-header="false"
      :show-footer="false"
    />
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
  import { OBJECT_TYPE_CONNECTION } from '@/constants/object-type'
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = () => {
    return {
      edit_connection: {},
      edit_values: {},
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
      ConnectionEditPanel,
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
      edit_connection: {
        handler: 'updateEditValues',
        immediate: true,
        deep: true
      },
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
    },
    methods: {
      initSelf() {
        var connection_attrs = _.get(this.item, 'connection', {})
        var edit_values = _.assign({}, this.form_values, this.defaultValues)
        var edit_connection = _.assign({}, connection_attrs, { eid_type: OBJECT_TYPE_CONNECTION })

        // reset our local component data
        _.assign(this.$data, getDefaultState(), { edit_values, edit_connection })
      },
      emitFormValues() {
        this.$emit('values-change', this.edit_values, this.item)
      },
      updateEditValues() {
        if (this.form_items.length == 1) {
          var key = _.get(this.form_items, '[0].name', '')
          if (key.length > 0) {
            var new_values = {}
            new_values[key] = _.cloneDeep(this.edit_connection)
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
