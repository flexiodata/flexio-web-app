<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <el-form
      ref="form"
      :class="item.cls"
      :model="flat_values"
      :label-position="label_position"
      :label-width="label_width"
      v-if="show_controls"
    >
      <el-form-item
        :class="fi.cls"
        :label="fi.label"
        :label-width="fi.element == 'markdown' ? (fi.label_width || '0') : fi.label_width"
        :key="getFlatKey(fi.variable)"
        :prop="getFlatKey(fi.variable)"
        v-show="fi.type !== 'hidden'"
        v-if="getShowFormItem(fi)"
        v-for="fi in form_items"
      >
        <div
          v-html="getMarkdown(fi.value)"
          v-if="fi.element == 'markdown'"
        ></div>
        <CodeEditor
          class="bg-white ba b--black-10"
          style="line-height: 1.15; font-size: 13px"
          :lang="fi.lang ? fi.lang : 'javascript'"
          :options="{ minRows: 8, maxRows: 20 }"
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'code-editor'"
        />
        <el-switch
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'switch'"
        />
        <el-checkbox
          :placeholder="fi.placeholder"
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'checkbox'"
        />
        <el-checkbox-group
          :placeholder="fi.placeholder"
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'checkbox-group'"
        >
          <el-checkbox
            :label="option.value"
            :key="option.value"
            :name="fi.variable"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-checkbox>
        </el-checkbox-group>
        <el-radio-group
          :placeholder="fi.placeholder"
          v-model="flat_values[getFlatKey(fi.variable)]"
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
          v-model="flat_values[getFlatKey(fi.variable)]"
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
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'input' && isDatePickerType(fi.type)"
        />
        <el-input
          type="textarea"
          :placeholder="fi.placeholder"
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'input' && fi.type == 'textarea'"
        />
        <el-input
          type="number"
          :placeholder="fi.placeholder"
          v-model.number="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'input' && fi.type == 'number'"
        />
        <el-input
          type="hidden"
          :placeholder="fi.placeholder"
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else-if="fi.element == 'input' && fi.type == 'hidden'"
        />
        <el-input
          :placeholder="fi.placeholder"
          v-model="flat_values[getFlatKey(fi.variable)]"
          v-else
        />
      </el-form-item>
    </el-form>

    <div v-else-if="show_summary">
      <div class="mb2 bt b--black-10"></div>
      <table>
        <tbody>
          <tr
            :key="key"
            v-for="(val, key) in form_values"
          >
            <td class="v-top pa1 f6"><span class="fw6">{{key}}:</span></td>
            <td class="v-top pa1 f6">{{ JSON.stringify(val, null, 2) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="mt2 bt b--black-10"></div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import CodeEditor from './CodeEditor.vue'

  const flatten = require('flat')
  const unflatten = require('flat').unflatten

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
      },
      isNextAllowed: {
        type: Boolean,
        required: false
      },
      builderMode: {
        type: String
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      is_changed: {
        handler: 'onChange'
      },
      is_active: {
        handler: 'initSelf',
        immediate: true
      },
      form_values: {
        handler: 'updateForm',
        immediate: true,
        deep: true
      },
      flat_values: {
        handler: 'updateFlatValues',
        deep: true
      },
      'item.form_values': {
        handler: 'resetSelf',
        deep: true
      }
    },
    data() {
      return {
        is_inited: false,
        flat_values: null,
        form_values: null,
        orig_form_values: null
      }
    },
    computed: {
      builder__is_wizard() {
        return this.builderMode == 'wizard' ? true : false
      },
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      is_changed() {
        return !_.isEqual(this.form_values, this.orig_form_values)
      },
      show_controls() {
        return !this.builder__is_wizard || this.is_active
      },
      show_description() {
        return this.show_controls && this.description.length > 0
      },
      show_summary() {
        return this.builder__is_wizard && this.is_before_active
      },
      title() {
        return _.get(this.item, 'title', 'Choose values')
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
      }
    },
    methods: {
      resetSelf() {
        // reset the form
        this.form_values = null
        this.orig_form_values = null
        this.updateForm()
      },
      initSelf(active) {
        if (active) {
          // focus first element in form and allow next step
          this.$emit('update:isNextAllowed', true)
          this.autoFocus()
        }
      },
      autoFocus() {
        if (!this.is_active) {
          return
        }

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
      getMarkdown(val) {
        return marked(val)
      },
      // this is necessary since ElForm doesn't like periods in key values
      getFlatKey(key) {
        if (!key) return
        return key.replace('.', '--')
      },
      // this is necessary since ElForm doesn't like periods in key values
      getExpandedKey(key) {
        if (!key) return
        return key.replace('--', '.')
      },
      getShowFormItem(form_item) {
        var v = form_item.is_visible
        if (!_.isObject(v)) {
          return true
        }

        try {
          return _.get(this.form_values, v.variable) == v.equals
        }
        catch (e) {
          // if something fails, default to showing the form item
          return true
        }
      },
      updateForm() {
        if (this.form_values === null) {
          var form_values = _.get(this.item, 'form_values', {})
          this.form_values = _.cloneDeep(form_values)
          this.orig_form_values = _.cloneDeep(form_values)

          var flat_values = flatten(this.form_values)
          this.flat_values = _.mapKeys(flat_values, (val, key) => { return this.getFlatKey(key) })
          this.is_inited = true
        } else {
          this.$emit('item-change', this.form_values, this.index)
        }
      },
      updateFlatValues() {
        if (this.is_inited) {
          var form_values = _.mapKeys(this.flat_values, (val, key) => { return this.getExpandedKey(key) })
          this.form_values = _.cloneDeep(unflatten(form_values))
        }
      },
      isDatePickerType(type) {
        return ['year','month','date','datetime','week','datetimerange','daterange'].indexOf(type) != -1
      },
      onChange(val) {
        if (!this.builder__is_wizard && val === true) {
          this.$emit('active-item-change', this.index)
        }
      }
    }
  }
</script>
