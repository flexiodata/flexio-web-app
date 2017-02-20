<template>
  <article
    class="dib mw5 h4 w4 center bg-white br2 pa1 ma2 v-top darken-10"
    @click="onClick"
  >
    <div class="tc css-valign">
      <connection-icon :type="item.connection_type" class="dib v-mid br2 fx-square-5"></connection-icon>
      <div class="mid-gray f6 fw6 mt2 cursor-default">{{item.service_name}}</div>
    </div>
  </article>
</template>

<script>
  import ConnectionIcon from './ConnectionIcon.vue'

  export default {
    props: ['item'],
    components: {
      ConnectionIcon
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
