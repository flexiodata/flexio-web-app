<template>
  <div>
    <el-form
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      inline
      :model="$data"
      @submit.prevent.native
    >
<!-- DEPRECATED: remote option; only allow inline
      <el-form-item
        key="remote_state"
        prop="remote_state"
        label="Would you like to execute an inline script or a remote script?"
      >
        <el-radio-group v-model="remote_state">
          <el-radio-button
            :label="option.val"
            :key="option.val"
            v-for="option in remote_options"
          >
            {{option.label}}
          </el-radio-button>
        </el-radio-group>
      </el-form-item>
 -->
      <el-form-item
        key="lang"
        prop="lang"
        label="Type"
      >
        <el-select v-model="lang">
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in lang_options"
          />
        </el-select>
      </el-form-item>
<!-- DEPRECATED: remote option; only allow inline
      <el-form-item
        class="w-100"
        style="margin-bottom: 0"
        key="path"
        prop="path"
        label="Remote script path or URL"
        v-show="remote_state == 'remote'"
      >
        <el-input
          auto-complete="off"
          spellcheck="false"
          placeholder="Enter path or URL"
          :autofocus="true"
          v-model="path"
        >
          <BrowseButton
            slot="append"
            class="ttu fw6"
            :file-chooser-options="{
              filetypeFilter: ['py','js']
            }"
            @paths-selected="onPathsSelected"
          >
            Browse
          </BrowseButton>
        </el-input>
      </el-form-item>
 -->
      <el-form-item
        class="w-100"
        style="margin-bottom: 0"
        key="code"
        prop="code"
        label="Content"
        v-show="remote_state == 'inline'"
      >
        <CodeEditor
          class="bg-white"
          style="line-height: 1.15; font-size: 13px; border: 1px solid #dcdfe6"
          transpose="base64"
          :lang="code_editor_lang"
          :options="{ minRows: 8, maxRows: 32 }"
          v-model="code"
        />
      </el-form-item>
    </el-form>
    <ButtonBar
      class="mt4"
      :submit-button-disabled="!isSaveAllowed"
      :submit-button-text="'Save Changes'"
      @cancel-click="onCancelClick"
      @submit-click="onSaveClick"
      v-show="isEditing"
    />
  </div>
</template>

<script>
  import { TASK_OP_EXECUTE } from '@/constants/task-op'
  import { btoaUnicode } from '@/utils'
  import CodeEditor from '@/components/CodeEditor'
  import ButtonBar from '@/components/ButtonBar'
  import BrowseButton from '@/components/BrowseButton'

  const code_python = btoaUnicode(`# basic hello world example
def flex_handler(flex):
    flex.end([["H","e","l","l","o"],["W","o","r","l","d"]])
`)

  const code_javascript = btoaUnicode(`// basic hello world example
exports.flex_handler = function(flex) {
  flex.end([["H","e","l","l","o"],["W","o","r","l","d"]])
}
`)

  const code_json = btoaUnicode(`[
    ["H","e","l","l","o"],
    ["W","o","r","l","d"]
]
`)

  const code_csv = btoaUnicode(``)

  const remote_options = [
    { label: 'Inline script', val: 'inline' },
    { label: 'Remote script', val: 'remote' }
  ]

  const lang_options = [
    { label: 'Python',  val: 'python' },
    { label: 'Node.js', val: 'nodejs' },
    { label: 'JSON', val: 'json' },
    { label: 'CSV', val: 'csv' }
  ]

  const getDefaultState = () => {
    return {
      code_python,
      code_javascript,
      code_json,
      code_csv,
      lang_options,
      remote_options,
      remote_state: 'inline',

      // task values
      op: 'execute',
      lang: 'python',
      path: '',
      code: '',
    }
  }

  export default {
    props: {
      task: {
        type: Object,
        required: true
      },
      isEditing: {
        type: Boolean,
        required: true
      },
      isSaveAllowed: {
        type: Boolean,
        required: true
      }
    },
    components: {
      CodeEditor,
      ButtonBar,
      BrowseButton
    },
    watch: {
      task: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      is_changed: {
        handler: 'onChanged',
        immediate: true
      },
      lang: {
        handler: 'onLangChange'
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      code_editor_lang() {
        switch (this.lang) {
          case 'python': return 'python'
          case 'nodejs': return 'javascript'
          case 'json': return 'json'
          case 'csv': return 'csv'
        }

        return 'python'
      },
      initial_remote_state() {
        // DEPRECATED: remote option; only allow inline; old logic is here for
        // reference, particularly since some old jobs may have the path info set
        //return _.get(this.task, 'path', '').length == 0 ? 'inline' : 'remote'
        return 'inline'
      },
      is_changed() {
        if (this.remote_state != this.initial_remote_state) { return true }
        if (this.code != _.get(this.task, 'code', '')) { return true }
        if (this.path != _.get(this.task, 'path', '')) { return true }
        if (this.lang != this.task.lang) { return true }
        return false
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState(), this.task)

        // set our internal remote state
        this.remote_state = this.initial_remote_state

        // overwrite internal code based on selected language
        var code = this.getCodeByLang(this.lang)
        switch (this.lang) {
          case 'python': this.code_python = code; break
          case 'nodejs': this.code_javascript = code; break
          case 'json': this.code_json = code; break
          case 'csv': this.code_csv = code; break
        }

        this.$emit('update:isEditing', false)
        this.$emit('update:isSaveAllowed', true)
      },
      getCodeByLang(lang) {
        switch (lang) {
          case 'python': return this.code_python
          case 'nodejs': return this.code_javascript
          case 'json': return this.code_json
          case 'csv': return this.code_csv
        }

        return ''
      },
      onPathsSelected(path) {
        this.path = path
      },
      onLangChange(val) {
        this.code = this.getCodeByLang(val)
      },
      onChanged() {
        this.$emit('update:isEditing', this.is_changed)
      },
      onCancelClick() {
        this.$emit('update:isEditing', false)
        this.initSelf()
      },
      onSaveClick() {
        var is_inline = this.remote_state == 'inline'
        var new_task = {
          op: TASK_OP_EXECUTE,
          lang: this.lang,
          path: is_inline ? undefined : this.path,
          code: is_inline ? this.code : undefined
        }

        this.$emit('save-click', new_task, this.task)
      }
    }
  }
</script>
