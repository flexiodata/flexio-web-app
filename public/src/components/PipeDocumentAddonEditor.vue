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
            v-model="edit_pipe.title"
          />
        </el-form-item>
        <el-form-item
          key="description"
          prop="description"
        >
          <div class="flex flex-row items-center el-form__label-tiny cf">
            <label class="el-form-item__label">Description</label>
            <div class="flex-fill"></div>
            <a
              href="https://guides.github.com/features/mastering-markdown/"
              class="flex flex-row items-center link blue underline-hover"
              style="line-height: 1.5; font-size: 12px; outline: none"
              title="Visit GitHub to learn Markdown"
              target="_blank"
            >
              <svg
                class="octicon octicon-markdown"
                style="margin-right: 3px"
                viewBox="0 0 16 16"
                version="1.1"
                width="16"
                height="16"
                aria-hidden="true"
              >
                <path fill-rule="evenodd" d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"></path>
              </svg>
              <span>Styling with Markdown is supported</span>
            </a>
          </div>
          <CodeEditor
            class="bg-white"
            style="line-height: 1.15; font-size: 13px; padding: 4px; border: 1px solid #dcdfe6; border-radius: 4px"
            placeholder="Add a description"
            lang="markdown"
            :options="{
              lineNumbers: false,
              minRows: 4,
              maxRows: 20
            }"
            v-model="edit_pipe.description"
          />
        </el-form-item>
        <el-form-item label="Parameters">
          <div
            class="mv2"
            :item="item"
            :index="index"
            :key="index"
            v-for="(item, index) in params"
          >
            <div class="flex flex-row items-center">
              <el-input
                size="small"
                style="line-height: normal; width: 25%"
                placeholder="Property"
                auto-complete="off"
                spellcheck="false"
                @input="onParamItemChange"
                v-model="params[index].name"
              />
              <el-input
                class="ml2"
                style="line-height: normal; width: 20%"
                size="small"
                placeholder="Type"
                auto-complete="off"
                spellcheck="false"
                @input="onParamItemChange"
                v-model="params[index].type"
              />
              <el-input
                class="ml2"
                style="line-height: normal; width: 55%"
                size="small"
                placeholder="Description"
                auto-complete="off"
                @input="onParamItemChange"
                v-model="params[index].description"
              />
              <el-checkbox
                class="ml2"
                style="line-height: normal"
                v-model="params[index].required"
              >
                <span style="font-size: 13px">Required</span>
              </el-checkbox>
              <div
                class="ml3 pointer f3 black-30 hover-black-60"
                style="line-height: 1"
                :class="index >= params.length-1 ? 'o-0 no-pointer-events' : ''"
                @click="removeParam(index)"

              >
                &times;
              </div>
            </div>
          </div>
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
                auto-complete="off"
                spellcheck="false"
                :placeholder="params_syntax_str"
                @input="onExampleItemChange"
                v-model="examples[index]"
              >
                <template slot="prepend">{{syntax_prefix_str}}{{examples[index].trim().length > 0 ? ',' : '&nbsp;'}}</template>
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
        >
          <div class="flex flex-row items-center el-form__label-tiny cf">
            <label class="el-form-item__label">Notes</label>
            <div class="flex-fill"></div>
            <a
              href="https://guides.github.com/features/mastering-markdown/"
              class="flex flex-row items-center link blue underline-hover"
              style="line-height: 1.5; font-size: 12px; outline: none"
              title="Visit GitHub to learn Markdown"
              target="_blank"
            >
              <svg
                class="octicon octicon-markdown"
                style="margin-right: 3px"
                viewBox="0 0 16 16"
                version="1.1"
                width="16"
                height="16"
                aria-hidden="true"
              >
                <path fill-rule="evenodd" d="M14.85 3H1.15C.52 3 0 3.52 0 4.15v7.69C0 12.48.52 13 1.15 13h13.69c.64 0 1.15-.52 1.15-1.15v-7.7C16 3.52 15.48 3 14.85 3zM9 11H7V8L5.5 9.92 4 8v3H2V5h2l1.5 2L7 5h2v6zm2.99.5L9.5 8H11V5h2v3h1.5l-2.51 3.5z"></path>
              </svg>
              <span>Styling with Markdown is supported</span>
            </a>
          </div>
          <CodeEditor
            class="bg-white"
            style="line-height: 1.15; font-size: 13px; padding: 4px; border: 1px solid #dcdfe6; border-radius: 4px"
            placeholder="Add additional notes"
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
      <ButtonBar
        class="mt4"
        :submit-button-text="'Save Changes'"
        @cancel-click="initSelf"
        @submit-click="onSaveClick"
      />
    </div>
    <div v-else>
      <div class="el-form__label-tiny cf">
        <label class="el-form-item__label">Preview</label>
      </div>
      <div
        class="markdown br2 pa4"
        style="border: 1px solid #dcdfe6"
      >
        <h2>{{addon_title}}</h2>
        <div v-html="html_description"></div>
        <h3>Syntax</h3>
        <p><code>{{syntax_str}}</code></p>
        <h3>Parameters</h3>
        <div v-html="html_params"></div>
        <h3>Sample Usage</h3>
        <div v-html="html_examples"></div>
        <h3>Notes</h3>
        <div v-html="html_notes"></div>
      </div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState } from 'vuex'
  import { getSyntaxStr } from '@/utils/pipe'
  import CodeEditor from '@/components/CodeEditor'
  import ButtonBar from '@/components/ButtonBar'

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
      params: [],
      examples: [],
    }
  }

  const newParam = (param) => {
    return _.assign({ name: '', type: '', description: '', required: true }, param)
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
      ButtonBar
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
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pipe() {
        return _.get(this.$store.state.pipes, `items.${this.pipeEid}`, {})
      },
      params_syntax_str() {
        var arr = [].concat(this.params)
        if (arr.length > 0) {
          arr.pop()
        }
        var param_names = _.map(arr, param => param.required === false ? '[' + param.name + ']' : param.name)
        return param_names.join(', ')
      },
      syntax_prefix_str() {
        return getSyntaxStr(this.active_team_name, this.pipe.name, '').slice(0, -1)
      },
      syntax_str() {
        return getSyntaxStr(this.active_team_name, this.pipe.name, this.params_syntax_str)
      },
      addon_title() {
        return this.edit_pipe.title.length > 0 ? this.edit_pipe.title : this.edit_pipe.name
      },
      html_description() {
        return marked(this.edit_pipe.description)
      },
      html_notes() {
        return marked(this.edit_pipe.notes)
      },
      html_params() {
        if (this.edit_pipe.params.length == 0) {
          return ''
        }

        var markdown = '' +
          'Property|Type|Description|Required\n' +
          '--------|----|-----------|--------\n'
        _.each(this.edit_pipe.params, p => {
          markdown += '`' + p.name + '`|' + p.type + '|' + p.description + '|' + (p.required ? 'true' : 'false') + '\n'
        })
        return marked(markdown)
      },
      html_examples() {
        var examples = _.map(this.edit_pipe.examples, example => getSyntaxStr(this.active_team_name, this.pipe.name, example) )
        var markdown = ''
        _.each(examples, example => {
          markdown += '`' + example + '`\n'
        })
        return marked(markdown)
      },
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState())

        // reset local objects
        this.edit_pipe = _.assign({}, this.edit_pipe, _.cloneDeep(this.pipe))
        this.params = [].concat(this.edit_pipe.params).concat(newParam())
        this.examples = [].concat(this.edit_pipe.examples).concat('')

        this.$emit('update:isEditing', false)
      },
      removeParam(index) {
        var params = _.cloneDeep(this.params)
        _.pullAt(params, [index])
        this.params = params
      },
      removeExample(index) {
        var examples = _.cloneDeep(this.examples)
        _.pullAt(examples, [index])
        this.examples = examples
      },
      onParamItemChange() {
        var arr = this.params
        if (arr.length > 0 && arr[arr.length-1].name.length > 0) {
          this.params = [].concat(arr).concat(newParam())
        }
      },
      onExampleItemChange() {
        var arr = this.examples
        if (arr.length > 0 && arr[arr.length-1].length > 0) {
          this.examples = [].concat(arr).concat('')
        }
      },
      onSaveClick() {
        var edit_attrs = _.pick(this.edit_pipe, ['title', 'description', 'notes', 'params', 'examples'])

        // remove ghost row param
        edit_attrs.params = this.params
        edit_attrs.params.pop()

        // don't allow parameters that don't have a name
        _.remove(edit_attrs.params, param => param.name.length == 0)

        // remove ghost row example
        edit_attrs.examples = this.examples
        edit_attrs.examples.pop()

        this.$emit('save-click', edit_attrs, this.pipe)
      },
    }
  }
</script>
