<template>
  <div>
    <connection-info-configure-item
      :item="{ key: 'Key', val: 'Value' }"
      :is-static="true"
    />
    <connection-info-configure-item
      v-for="(item, index) in headers"
      :key="index"
      :item="item"
      :index="index"
      @change="onItemChange"
      @delete="onItemDelete"
    />
    <div class="flex flex-row justify-end pt2 mt2 bt b--light-gray">
      <btn btn-md class="b ttu blue mr2" :disabled="!is_changed" @click="onCancel">Cancel</btn>
      <btn btn-md btn-primary class="ttu b" :disabled="!is_changed" @click="onSave">Save</btn>
    </div>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import ConnectionInfoConfigureItem from './ConnectionInfoConfigureItem.vue'

  var newItem = () => {
    return {
      key: '',
      val: ''
    }
  }

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
      }
    },
    components: {
      Btn,
      ConnectionInfoConfigureItem
    },
    data() {
      var headers = _.get(this.connection, 'connection_info.headers', [])
      headers.push(newItem())

      return {
        headers,
        output_headers: _.cloneDeep(headers),
        original_headers: _.cloneDeep(headers)
      }
    },
    computed: {
      is_changed() {
        return !_.isEqual(this.headers, this.original_headers)
      }
    },
    methods: {
      onItemChange(item, index) {
        if (index == _.size(this.headers) - 1)
          this.headers = [].concat(this.headers).concat(newItem())
      },
      onItemDelete(item, index) {
        debugger
        _.pullAt(this.headers, [index])
        this.$nextTick(() => { this.headers = [].concat(this.headers) })
      },
      onCancel() {
        this.headers = []
        this.$nextTick(() => { this.headers = [].concat(this.original_headers) })
      },
      onSave() {

      }
    }
  }
</script>
