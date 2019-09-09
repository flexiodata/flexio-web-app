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
                style="line-height: normal; width: 25%"
                size="small"
                placeholder="Type"
                auto-complete="off"
                spellcheck="false"
                @input="onParamItemChange"
                v-model="params[index].type"
              />
              <el-input
                class="ml2"
                style="line-height: normal; width: 50%"
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
                Required
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
        <div
          v-html="html_description"
          v-if="edit_pipe.description.length > 0"
        ></div>
        <div v-else>
          <el-button
            type="text"
            style="padding: 0; border: 0; font-weight: 600"
            @click="$emit('edit-click', 'description')"
          >
            Add a description
          </el-button>
          &mdash;
          <em>Styling with Markdown is supported</em>
        </div>
        <h3>Syntax</h3>
        <p><code>{{syntax_str}}</code></p>
        <h3>Sample Usage</h3>
        <div v-html="html_examples"></div>
        <h3>Parameters</h3>
        <div v-html="html_params"></div>
        <h3>Notes</h3>
        <div
          v-if="edit_pipe.notes.length > 0"
          v-html="html_notes"
        ></div>
        <div v-else>
          <el-button
            type="text"
            style="padding: 0; border: 0; font-weight: 600"
            @click="$emit('edit-click', 'notes')"
          >
            Add notes
          </el-button>
          &mdash;
          <em>Styling with Markdown is supported</em>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState } from 'vuex'
  import { getSyntaxStr } from '@/utils/pipe'
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
      syntax_str() {
        return getSyntaxStr(this.active_team_name, this.pipe.name, this.params_syntax_str)
      },
      html_description() {
        return marked(this.edit_pipe.description)
      },
      html_notes() {
        return marked(this.edit_pipe.notes)
      },
      html_params() {
        var markdown = ''
        _.each(this.edit_pipe.params, p => {
          markdown += '* `' + p.name + '`: ' + p.description + (!p.required ? ' (optional)' : '') + '\n'
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
        _.assign(this.$data, getDefaultState(this))

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
