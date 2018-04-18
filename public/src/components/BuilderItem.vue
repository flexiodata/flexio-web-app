<template>
  <div
    :id="item.id"
    :class="cls"
  >
    <div>
      {{index+1}}.
      <span class="silver">
        {{item.ui}}
        <span v-if="item.variable">:</span>
      </span>
      {{item.variable}}
    </div>
    <div class="flex flex-row justify-end" v-if="is_active">
      <el-button
        size="small"
        class="ttu b"
        type="plain"
        @click="$store.commit('BUILDER__GO_PREV_ITEM')"
        v-show="!is_first"
      >
        Back
      </el-button>
      <el-button
        size="small"
        class="ttu b"
        type="primary"
        @click="$store.commit('BUILDER__GO_NEXT_ITEM')"
        v-show="!is_last"
      >
        Next
      </el-button>
      <el-button
        size="small"
        class="ttu b"
        type="primary"
        @click="finishClick"
        v-show="is_last"
      >
        Finish
      </el-button>
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
        prompts: state => state.builder.prompts,
        active_prompt: state  => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      is_first() {
        return this.index == 0
      },
      is_last() {
        return this.index == this.prompts.length - 1
      },
      is_active() {
        return this.index == this.active_prompt_idx
      },
      cls() {
        return {
          'pa3': true,
          'mv3': !this.is_first && !this.is_last,
          'mb3': this.is_first,
          'mt3': this.is_last,
          'bg-white': this.is_active,
          'css-active': this.is_active
        }
      }
    },
    methods: {
      finishClick() {
        alert('Finished!')
      }
    }
  }
</script>

<style scoped>
  .css-active {
    box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2);
  }
</style>
