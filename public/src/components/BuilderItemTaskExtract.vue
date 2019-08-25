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
              filetypeFilter: ['csv','icsv']
            }"
            @paths-selected="onPathsSelected"
          >
            Browse
          </BrowseButton>
        </el-input>
      </el-form-item>
      <div
        class="relative el-form-item"
        v-if="fetching_structure"
      >
        <div class="flex flex-row items-center">
          <Spinner size="small" />
          <span class="ml2 el-form-item__label">Loading structure...</span>
        </div>
      </div>
      <div
        class="relative el-form-item"
        style="margin-top: -12px"
        v-else-if="has_structure"
      >
        <SimpleTable
          class="overflow-x-auto"
          :columns="structure_cols"
          :rows="structure_rows"
        />
      </div>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState } from 'vuex'
  import api from '@/api'
  import Spinner from 'vue-simple-spinner'
  import BrowseButton from '@/components/BrowseButton'
  import SimpleTable from '@/components/SimpleTable'

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
      BrowseButton,
      SimpleTable
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
      'edit_values.path': {
        handler: 'onPathChange'
      },
      form_errors(val) {
        this.$emit('update:isNextAllowed', _.keys(val).length == 0)
      }
    },
    data() {
      return {
        structure: [],
        fetching_structure: false,
        fetched_structure_path: '',
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
      },
      structure_cols() {
        return _.get(this.structure, 'columns', [])
      },
      structure_rows() {
        return _.get(this.structure, 'rows', [])
      },
      has_structure() {
        return this.fetched_structure_path.length > 0 && this.structure_cols.length > 0
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})
        this.orig_values = _.assign({}, getDefaultValues(), form_values)
        this.edit_values = _.assign({}, getDefaultValues(), form_values)
        this.fetchStructure()
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
        this.fetchStructure()
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
      },
      onPathChange: _.debounce(function(path) {
        this.fetchStructure()
      }, 1000),
      fetchStructure() {
        if (this.fetching_structure === true) {
          return
        }

        var path = _.get(this.edit_values, 'path', '')
        if (path.indexOf(':/') > 0 && this.fetched_structure_path != path) {
          this.fetching_structure = true
          api.vfsFetchInfo(this.active_team_name, path).then(response => {
            this.fetched_structure_path = path
            this.structure = response.data
          })
          .catch(error => {
            this.structure = []
          })
          .finally(() => {
            this.fetching_structure = false
          })
        }
      }
    }
  }
</script>
