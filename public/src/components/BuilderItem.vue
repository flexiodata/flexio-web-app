<template>
  <div
    class="pa2 mv2"
    :class="cls"
    :style="styl"
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
        size="mini"
        class="ttu b"
        type="plain"
        @click="$store.commit('BUILDER__GO_PREV_ITEM')"
        v-show="index > 0"
      >
        Back
      </el-button>
      <el-button
        size="mini"
        class="ttu b"
        type="primary"
        @click="$store.commit('BUILDER__GO_NEXT_ITEM')"
        v-show="index < prompts.length - 1"
      >
        Next
      </el-button>
      <el-button
        size="mini"
        class="ttu b"
        type="primary"
        @click="finishClick"
        v-show="index == prompts.length - 1"
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
      is_active() {
        return this.index == this.active_prompt_idx
      },
      cls() {
        return this.is_active ? 'bg-white' : ''
      },
      styl() {
        return this.is_active ? 'box-shadow: 0 4px 24px -4px rgba(0,0,0,0.2)' : ''
      }
    },
    methods: {
      finishClick() {
        alert('Finished!')
      }
    }
  }
</script>
