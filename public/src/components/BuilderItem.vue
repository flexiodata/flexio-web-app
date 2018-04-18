<template>
  <div
    class="flex flex-row"
    :class="cls"
    :id="item.id"
  >
    <div class="flex-none pv4 ph3">
      {{index+1}}.
    </div>
    <div
      class="flex-fill bg-white pa4"
      :class="content_cls"
    >
      <div>
        <span class="silver">
          {{item.ui}}
          <span v-if="item.variable">:</span>
        </span>
        {{item.variable}}
      </div>
      <div class="mt3 nr3 nb3 flex flex-row justify-end" v-if="is_active">
        <el-button
          class="ttu b"
          type="plain"
          @click="$store.commit('BUILDER__GO_PREV_ITEM')"
          v-show="!is_first"
        >
          Back
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          @click="$store.commit('BUILDER__GO_NEXT_ITEM')"
          v-show="!is_last"
        >
          Next
        </el-button>
        <el-button
          class="ttu b"
          type="primary"
          @click="finishClick"
          v-show="is_last"
        >
          Finish
        </el-button>
      </div>
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
        }
      },
      content_cls() {
        return {
          'b--black-10': true,
          'bl br': !this.is_first && !this.is_last,
          'bl br bt br2 br--top': this.is_first,
          'bl br bb br2 br--bottom': this.is_last,
          'relative z-2 css-active': this.is_active,
          'o-40 no-pointer-events': !this.is_active
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
    transition: all 0.15s ease;
  }
</style>
