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
        label="Select the path of the extract file or table"
        key="path"
        prop="path"
      >
        <el-input
          auto-complete="off"
          spellcheck="false"
          placeholder="Enter the extract file or table"
          v-model="edit_values.path"
        >
          <BrowseButton
            slot="append"
            class="ttu fw6"
            :fileChooserOptions="{
              filetypeFilter: ['csv','icsv','xls','xlsx']
            }"
            @paths-selected="onPathsSelected"
          >
            Browse
          </BrowseButton>
        </el-input>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import BrowseButton from '@/components/BrowseButton'

  const getDefaultValues = () => {
    return {
      path: '',
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
      Spinner,
      BrowseButton
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
        rules: {
          path: [
            { required: true, message: 'Please select the path of the file or table on which to do the extract' }
          ]
        }
      }
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
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
      onPathsSelected(path) {
        this.edit_values.path = path
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
