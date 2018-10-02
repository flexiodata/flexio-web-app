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
          v-model="ui.form_type"
        >
          <el-radio-button
            :label="option.val"
            :key="option.val"
            v-for="option in form_options"
          >
            {{option.label}}
          </el-radio-button>
        </el-radio-group>
      </el-form-item>
      <el-form-item
        label="What content type header would you like to use?"
        v-if="ui.form_type == 'raw'"
      >
        <!-- TODO: Added 1px top margin to make it line up. I don't care... -->
        <el-select
          size="small"
          style="margin-top: 1px"
          v-model="ui.raw_type"
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
      v-model="ui.form_data"
      v-show="ui.form_type == 'multipart/form-data'"
    />
    <KeypairList
      :header="{ key: 'Key', val: 'Value' }"
      v-model="ui.x_www_form_urlencoded"
      v-show="ui.form_type == 'application/x-www-form-urlencoded'"
    />
    <div v-show="ui.form_type == 'raw'">
      <KeypairList
        :header="{ key: 'Key', val: 'Value' }"
        v-model="ui.json"
        v-if="false"
      />
      <CodeEditor
        class="bg-white ba b--black-10"
        style="line-height: 1.15; font-size: 13px"
        :lang="ui.raw_type == 'application/json' ? 'json' : ''"
        :show-json-view-toggle="false"
        :options="{ minRows: 8, maxRows: 20 }"
        v-model="ui.raw"
      />
    </div>
  </div>
</template>

<script>
  import KeypairList from './KeypairList.vue'
  import CodeEditor from './CodeEditor.vue'

  const getDefaultUiValues = () => {
    return _.assign({}, {
      form_type: 'multipart/form-data',
      raw_type: 'application/json',
      form_data: {},
      x_www_form_urlencoded: {},
      json: {},
      raw: ''
    })
  }

  export default {
    props: {
      value: {
        type: Object,
        required: true
      },
      processData: {
        type: Object
      }
    },
    components: {
      KeypairList,
      CodeEditor
    },
    watch: {
      ui: {
        handler: 'emitChange',
        deep: true
      },
      processData: {
        handler: 'updateFromData',
        deep: true
      }
    },
    data() {
      return {
        force_render: false,
        ui: getDefaultUiValues(),
        form_options: [
          { label: 'form-data',             val: 'multipart/form-data'               },
          { label: 'x-www-form-urlencoded', val: 'application/x-www-form-urlencoded' },
          { label: 'raw',                   val: 'raw'                               }
        ],
        raw_options: [
          { label: 'Text (none)',                         val: ''                       },
          { label: 'Text (text/plain)',                   val: 'text/plain'             },
          { label: 'JSON (application/json)',             val: 'application/json'       },
          { label: 'Javascript (application/javascript)', val: 'application/javascript' },
          { label: 'XML (application/xml)',               val: 'application/xml'        },
          { label: 'XML (text/xml)',                      val: 'text/xml'               },
          { label: 'HTML',                                val: 'text/html'              }
        ]
      }
    },
    methods: {
      revert() {
        this.force_render = true
        this.$nextTick(() => { this.force_render = false })
      },
      getContentType() {
        var ui = this.ui
        var form_type = ui.form_type
        var raw_type = ui.raw_type
        return form_type == 'raw' ? raw_type : form_type
      },
      getHeaders() {
        var headers = {}
        var content_type = this.getContentType()
        if (content_type.length > 0) {
          headers['Content-Type'] = content_type
        }
        return headers
      },
      getData() {
        var ui = this.ui
        var form_type = ui.form_type
        var raw_type = ui.raw_type

        if (form_type == 'raw') {
          if (raw_type == 'application/json') {
            try {
              var json = JSON.parse(ui.raw)
              return json
            }
            catch(e) {
              return {
                error: {
                  msg: 'Malformed JSON.'
                }
              }
            }
          } else {
            return ui.raw
          }
        } else if (form_type == 'multipart/form-data') {
          var data = ui.form_data

          const form_data = new FormData()
          _.keys(data).forEach(key => {
            form_data.append(key, data[key])
          })

          return form_data
        } else if (form_type == 'application/x-www-form-urlencoded') {
          var data = ui.x_www_form_urlencoded
          var str = Object.keys(data).map(key => key + '=' + data[key]).join('&')
          return str
        }
      },
      updateFromData(data) {
        this.ui.form_data = _.cloneDeep(data)
        this.ui.x_www_form_urlencoded = _.cloneDeep(data)
        this.ui.json = _.cloneDeep(data)
        this.ui.raw = JSON.stringify(data, null, 2)
        this.revert()
        this.emitChange()
      },
      emitChange() {
        var value = {
          headers: this.getHeaders(),
          data: this.getData()
        }
        this.$emit('input', value)
      }
    }
  }
</script>
