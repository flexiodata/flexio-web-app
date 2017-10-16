<template>
  <div>
    <keypair-item
      :item="{ key: 'Key', val: 'Value' }"
      :is-static="true"
    />
    <keypair-item
      v-for="(item, index) in headers"
      :key="index"
      :item="item"
      :index="index"
      @change="onItemChange"
      @delete="onItemDelete"
    />
    <div class="flex flex-row justify-end pt2 mt4 bt b--light-gray">
      <btn btn-md class="b ttu blue mr2" :disabled="!isNew && !is_changed" @click="onCancel">Cancel</btn>
      <btn btn-md btn-primary class="ttu b" :disabled="!isNew && !is_changed" @click="onSave">
        <span v-if="isNew">Create Connection</span>
        <span v-else>Save Changes</span>
      </btn>
    </div>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import KeypairItem from './KeypairItem.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      eid_status: OBJECT_STATUS_PENDING,
      name: '',
      description: '',
      connection_type: '',
      connection_info: {},
      rights: defaultRights()
    }
  }

  const newItem = () => {
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
      },
      'is-new': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Btn,
      KeypairItem
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
        _.pullAt(this.headers, [index])
        this.$nextTick(() => { this.headers = [].concat(this.headers) })
      },
      onCancel() {
        this.headers = []
        this.$nextTick(() => { this.headers = [].concat(this.original_headers) })

        this.$emit('cancel', this.connection)
      },
      onSave() {
        this.$emit('submit', this.connection)
      }
    }
  }
</script>
