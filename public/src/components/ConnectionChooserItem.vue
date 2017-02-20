<template>
  <article
    class="dib mw5 h4 w4 br2 pa1 mv2 mh1 v-top darken-10"
    :class="cls"
    @click="onClick"
  >
    <div class="tc css-valign">
      <connection-icon :type="item.connection_type" class="dib v-mid br2 fx-square-5"></connection-icon>
      <div class="mid-gray f6 fw6 mt2 cursor-default">{{item.name}}</div>
    </div>
  </article>
</template>

<script>
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item', 'connection-type'],
    components: {
      ConnectionIcon
    },
    computed: {
      is_selected() {
        return this.connectionType == _.get(this.item, 'connection_type')
      },
      cls() {
        return this.is_selected ? 'bg-black-10' : 'bg-white'
      }
    },
    methods: {
      onClick() {
        this.$emit('activate', this.item)
      }
    }
  }
</script>

<style lang="less">
  .css-valign {
    position: relative;
    top: 50%;
    transform: translateY(-50%);
  }
</style>
