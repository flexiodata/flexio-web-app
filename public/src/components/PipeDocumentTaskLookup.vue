<template>
  <div>
    <el-form
      ref="form"
      class="flex-fill el-form--cozy el-form__label-tiny"
      label-position="top"
      :model="$data"
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
          v-model="path"
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
        <div class="mt3 bb bw1 b--black-10"></div>
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
          v-model="lookup_keys"
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
          v-model="return_columns"
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
    <div
      class="flex-none flex flex-row justify-end"
      v-show="is_changed"
    >
      <el-button
        class="ttu fw6"
        @click="initSelf"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
        type="primary"
        :disabled="!isSaveAllowed"
        @click="onSaveClick"
      >
        Save Changes
      </el-button>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { TASK_OP_LOOKUP } from '@/constants/task-op'
  import api from '@/api'
  import Spinner from 'vue-simple-spinner'
  import BrowseButton from '@/components/BrowseButton'
  import SimpleTable from '@/components/SimpleTable'

  const getDefaultState = () => {
    return {
      structure: [],
      fetching_structure: false,
      fetched_structure_path: '',
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
      },

      // task values
      path: '',
      lookup_keys: [],
      return_columns: [],
    }
  }

  export default {
    props: {
      task: {
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
      task: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      is_changed: {
        handler: 'updateIsEditing',
        immediate: true
      },
      path: {
        handler: 'onPathChange'
      },
      form_errors(val) {
        this.$emit('update:isSaveAllowed', _.keys(val).length == 0)
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      is_changed() {
        if (this.path != _.get(this.task, 'path', '')) { return true }
        if (this.lookup_keys != _.get(this.task, 'lookup_keys', [])) { return true }
        if (this.return_columns != _.get(this.task, 'return_columns', [])) { return true }
        return false
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
        // reset our local component data
        _.assign(this.$data, getDefaultState(), this.task)
        this.fetchStructure()
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
      updateIsEditing() {
        this.$emit('update:isEditing', this.is_changed)
      },
      fetchStructure() {
        if (this.fetching_structure === true) {
          return
        }

        if (this.path.indexOf(':/') > 0 && this.fetched_structure_path != this.path) {
          this.fetching_structure = true
          api.vfsFetchInfo(this.active_team_name, this.path).then(response => {
            this.fetched_structure_path = this.path
            this.structure = response.data
          })
          .catch(error => {
            this.structure = []
          })
          .finally(() => {
            this.fetching_structure = false
          })
        }
      },
      onPathsSelected(path) {
        this.path = path
        this.fetchStructure()
      },
      onPathChange: _.debounce(function(path) {
        this.fetchStructure()
      }, 1000),
      onSaveClick() {
        var new_task = {
          op: TASK_OP_LOOKUP,
          path: this.path,
          lookup_keys: this.lookup_keys,
          return_columns: this.return_columns
        }

        this.$emit('save-click', new_task, this.task)
      },
    }
  }
</script>
