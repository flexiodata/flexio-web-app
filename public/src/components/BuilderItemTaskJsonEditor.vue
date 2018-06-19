<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <CodeEditor
      class="bg-white ba b--black-10 overflow-y-auto"
      lang="javascript"
      :options="{ minRows: 12, maxRows: 30 }"
      v-model="edit_json"
    />
    <transition name="el-zoom-in-top">
      <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="json_parse_error.length > 0">Parse error: {{json_parse_error}}</div>
    </transition>
  </div>
</template>

<script>
  import marked from 'marked'
  import CodeEditor from './CodeEditor.vue'

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
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      is_changed: {
        handler: 'onChange'
      },
      edit_json: {
        handler: 'onEditJsonChange'
      },
      'item.value': {
        handler: 'resetSelf',
        deep: true
      }
    },
    data() {
      return {
        edit_json: this.item.value,
        orig_json: this.item.value,
        json_parse_error: '',
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Task JSON')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      is_changed() {
        return this.edit_json != this.orig_json
      }
    },
    methods: {
      resetSelf() {
        // reset the form
        this.edit_json = this.item.value
        this.orig_json = this.item.value
        this.json_parse_error = ''
      },
      onChange(val) {
        if (val === true) {
          this.$emit('active-item-change', this.index)
        }
      },
      onEditJsonChange() {
        try {
          var task = JSON.parse(this.edit_json)
          this.$emit('item-change', task, this.index)
          this.json_parse_error = ''
        }
        catch(e)
        {
          this.json_parse_error = e.message
        }
      }
    }
  }
</script>
