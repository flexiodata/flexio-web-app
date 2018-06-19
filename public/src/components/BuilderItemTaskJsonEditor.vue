<template>
  <div>
    <CodeEditor
      class="bg-white ba b--black-10 overflow-y-auto"
      lang="javascript"
      :options="{ minRows: 12, maxRows: 30 }"
      v-model="edit_json"
    />
    <transition name="el-zoom-in-top">
      <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="json_parse_error.length > 0">Parse error: {{json_parse_error}}</div>
    </transition>
  </div>
</template>

<script>
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      is_changed: {
        handler: 'onChange'
      },
      edit_json: {
        handler: 'onEditJsonChange'
      }
    },
    data() {
      return {
        edit_json: this.item.value,
        orig_json: this.item.value,
        json_parse_error: '',
      }
    },
    computed: {
      is_changed() {
        return this.edit_json != this.orig_json
      }
    },
    methods: {
      onChange(val) {
        if (val === true) {
          this.$store.commit('builder/SET_ACTIVE_ITEM', this.index)
        }
      },
      onEditJsonChange() {
        try {
          var task = JSON.parse(this.edit_json)
          this.$store.commit('pipe/UPDATE_EDIT_TASK', { index: this.index, attrs: task })
          this.json_parse_error = ''

        }
        catch(e)
        {
          this.json_parse_error = e.message
        }
      }
    }
  }
</script>
