<template>
  <div>
    <div v-if="isEditing">
      <el-form
        class="el-form--cozy el-form__label-tiny"
        label-position="top"
        :model="edit_pipe"
      >
        <el-form-item
          key="title"
          prop="title"
          label="Title"
        >
          <el-input
            placeholder="Enter title"
            auto-complete="off"
            spellcheck="false"
            :autofocus="true"
            v-model="edit_pipe.title"
          />
        </el-form-item>
        <el-form-item
          class="w-100"
          key="description"
          prop="description"
          label="Description"
        >
          <CodeEditor
            class="bg-white"
            style="line-height: 1.15; font-size: 13px; border: 1px solid #dcdfe6"
            lang="markdown"
            :options="{
              lineNumbers: false,
              minRows: 4,
              maxRows: 20
            }"
            v-model="description"
          />
        </el-form-item>
      </el-form>
      <div class="mt4 w-100 flex flex-row justify-end">
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
    <div v-else>
      Markdown result...
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import CodeEditor from '@/components/CodeEditor'

  const getDefaultState = () => {
    return {
      // pipe values
      edit_pipe: {
        title: '',
        description: '',
        notes: '',
        params: [],
        examples: [],
      },
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
      pipeEid: {
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
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
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
