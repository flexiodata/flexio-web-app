<template>
  <el-form
    ref="form"
    label-width="9rem"
    :model="form_values"
    v-if="form_values"
  >
    <el-form-item
      key="name"
      label="Name"
      prop="name"
    >
      <el-input
        placeholder="Enter name"
        autofocus
        v-model="form_values.name"
      />
    </el-form-item>
    <el-form-item
      key="alias"
      label="Path"
      prop="alias"
    >
      <el-input
        placeholder="Enter alias"
        v-model="form_values.alias"
      >
        <template slot="prepend">https://api.flex.io/v1/me/pipes/</template>
        <template slot="append">
          <el-button
            slot="append"
            class="hint--top"
            aria-label="Copy"
            size="small"
            icon="el-icon-document"
            :data-clipboard-text="path"

          />
        </template>
      </el-input>
    </el-form-item>
    <el-form-item
      key="description"
      label="Description"
      prop="description"
    >
      <el-input
        type="textarea"
        placeholder="Enter description"
        rows="3"
        v-model="form_values.description"
      />
    </el-form-item>
  </el-form>
</template>

<script>
  export default {
    watch: {
      form_values: {
        handler: 'updateForm',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        form_values: null
      }
    },
    computed: {
      path() {
        return 'https://api.flex.io/v1/me/pipes/' + this.form_values.alias
      }
    },
    methods: {
      updateForm() {
        if (this.form_values === null) {
          var form_values = _.get(this.$store, 'state.pipe.edit_pipe')
          this.form_values = _.cloneDeep(form_values)
        } else {
          this.$store.commit('pipe/UPDATE_EDIT_PIPE', this.form_values)
        }
      }
    }
  }
</script>
