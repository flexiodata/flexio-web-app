<template>
  <div>
    <div
      class="tl pb3"
      v-show="title.length > 0"
    >
      <h3 class="fw6 f3 ma0">{{title}}</h3>
    </div>
    <div
      class="pb3 markdown"
      v-html="description"
      v-show="description.length > 0"
    >
    </div>
    <el-form
      ref="form"
      :class="item.class"
      :model="edit_values"
      :label-position="label_position"
      :label-width="label_width"
    >
      <el-form-item
        :class="fi.class"
        :label="fi.label"
        :label-width="fi.element == 'markdown' ? (fi.label_width || '0') : fi.label_width"
        :key="fi.name"
        :prop="fi.name"
        v-show="fi.type !== 'hidden'"
        v-for="fi in form_items"
      >
        <div
          v-html="getMarkdown(fi.value)"
          v-if="fi.element == 'markdown'"
        ></div>
        <CodeEditor
          class="bg-white"
          style="line-height: 1.15; font-size: 13px; padding: 4px; border: 1px solid #dcdfe6; border-radius: 4px"
          :lang="fi.lang"
          :transpose="fi.transpose || 'none'"
          :options="{ minRows: 8, maxRows: 20, lineNumbers: false }"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'code-editor'"
        />
        <el-switch
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'switch'"
        />
        <el-checkbox
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'checkbox'"
        />
        <el-checkbox-group
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'checkbox-group'"
        >
          <el-checkbox
            :label="option.value"
            :key="option.value"
            :name="fi.name"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-checkbox>
        </el-checkbox-group>
        <el-radio-group
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'radio-group'"
        >
          <el-radio
            :label="option.value"
            :key="option.value"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-radio>
        </el-radio-group>
        <el-select
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'select'"
        >
          <el-option
            :label="option.label"
            :value="option.value"
            :key="option.value"
            v-for="option in fi.options"
          />
        </el-select>
        <el-date-picker
          format="MM/dd/yyyy"
          :type="fi.type"
          :editable="false"
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && isDatePickerType(fi.type)"
        />
        <el-input
          type="textarea"
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && fi.type == 'textarea'"
        />
        <el-input
          type="number"
          :placeholder="fi.placeholder"
          v-model.number="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && fi.type == 'number'"
        />
        <el-input
          type="hidden"
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && fi.type == 'hidden'"
        />
        <el-input
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else
        />
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import CodeEditor from '@/components/CodeEditor'

  const getDefaultState = () => {
    return {
      edit_values: {},
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
      item: {
        type: Object,
        required: true
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      label_position() {
        return _.get(this.item, 'label_position', 'top')
      },
      label_width() {
        return _.get(this.item, 'label_width', '10rem')
      },
      form_items() {
        return _.get(this.item, 'form_items', [])
      },
      form_values() {
        var obj = {}
        _.each(this.form_items, item => {
          var val = item.value
          val = _.isArray(val) || _.isObject(val) ? _.cloneDeep(val) : val
          obj[item.name] = val
        })
        return obj
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        var edit_values = this.form_values
        _.assign(this.$data, getDefaultState(), { edit_values })

        this.autoFocus()
      },
      autoFocus() {
        this.$nextTick(() => {
          var form = this.$refs['form']
          if (form) {
            var first_comp = _.get(form, '$children[0].$children[0]', null)
            if (first_comp && first_comp.focus) {
              first_comp.focus()
            }
          }
        })
      },
      isDatePickerType(type) {
        return ['year','month','date','datetime','week','datetimerange','daterange'].indexOf(type) != -1
      },
      getMarkdown(val) {
        return marked(val)
      },
    }
  }
</script>
