<template>
  <div>
    <connection-info-configure-item
      :item="{ key: 'Key', val: 'Value' }"
      :is-static="true"
    />
    <connection-info-configure-item
      v-for="(item, index) in headers"
      :key="item.key"
      :item="item"
      :index="index"
      @change="onItemChange"
    />
    <div class="flex flex-row justify-end mt2">
      <btn btn-md class="b ttu blue mr2">Cancel</btn>
      <btn btn-md class="b ttu blue">Save</btn>
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
        headers
      }
    },
    methods: {
      onItemChange(item, index) {
        if (index == _.size(this.headers) - 1)
          this.headers = [].concat(this.headers).concat(newItem())
      }
    }
  }
</script>
