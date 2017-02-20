<template>
  <table class="collapse f6 w-100">
    <thead>
      <tr class="fw6 tl">
        <th v-if="showCheckbox" class="ph2 pv2 bb bw1 b--black-20 w1"><image-checkbox :checked="false" @click="onHeaderCheckboxClick"></image-checkbox></th>
        <th class="ph2 pv2 bb bw1 b--black-20 mw5">Name</th>
        <th v-show="fullList" class="ph2 pv2 bb bw1 b--black-20 mw5">Path</th>
      </tr>
    </thead>
    <tbody>
      <input-item
        v-for="(input, index) in inputs"
        :item="input"
        :index="index"
        :show-checkbox="showCheckbox"
        :full-list="fullList"
        :active-item="active_item"
        @activate="onItemActivate"
      >
      </input-item>
    </tbody>
  </table>
</template>

<script>
  import ImageCheckbox from './ImageCheckbox.vue'
  import InputItem from './InputItem.vue'

  export default {
    props: ['inputs', 'show-checkbox', 'full-list'],
    components: {
      ImageCheckbox,
      InputItem
    },
    data() {
      return {
        active_item: ''
      }
    },
    created() {
      this.ensureActiveItem()
    },
    updated() {
      this.ensureActiveItem()
    },
    methods: {
      hasActiveItem() {
        if (!this.active_item)
          return false

        var item = _.find(this.inputs, { eid: this.active_item })
        return !_.isNil(item)
      },

      ensureActiveItem() {
        if (!this.hasActiveItem())
        {
          var first_item = _.first(this.inputs)

          if (_.isObject(first_item))
          {
            this.active_item = _.get(first_item, 'eid', '')
            this.$emit('item-activate', first_item)
          }
        }
      },

      onHeaderCheckboxClick() {
        console.log('Header checkbox clicked')
      },

      onItemActivate: function(item) {
        this.active_item = item.eid
        this.$emit('item-activate', item)
      }
    }
  }
</script>
