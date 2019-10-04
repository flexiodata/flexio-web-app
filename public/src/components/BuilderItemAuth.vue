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
  import ConnectionEditPanel from '@/components/ConnectionEditPanel'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = () => {
    return {
      edit_connection: {},
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
      edit_connection: {
        handler: 'emitConnectionValues',
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
    },
    methods: {
      initSelf() {
        var connection_attrs = _.get(this.item, 'connection', {})
        var edit_connection = _.assign({}, connection_attrs, this.defaultValues)

        // reset our local component data
        _.assign(this.$data, getDefaultState(), { edit_connection })
      },
      emitConnectionValues() {
        this.$emit('values-change', this.edit_connection, this.item)
      },
      onCancelClick() {
        this.$emit('cancel-click', this.edit_connection)
      },
      onSubmitClick() {
        this.$emit('submit-click', this.edit_connection)
      },
    }
  }
</script>
