<template>
  <div>
    <el-form
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      inline
      :model="$data"
    >
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
      <el-form-item
        key="lang"
        prop="lang"
        label="Language"
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
      <el-form-item
        class="w-100"
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
            :fileChooserOptions="{
              filetypeFilter: ['py','js']
            }"
            @paths-selected="onPathsSelected"
          >
            Browse
          </BrowseButton>
        </el-input>
      </el-form-item>
      <el-form-item
        class="w-100"
        key="code"
        prop="code"
        label="Code"
        v-show="remote_state == 'inline'"
      >
        <CodeEditor
          class="bg-white ba b--black-10"
          style="line-height: 1.15; font-size: 13px"
          transpose="base64"
          :lang="code_editor_lang"
          :options="{ minRows: 8, maxRows: 32 }"
          v-model="code"
        />
      </el-form-item>
    </el-form>

    <div
      class="flex-none mt3 flex flex-row justify-end"
      v-show="is_changed"
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
        :disabled="!isSaveAllowed"
        @click="onSaveClick"
      >
        Save Changes
      </el-button>
    </div>
  </div>
</template>

<script>
  import { btoaUnicode } from '@/utils'
  import CodeEditor from '@/components/CodeEditor'
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

  const remote_options = [
    { label: 'Inline script', val: 'inline' },
    { label: 'Remote script', val: 'remote' }
  ]

  const lang_options = [
    { label: 'Python',  val: 'python' },
    { label: 'Node.js', val: 'nodejs' }
  ]

  const getDefaultState = () => {
    return {
      remote_options,
      lang_options,
      code_python,
      code_javascript,
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
      BrowseButton
    },
    watch: {
      task: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      is_changed: {
        handler: 'updateIsEditing',
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
        }

        return 'python'
      },
      intial_remote_state() {
        return _.get(this.task, 'path', '').length == 0 ? 'inline' : 'remote'
      },
      is_changed() {
        if (this.remote_state != this.intial_remote_state) { return true }
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
        this.remote_state = this.intial_remote_state

        // initialize code
        if (this.code.length == 0) {
          this.code = this.getCodeByLang(this.lang)
        }

        // overwrite internal code based on selected language
        switch (this.lang) {
          case 'python': this.code_python = this.code; break
          case 'nodejs': this.code_javascript = this.code; break
        }

        this.$emit('update:isEditing', false)
        this.$emit('update:isSaveAllowed', true)
      },
      getCodeByLang(lang) {
        switch (lang) {
          case 'python': return this.code_python
          case 'nodejs': return this.code_javascript
        }

        return ''
      },
      updateIsEditing() {
        this.$emit('update:isEditing', this.is_changed)
      },
      onPathsSelected(path) {
        this.path = path
      },
      onLangChange(val) {
        this.code = this.getCodeByLang(val)
      },
      onSaveClick() {
        var is_inline = this.remote_state == 'inline'
        var new_task = {
          op: this.op,
          lang: this.lang,
          path: is_inline ? undefined : this.path,
          code: is_inline ? this.code : undefined
        }

        this.$emit('save-click', new_task, this.task)
      }
    }
  }
</script>
