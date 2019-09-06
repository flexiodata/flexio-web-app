<template>
  <div>
    <div class="flex flex-row items-center">
      <div class="flex-fill markdown">
        <slot name="title">Title</slot>
      </div>
      <div class="flex-none">
        <el-button
          style="padding: 0"
          type="text"
          @click="$emit('update:isEditing', true)"
          v-show="!isEditing"
          v-require-rights:pipe.update
        >
          Edit
        </el-button>
      </div>
    </div>

    <CodeEditor
      ref="code-editor"
      class="bg-white"
      style="line-height: 1.15; font-size: 13px; border: 1px solid #dcdfe6"
      lang="markdown"
      :options="{
        lineNumbers: false,
        minRows: 24,
        maxRows: 24,
      }"
      v-model="edit_value"
      v-if="isEditing"
    />
    <div
      class="markdown"
      v-html="compiled_html"
      v-else-if="edit_value.length > 0"
    >
    </div>
    <div
      class="f6 fw4 mt1 lh-copy moon-gray"
      v-else
    >
      <slot name="empty"><em>(No value)</em></slot>
    </div>
    <div
      class="flex-none mt3 flex flex-row justify-end"
      v-show="isEditing"
    >
      <el-button
        class="ttu fw6"
        @click="initSelf"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
        type="primary"
        @click="onSaveClick"
      >
        Save Changes
      </el-button>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import CodeEditor from '@/components/CodeEditor'

  const getDefaultState = () => {
    return {
      edit_value: '',
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
    props: {
      name: {
        type: String,
        required: true
      },
      value: {
        type: String,
        required: true
      },
      isEditing: {
        type: Boolean,
        required: true
      }
    },
    components: {
      CodeEditor,
    },
    watch: {
      value: {
        handler: 'initSelf',
        immediate: true
      },
      isEditing: {
        handler: 'onEditingChange'
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      compiled_html() {
        return marked(this.edit_value)
      },
      is_changed() {
        return this.edit_value != this.value
      },
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState(), { edit_value: this.value })

        this.$emit('update:isEditing', false)
      },
      onEditingChange(is_editing, was_editing) {
        if (is_editing && !was_editing) {
          this.$nextTick(() => {
            var editor = this.$refs['code-editor']
            if (editor) {
              editor.focus()
            }
          })
        }
      },
      onSaveClick() {
        var name = this.name
        var new_value = this.edit_value
        var old_value = this.value

        this.$emit('save-click', name, new_value, old_value)
      },
    }
  }
</script>
