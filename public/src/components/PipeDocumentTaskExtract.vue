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
        label="Select the path of the extract file or table"
        key="path"
        prop="path"
      >
        <el-input
          auto-complete="off"
          spellcheck="false"
          placeholder="Enter the extract file or table"
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
      </div>
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
  import marked from 'marked'
  import { mapState } from 'vuex'
  import { TASK_OP_EXTRACT } from '@/constants/task-op'
  import api from '@/api'
  import Spinner from 'vue-simple-spinner'
  import BrowseButton from '@/components/BrowseButton'
  import SimpleTable from '@/components/SimpleTable'

  const getDefaultState = () => {
    return {
        path: '',
        structure: [],
        fetching_structure: false,
        fetched_structure_path: '',
        form_errors: {},
        rules: {
          path: [
            { required: true, message: 'Please select the path of the file or table on which to do the extract' }
          ]
        }
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
        return this.path != this.task.path
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
          op: TASK_OP_EXTRACT,
          path: this.path,
        }

        this.$emit('save-click', new_task, this.task)
      },
    }
  }
</script>
