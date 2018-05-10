<template>
  <el-form
    class="el-form-cozy"
    label-width="7rem"
    :model="form_values"
    :rules="rules"
    v-if="form_values"
  >
    <el-form-item
      key="name"
      label="Name"
      prop="name"
    >
      <el-input
        placeholder="Enter name"
        style="max-width: 36rem"
        :autofocus="true"
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
        style="max-width: 36rem"
        v-model="form_values.alias"
      >
        <template slot="prepend">https://api.flex.io/v1/me/pipes/</template>
        <template slot="append">
          <el-button
            slot="append"
            class="hint--top"
            aria-label="Copy to Clipboard"
            size="small"
            :data-clipboard-text="path"
          ><span class="ttu b">Copy</span></el-button>
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
        style="max-width: 48rem"
        :rows="3"
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
        form_values: null,
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' }
          ]
        }
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
          this.resetForm()
        } else {
          this.$store.commit('pipe/UPDATE_EDIT_PIPE', this.form_values)
        }
      },
      resetForm() {
        var form_values = _.get(this.$store, 'state.pipe.edit_pipe')
        this.form_values = _.cloneDeep(form_values)
      }
    }
  }
</script>
