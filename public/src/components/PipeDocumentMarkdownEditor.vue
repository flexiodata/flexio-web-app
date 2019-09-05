<template>
  <div>
    <CodeEditor
      class="bg-white ba b--black-10"
      style="line-height: 1.15; font-size: 13px"
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
      class="marked"
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

  export default {
    props: {
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
      onSaveClick() {
        var save_obj = {
          new_value: this.edit_value,
          old_value: this.value,
          compiled_html: this.compiled_html
        }

        this.$emit('save-click', save_obj)
      },
    }
  }
</script>
