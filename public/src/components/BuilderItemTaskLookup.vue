<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <el-form
      ref="form"
      class="flex-fill el-form--compact el-form__label-tiny"
      label-position="top"
      :model="edit_values"
      :rules="rules"
      @validate="onValidateItem"
    >
      <el-form-item
        label="Select the path of the lookup file or table"
        key="path"
        prop="path"
      >
        <el-input
          auto-complete="off"
          spellcheck="false"
          placeholder="Enter the lookup file or table"
          v-model="edit_values.path"
        >
          <el-button
            slot="append"
            class="ttu fw6"
            type="primary"
            size="small"
            @click="show_file_chooser_dialog = true"
          >
            Browse
          </el-button>
        </el-input>
      </el-form-item>
      <el-form-item
        key="lookup_keys"
        label="Select the key fields"
        prop="lookup_keys"
      >
        <el-select
          multiple
          filterable
          allow-create
          default-first-option
          popper-class="dn"
          class="w-100"
          spellcheck="false"
          placeholder="Enter the names of the key fields"
          v-model="edit_values.lookup_keys"
          v-tag
        >
          <el-option
            :label="item.label"
            :value="item.value"
            :key="item.value"
            v-for="item in []"
          />
        </el-select>
      </el-form-item>
      <el-form-item
        key="return_columns"
        label="Select columns to return"
        prop="return_columns"
      >
        <el-select
          multiple
          filterable
          allow-create
          default-first-option
          popper-class="dn"
          class="w-100"
          spellcheck="false"
          placeholder="Enter the names of the columns to return"
          v-model="edit_values.return_columns"
          v-tag
        >
          <el-option
            :label="item.label"
            :value="item.value"
            :key="item.value"
            v-for="item in []"
          />
        </el-select>
      </el-form-item>
    </el-form>

    <!-- file chooser dialog -->
    <el-dialog
      custom-class="el-dialog--compressed-body"
      title="Choose files"
      width="60vw"
      top="4vh"
      :append-to-body="true"
      :visible.sync="show_file_chooser_dialog"
    >
      <FileChooser
        ref="file-chooser"
        style="max-height: 60vh"
        :selected-items.sync="selected_files"
        :allow-multiple="false"
        :allow-folders="false"
        :show-connection-list="true"
        v-if="show_file_chooser_dialog"
      />
      <span slot="footer" class="dialog-footer">
        <el-button
          class="ttu fw6"
          @click="show_file_chooser_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="addFiles"
        >
          Choose a file
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
  import marked from 'marked'
  import { atobUnicode, btoaUnicode } from '@/utils'
  import FileChooser from '@/components/FileChooser'

  const getDefaultValues = () => {
    return {
      path: '',
      lookup_keys: [],
      return_columns: []
    }
  }

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
        required: true
      }
    },
    components: {
      FileChooser
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      is_changed: {
        handler: 'onChange'
      },
      edit_values: {
        handler: 'onEditValuesChange',
        immediate: true,
        deep: true
      },
      form_errors(val) {
        this.$emit('update:isNextAllowed', _.keys(val).length == 0)
      }
    },
    data() {
      return {
        selected_files: [],
        show_file_chooser_dialog: false,
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues(),
        form_errors: {},
        rules: {
          path: [
            { required: true, message: 'Please select the path of the file or table on which to do the lookup' }
          ],
          lookup_keys: [
            { required: true, message: 'Please input the key field on which to do the lookup' }
          ],
          return_columns: [
            { required: true, message: 'Please input the names of the columns to return' }
          ]
        }
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Lookup')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      is_changed() {
        return !_.isEqual(this.edit_values, this.orig_values)
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})
        this.orig_values = _.assign({}, getDefaultValues(), form_values)
        this.edit_values = _.assign({}, getDefaultValues(), form_values)
        this.$nextTick(() => { this.validateForm(true) })
      },
      validateForm(clear) {
        if (this.$refs.form) {
          this.$refs.form.validate((valid) => {
            this.$emit('update:isNextAllowed', valid)
            if (clear === true) {
              this.$refs.form.clearValidate()
            }
          })
        }
      },
      onValidateItem(key, valid) {
        var errors = _.assign({}, this.form_errors)
        if (valid) {
          errors = _.omit(errors, [key])
        } else {
          errors[key] = true
        }
        this.form_errors = _.assign({}, errors)
      },
      addFiles() {
        var files = this.selected_files
        files = _.map(files, (f) => { return f.full_path })
        this.edit_values.path = _.get(files, '[0]', '')
        this.show_file_chooser_dialog = false
      },
      onChange(val) {
        if (val) {
          this.$nextTick(() => { this.validateForm(true) })
          this.$emit('active-item-change', this.index)
        }
      },
      onEditValuesChange() {
        var vals = _.cloneDeep(this.edit_values)
        this.$emit('item-change', vals, this.index)
      }
    }
  }
</script>
