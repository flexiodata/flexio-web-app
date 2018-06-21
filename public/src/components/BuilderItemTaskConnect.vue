<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <BuilderItemConnectionChooser
      :item="item"
      :index="index"
      :active-item-idx="activeItemIdx"
      :builder-mode="'build'"
      :show-title="false"
      :connection-eid.sync="connection"
      v-on="$listeners"
    />
    <el-form
      class="el-form--compact"
      :model="$data"
      v-if="connection.length > 0"
    >
      <el-form-item
        key="alias"
        prop="alias"
      >
        <p class="mv0">How would you like to refer to this connection in this pipe?</p>
        <el-input
          placeholder="Alias"
          v-model="$data['alias']"
        />
      </el-form-item>
    </el-form>

  </div>
</template>

<script>
  import marked from 'marked'
  import BuilderItemConnectionChooser from './BuilderItemConnectionChooser.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: true
      }
    },
    components: {
      BuilderItemConnectionChooser
    },
    watch: {
      '$data': {
        handler: 'onChange',
        deep: true
      }
    },
    data() {
      var form_values = _.get(this.item, 'form_values', {})

      return _.assign({
        op: 'connect',
        connection: '',
        alias: ''
      }, form_values)
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Connect')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      }
    },
    methods: {
      onChange: _.debounce(function() {
        this.$emit('item-change', this.$data, this.index)
      }, 50)
    }
  }
</script>
