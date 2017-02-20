<template>
  <div @click.stop="click" @dblclick.stop="dblclick" class="pointer no-select">
    <i v-show="is_checked" class="material-icons md-18 dib v-mid blue">check_box</i>
    <i v-show="!is_checked" class="material-icons md-18 dib v-mid f4 black-60">check_box_outline_blank</i>
    <input :id="uid" :checked="is_checked !== undefined" type="checkbox">
    <label :for="uid"></label>
  </div>
</template>

<script>
  export default {
    props: ['checked'],
    data() {
      return {
        is_checked: this.checked
      }
    },
    computed: {
      uid() { return _.uniqueId('checkbox-'); }
    },
    methods: {
      click: function(evt) {
        this.is_checked = !this.is_checked
        this.$emit('click', evt)
      },
      dblclick: function(evt) {
        this.$emit('dblclick', evt)
      }
    }
  }
</script>

<style lang="less">
  input[type="checkbox"] {
    display: none;
  }
</style>
