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
          placeholder="Enter the names of the key fields"
          style="width: 100%"
          v-model="edit_values.lookup_keys"
        >
          <el-option
            v-for="item in []"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
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
          placeholder="Enter the names of the columns to return"
          style="width: 100%"
          v-model="edit_values.return_columns"
        >
          <el-option
            v-for="item in []"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import { atobUnicode, btoaUnicode } from '@/utils'

  // non-base64 Python code
  const template_code = `
# looks up data values from a table based on keys

import pandas
import json
from io import StringIO

def flex_handler(flex):

    # data values for which to look up content
    lookup_values = flex.input.read()
    # lookup_values = [["000031","00410575"],["a","00306529"],["000053","b"],["000053","00306529"],["000053","00306529"]]

    # table to use for lookup values
    lookup_table_path = '{{lookup_table_path}}'
    # lookup_table_path = 'my-connection-alias:/my-file.txt'

    # columns from the lookup table to use to search for values
    lookup_keys = '{{lookup_keys}}'.split(',')
    # lookup_keys = ["Vend_no","Item_no"]

    # columns from the lookup table to return
    lookup_columns = '{{lookup_columns}}'.split(',')
    # lookup_columns = ["Item_desc","Case_cost","Item_cost"]

    # table config to use for lookup
    file = flex.fs.open(lookup_table_path)
    lookup_table_content = file.read()
    file.close()

    lookup_table_config = {
        "data": lookup_table_content,
        "delimiter": ",",
        "quotechar": "\"",
        "encoding": "utf8"
    }

    # get the result and return it
    result = lookupValues(lookup_table_config, lookup_keys, lookup_columns, lookup_values)
    flex.output.write(json.dumps(result))


def lookupValues(lookup_table_config, lookup_keys, lookup_columns, lookup_values):

    # seperator for constructing keys
    seperator = "|"

    # load the csv into a dictionary using pandas
    df = pandas.read_csv(lookup_table_config['data'], sep=lookup_table_config['delimiter'], quotechar=lookup_table_config['quotechar'], skipinitialspace=True, encoding=lookup_table_config['encoding'], dtype = str)
    lookup_table = df.to_dict('records')

    # create a dictionary of lookup values based on the key
    lookup_table_index = {}
    for row in lookup_table:
        keyvalues = []
        for lk in lookup_keys:
            keyvalues.append(str(row.get(lk,'')))
        key = seperator.join(keyvalues)
        lookup_table_index[key] = row

    # return an output corresponding to the exact order of the
    # input values, filled out with the appropriate information from the
    # index use all fields if no fields are specified
    result = []
    result.append(lookup_columns)
    for lv in lookup_values:
        key = seperator.join(lv)
        result.append(getValuesFromLookup(key, lookup_table_index, lookup_columns))

    return result


def getValuesFromLookup(key, lookup_table_index, lookup_columns):
    default_values = ['']*len(lookup_columns)
    default_row = dict(zip(lookup_columns, default_values))
    lookup_values = lookup_table_index.get(key, default_row)
    result = [lookup_values.get(c,'') for c in lookup_columns]
    return result
`

  const getDefaultValues = () => {
    return {
      lookup_keys: [],
      return_columns: [],
      lang: 'python',
      code: ''
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
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues(),
        form_errors: {},
        template_code: template_code,
        rules: {
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
      onChange(val) {
        if (val) {
          this.$nextTick(() => { this.validateForm(true) })
          this.$emit('active-item-change', this.index)
        }
      },
      onEditValuesChange() {
        var vals = _.cloneDeep(this.edit_values)
        vals.code = this.template_code
        vals.code = vals.code.replace('{{lookup_keys}}', this.edit_values.lookup_keys.join(','))
        vals.code = vals.code.replace('{{lookup_columns}}', this.edit_values.return_columns.join(','))
        vals.code = btoaUnicode(vals.code)
        this.$emit('item-change', vals, this.index)
      }
    }
  }
</script>
