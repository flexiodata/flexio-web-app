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
        class="el-form--compact el-form__label-tiny mr3 mb3 bg-nearer-white ba b--black-05 br2 pa4"
        style="width: 300px"
        label-position="top"
        :model="edit_values.input"
      >
        <h4 class="mt0 mb3">Input</h4>
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
          v-if="edit_values.input.format == 'delimited'"
        >
          <el-switch v-model="edit_values.input.header" />
          <span
            class="f6 pl2 pointer"
            @click.stop="edit_values.input.header = !edit_values.input.header"
          >
            First row header
          </span>
        </el-form-item>
      </el-form>
      <el-form
        class="el-form--compact el-form__label-tiny mr3 mb3 bg-nearer-white ba b--black-05 br2 pa4"
        style="width: 300px"
        label-position="top"
        :model="edit_values.output"
      >
        <h4 class="mt0 mb3">Output</h4>
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
          v-if="edit_values.output.format == 'delimited'"
        >
          <el-switch v-model="edit_values.output.header" />
          <span
            class="f6 pl2 pointer"
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
    { label: 'CSV',        val: 'csv'       },
    { label: 'TSV',        val: 'tsv'       },
    { label: 'Delimited',  val: 'delimited' },
    { label: 'JSON',       val: 'json'      },
    { label: 'PDF',        val: 'pdf'       },
    { label: 'RSS',        val: 'rss'       },
    { label: 'Atom',       val: 'atom'      },
    { label: 'Table',      val: 'table'     }
  ]

  const delimiter_options = [
    { label: 'None',       val: '{none}'      },
    { label: 'Comma',      val: '{comma}'     },
    { label: 'Tab',        val: '{tab}'       },
    { label: 'Semicolon',  val: '{semicolon}' },
    { label: 'Pipe',       val: '{pipe}'      },
    { label: 'Space',      val: '{space}'     }
  ]

  const qualifier_options = [
    { label: 'None',         val: '{none}'         },
    { label: 'Double-quote', val: '{double-quote}' },
    { label: 'Single-quote', val: '{single-quote}' }
  ]

  const getDefaultValues = () => {
    return _.assign({}, {
      op: 'convert',
      input: {
        format: 'json',
        delimiter: '{comma}',
        qualifier: '{double-quote}',
        header: true
      },
      output: {
        format: 'table',
        delimiter: '{comma}',
        qualifier: '{double-quote}',
        header: true
      }
    })
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
        console.log('convert')
        var form_values = _.get(this.item, 'form_values', {})

        var orig_values = _.cloneDeep(this.orig_values)
        var edit_values = _.cloneDeep(this.edit_values)

        _.each(['input', 'output'], (key) => {
          orig_values[key] = _.assign({}, orig_values[key], form_values[key])
          edit_values[key] = _.assign({}, edit_values[key], form_values[key])
        })

        this.orig_values = orig_values
        this.edit_values = edit_values
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
