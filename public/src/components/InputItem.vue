<template>
  <tr
    class="cursor-default darken-10"
    :class="[is_active?'bg-light-gray':'']"
    @click="onClick"
  >
    <td v-if="showCheckbox" class="ph2 pv2a bb b--black-20 truncate w1"><image-checkbox :checked="false"></image-checkbox></td>
    <td class="ph2 pv2a bb b--black-20 truncate mw5">{{name}}</td>
    <td v-show="fullList" class="ph2 pv2a bb b--black-20 truncate mw5">{{item.path}}</td>
  </tr>
</template>

<script>
  import ImageCheckbox from './ImageCheckbox.vue'

  export default {
    props: ['item', 'active-item', 'show-checkbox', 'full-list'],
    components: {
      ImageCheckbox
    },
    computed: {
      name() {
        var name = _.get(this.item, 'name', '')
        name = name.length > 0 ? name : _.get(this.item, 'path', '')
        name = name.length > 0 ? name : _.get(this.item, 'eid', '')
        return name
      },
      is_active() {
        return this.activeItem == this.item.eid
      }
    },
    methods: {
      onClick() {
        this.$emit('activate', this.item)
      }
    }
  }
</script>
