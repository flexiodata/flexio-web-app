<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <ConnectionInfoPanel
      :connection-info.sync="edit_values"
      :form-errors.sync="form_errors"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import ConnectionInfoPanel from './ConnectionInfoPanel.vue'

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
        required: true
      }
    },
    components: {
      ConnectionInfoPanel
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_values: {
        handler: 'onEditValuesChange',
        immediate: true,
        deep: true
      },
      is_changed: {
        handler: 'onChange'
      },
      form_errors(val) {
        this.$emit('update:isNextAllowed', _.keys(val).length == 0)
      }
    },
    data() {
      return {
        orig_values: {},
        edit_values: {},
        form_errors: {}
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Read')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      is_changed() {
        return !_.isEqual(this.edit_values, this.orig_values)
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})

        // TODO: remove this at some point when the backend returns an array
        if (_.isArray(form_values.data)) {
          form_values.data = {}
        }
        if (_.isArray(form_values.headers)) {
          form_values.headers = {}
        }

        this.orig_values = _.cloneDeep(form_values)
        this.edit_values = _.cloneDeep(form_values)
        this.$emit('update:isNextAllowed', true)
      },
      onChange(val) {
        if (val) {
          this.$emit('active-item-change', this.index)
        }
      },
      onEditValuesChange() {
        this.$emit('item-change', this.edit_values, this.index)
      }
    }
  }
</script>
