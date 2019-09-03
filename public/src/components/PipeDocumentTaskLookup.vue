<template>
  <div>
    <el-form
      ref="form"
      class="flex-fill el-form--compact el-form__label-tiny"
      label-position="top"
      :model="edit_values"
      :rules="rules"
      @validate="onValidateItem"
    >
      <el-form-item
        label="1. Select the path of the lookup file or table"
        key="path"
        prop="path"
      >
        <el-input
          auto-complete="off"
          spellcheck="false"
          placeholder="Enter the lookup file or table"
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
        v-else-if="has_structure"
      >
        <label class="el-form-item__label">Preview</label>
        <SimpleTable
          class="overflow-x-auto"
          :columns="structure_cols"
          :rows="structure_rows"
        />
        <div class="mt3 mb2 bb bw1 b--black-10"></div>
      </div>
      <el-form-item
        key="lookup_keys"
        label="2. Select the key fields"
        prop="lookup_keys"
        :class="fetching_structure || !has_structure ? 'o-40 no-pointer-events' : ''"
      >
        <el-select
          multiple
          filterable
          default-first-option
          class="w-100"
          spellcheck="false"
          placeholder="Enter the names of the key fields"
          v-model="edit_values.lookup_keys"
        >
          <el-option
            :label="item.name"
            :value="item.name"
            :key="item.name"
            v-for="item in structure_cols"
          />
        </el-select>
      </el-form-item>
      <el-form-item
        key="return_columns"
        label="3. Select the columns to return"
        prop="return_columns"
        :class="fetching_structure || !has_structure ? 'o-40 no-pointer-events' : ''"
      >
        <el-select
          multiple
          filterable
          default-first-option
          class="w-100"
          spellcheck="false"
          placeholder="Enter the names of the columns to return"
          v-model="edit_values.return_columns"
        >
          <el-option
            :label="item.name"
            :value="item.name"
            :key="item.name"
            v-for="item in structure_cols"
          />
        </el-select>
      </el-form-item>
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
      isEditing: {
        type: Boolean,
        required: true
      },
      isSaveAllowed: {
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
        this.$emit('update:isSaveAllowed', _.keys(val).length == 0)
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
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
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
            this.$emit('update:isSaveAllowed', valid)
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
