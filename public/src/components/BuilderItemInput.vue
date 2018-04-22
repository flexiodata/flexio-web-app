<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">Choose value</h3>
    </div>
    <div v-show="is_active">
      <label class="db mb2">{{item.msg}}</label>
      <el-input v-model="input_val" />
    </div>
    <div v-show="is_before_active">
      <div class="mb2 bt b--black-10"></div>
      <span>{{item.variable}}:</span> <span class="b">{{input_val}}</span>
      <div class="mt2 bt b--black-10"></div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

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
    computed: {
      ...mapState({
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_before_active() {
        return this.index < this.active_prompt_idx
      },
      input_val: {
        get() {
          return _.get(this.$store, 'state.builder.prompts[' + this.index + '].value')
        },
        set(value) {
          this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { value })
        }
      }
    }
  }
</script>
