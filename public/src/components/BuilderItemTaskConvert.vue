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
    <div class="flex flex-row flex-wrap">
      <el-form
        class="el-form--compact el-form__label-tiny mr3 mb3 pv3 ph4 bg-nearer-white br2 ba b--black-05"
        size="small"
        label-position="top"
        :model="edit_values.input"
      >
        <h4 class="f8 fw6 ttu moon-gray mt0 mb3 nl3">Input</h4>
        <el-form-item
          key="format"
          prop="format"
          label="Format"
        >
          <el-select v-model="edit_values.input.format">
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in format_options"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          key="delimiter"
          prop="delimiter"
          label="Delimiter"
          v-if="edit_values.input.format == 'delimited'"
        >
          <el-select v-model="edit_values.input.delimiter">
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in delimiter_options"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          key="qualifier"
          prop="qualifier"
          label="Text qualifier"
          v-if="edit_values.input.format == 'delimited'"
        >
          <el-select v-model="edit_values.input.qualifier">
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in qualifier_options"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          key="header"
          prop="header"
          label="First row header"
          v-if="edit_values.input.format == 'delimited'"
        >
          <el-switch v-model="edit_values.input.header" />
          <span
            class="f8 pl2 pointer dn"
            @click.stop="edit_values.input.header = !edit_values.input.header"
          >
            First row header
          </span>
        </el-form-item>
      </el-form>
      <el-form
        class="el-form--compact el-form__label-tiny mr3 mb3 pv3 ph4 bg-nearer-white br2 ba b--black-05"
        size="small"
        label-position="top"
        :model="edit_values.output"
      >
        <h4 class="f8 fw6 ttu moon-gray mt0 mb3 nl3">Output</h4>
        <el-form-item
          key="format"
          prop="format"
          label="Format"
        >
          <el-select v-model="edit_values.output.format">
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in format_options"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          key="delimiter"
          prop="delimiter"
          label="Delimiter"
          v-if="edit_values.output.format == 'delimited'"
        >
          <el-select v-model="edit_values.output.delimiter">
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in delimiter_options"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          key="qualifier"
          prop="qualifier"
          label="Text qualifier"
          v-if="edit_values.output.format == 'delimited'"
        >
          <el-select v-model="edit_values.output.qualifier">
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in qualifier_options"
            />
          </el-select>
        </el-form-item>
        <el-form-item
          key="header"
          prop="header"
          label="First row header"
          v-if="edit_values.output.format == 'delimited'"
        >
          <el-switch v-model="edit_values.output.header" />
          <span
            class="f8 pl2 pointer dn"
            @click.stop="edit_values.output.header = !edit_values.output.header"
          >
            First row header
          </span>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'

  const format_options = [
    { label: 'CSV',             val: 'csv'       },
    { label: 'TSV',             val: 'tsv'       },
    { label: 'Delimited Text',  val: 'delimited' },
    { label: 'JSON',            val: 'json'      },
    { label: 'PDF',             val: 'pdf'       },
    { label: 'RSS',             val: 'rss'       },
    { label: 'Atom',            val: 'atom'      },
    { label: 'Table',           val: 'table'     }
  ]

  const delimiter_options = [
    { label: 'None',       val: ''   },
    { label: 'Comma',      val: ','  },
    { label: 'Tab',        val: '\t' },
    { label: 'Semicolon',  val: ';'  },
    { label: 'Pipe',       val: '|'  },
    { label: 'Space',      val: ' '  }
  ]

  const qualifier_options = [
    { label: 'None',         val: ''   },
    { label: 'Double-quote', val: '"'  },
    { label: 'Single-quote', val: '\'' }
  ]

  const getDefaultInputValues = () => {
    return _.assign({}, {
      format: 'json',
      delimiter: ',',
      qualifier: '"',
      header: true
    })
  }

  const getDefaultOutputValues = () => {
    return _.assign({}, {
      format: 'table',
      delimiter: ',',
      qualifier: '"',
      header: true
    })
  }

  const getDefaultValues = () => {
    return _.assign({}, {
      op: 'convert',
      input: getDefaultInputValues(),
      output: getDefaultOutputValues()
    })
  }

  const mapDelimiter = (v) => {
    switch (v) {
      default:            return v
      case '{none}':      return ''
      case '{comma}':     return ','
      case '{tab}':       return '\t'
      case '{semicolon}': return ';'
      case '{pipe}':      return '|'
      case '{space}':     return ' '
    }
  }

  const mapQualifier = (v) => {
    switch (v) {
      default:               return v
      case '{none}':         return ''
      case '{double-quote}': return '"'
      case '{single-quote}': return "'"
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
      }
    },
    data() {
      return {
        format_options,
        delimiter_options,
        qualifier_options,
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues()
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Connect')
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
        // initialize default values
        var form_values = _.get(this.item, 'form_values', {})
        form_values = _.cloneDeep(form_values)

        // if the convert step doesn't have an input or output, use default values
        var is_new = !_.has(form_values, 'input') || !_.has(form_values, 'output')

        form_values.input = _.assign({}, getDefaultInputValues(), form_values.input)
        form_values.output = _.assign({}, getDefaultOutputValues(), form_values.output)

        // TODO: this can be removed after awhile
        // translate old delimiter and qualifier values
        _.each(['input', 'output'], (key) => {
          form_values[key]['delimiter'] = mapDelimiter(form_values[key]['delimiter'])
          form_values[key]['qualifier'] = mapQualifier(form_values[key]['qualifier'])
        })

        var orig_values = _.cloneDeep(this.orig_values)
        var edit_values = _.cloneDeep(this.edit_values)

        // update values from item
        _.each(['input', 'output'], (key) => {
          orig_values[key] = _.assign({}, orig_values[key], form_values[key])
          edit_values[key] = _.assign({}, edit_values[key], form_values[key])
        })

        this.orig_values = orig_values
        this.edit_values = edit_values

        if (is_new) {
          // when creating a new convert task, make sure we fire an 'item-change'
          // event so that the pipedocument module knows about the default config
          // if the user attempts to save the function without editing the step at all
          //
          // NOTE: we cannot use $nextTick here because this call happens multiple times
          setTimeout(() => { this.onEditValuesChange() }, 1)
        }
      },
      getEditValues() {
        var input = _.assign({}, _.get(this.edit_values, 'input'))
        var output = _.assign({}, _.get(this.edit_values, 'output'))

        if (input.format != 'delimited') {
          input =  _.pick(input, ['format'])
        }

        if (output.format != 'delimited') {
          output = _.pick(output, ['format'])
        }

        return {
          op: 'convert',
          input,
          output
        }
      },
      onChange(val) {
        if (val) {
          this.$emit('active-item-change', this.index)
        }
      },
      onEditValuesChange() {
        this.$emit('item-change', this.getEditValues(), this.index)
      }
    }
  }
</script>
