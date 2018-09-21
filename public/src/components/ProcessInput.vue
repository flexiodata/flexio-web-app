<template>
  <div v-if="!force_render">
    <el-form
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      inline
    >
      <el-form-item label="How would you like to send the form data?">
        <el-radio-group
          size="small"
          v-model="input_type"
        >
          <el-radio-button label="form-data" />
          <el-radio-button label="x-www-form-urlencoded" />
          <el-radio-button label="raw" />
        </el-radio-group>
      </el-form-item>
      <el-form-item
        label="What content type header would you like to use?"
        v-if="input_type == 'raw'"
      >
        <el-select
          size="small"
          v-model="raw_type"
        >
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in raw_options"
          />
        </el-select>
      </el-form-item>
    </el-form>
    <KeypairList
      :header="{ key: 'Key', val: 'Value' }"
      v-model="form_data_value"
      v-show="input_type == 'form-data'"
    />
    <KeypairList
      :header="{ key: 'Key', val: 'Value' }"
      v-model="x_www_form_urlencoded_data"
      v-show="input_type == 'x-www-form-urlencoded'"
    />
    <div v-show="input_type == 'raw'">
      <KeypairList
        :header="{ key: 'Key', val: 'Value' }"
        v-model="json_value"
        v-if="false"
      />
      <CodeEditor
        class="bg-white ba b--black-10"
        style="line-height: 1.15; font-size: 13px"
        :lang="raw_type == 'application/json' ? 'json' : ''"
        :show-json-view-toggle="false"
        :options="{ minRows: 8, maxRows: 20 }"
        v-model="raw_value"
      />
    </div>
  </div>
</template>

<script>
  import KeypairList from './KeypairList.vue'
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      value: {
        type: Object,
        required: true
      }
    },
    components: {
      KeypairList,
      CodeEditor
    },
    data() {
      return {
        force_render: false,
        raw_options: [
          { label: 'Text',                                val: 'text'                   },
          { label: 'Text (text/plain)',                   val: 'text/plain'             },
          { label: 'JSON (application/json)',             val: 'application/json'       },
          { label: 'Javascript (application/javascript)', val: 'application/javascript' },
          { label: 'XML (application/xml)',               val: 'application/xml'        },
          { label: 'XML (text/xml)',                      val: 'text/xml'               },
          { label: 'HTML',                                val: 'text/html'              }
        ]
      }
    },
    computed: {
      input_type: {
        get() {
          return _.get(this.value, 'type', 'raw')
        },
        set(value) {
          this.emitObject({ type: value })
        }
      },
      raw_type: {
        get() {
          return _.get(this.value, 'raw_type', 'application/json')
        },
        set(value) {
          this.emitObject({ raw_type: value })
        }
      },
      form_data_value: {
        get() {
          return _.get(this.value, 'form_data', {})
        },
        set(value) {
          this.emitObject({ form_data: value })
        }
      },
      x_www_form_urlencoded_data: {
        get() {
          return _.get(this.value, 'x_www_form_urlencoded', {})
        },
        set(value) {
          this.emitObject({ x_www_form_urlencoded: value })
        }
      },
      json_value: {
        get() {
          return _.get(this.value, 'json', {})
        },
        set(value) {
          this.emitObject({ json: value })
        }
      },
      raw_value: {
        get() {
          return _.get(this.value, 'raw', '')
        },
        set(value) {
          this.emitObject({ raw: value })
        }
      }
    },
    methods: {
      revert() {
        this.force_render = true
        this.$nextTick(() => { this.force_render = false })
      },
      emitObject(obj) {
        this.$emit('input', _.assign({}, this.value, obj))
      }
    }
  }
</script>
