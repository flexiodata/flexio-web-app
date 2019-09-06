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
            v-model="edit_pipe.description"
          />
        </el-form-item>
        <el-form-item label="Sample Usage">
          <div
            class="mv2"
            :item="item"
            :index="index"
            :key="index"
            v-for="(item, index) in examples"
          >
            <div class="flex flex-row items-center">
              <el-input
                size="small"
                placeholder=", param1, [param2]"
                auto-complete="off"
                spellcheck="false"
                @input="onExampleItemChange"
                v-model="examples[index]"
              >
                <template slot="prepend">=FLEX("team/function"</template>
                <template slot="append">)</template>
              </el-input>
              <div
                class="ml2 pointer f3 black-30 hover-black-60"
                style="line-height: 1"
                :class="index >= examples.length-1 ? 'o-0 no-pointer-events' : ''"
                @click="removeExample(index)"

              >
                &times;
              </div>
            </div>
          </div>
        </el-form-item>
        <el-form-item
          key="notes"
          prop="notes"
          label="Notes"
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
            v-model="edit_pipe.notes"
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
      <div class="el-form__label-tiny cf">
        <label class="el-form-item__label">Preview</label>
      </div>
      <div
        class="markdown br2 pa4"
        style="border: 1px solid #dcdfe6"
      >
        <h2 v-if="edit_pipe.title.length > 0">{{edit_pipe.title}}</h2>
        <h2 v-else>{{edit_pipe.name}}</h2>
        <div v-html="html_description"></div>
        <h3>Notes</h3>
        <div v-html="html_notes"></div>
      </div>
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
      examples: []
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
      pipe: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
      html_description() {
        return marked(this.edit_pipe.description)
      },
      html_notes() {
        return marked(this.edit_pipe.notes)
      },
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState(this))

        // reset local objects
        this.edit_pipe = _.assign({}, this.edit_pipe, _.cloneDeep(this.pipe))
        this.examples = [].concat(this.edit_pipe.examples).concat('')

        this.$emit('update:isEditing', false)
      },
      onExampleItemChange(new_arr, old_arr) {
        var arr = this.examples
        if (arr.length > 0 && arr[arr.length-1].length > 0) {
          this.examples = [].concat(arr).concat('')
        }
      },
      removeExample(index) {
        var examples = _.cloneDeep(this.examples)
        _.pullAt(examples, [index])
        this.examples = examples
      },
      onSaveClick() {
        var edit_attrs = _.pick(this.edit_pipe, ['title', 'description', 'notes', 'params', 'examples'])
        edit_attrs.examples = this.examples
        edit_attrs.examples.pop()
        this.$emit('save-click', edit_attrs, this.pipe)
      },
    }
  }
</script>
