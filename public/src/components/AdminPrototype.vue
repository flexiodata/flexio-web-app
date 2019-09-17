<template>
  <div class="pa5">
    <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box trans-mw">
      <BuilderItemForm
        class="mb5"
        :key="prompt.name"
        :item="prompt"
        v-show="index == active_idx"
        v-for="(prompt, index) in def.prompts"
      />
      <div class="mt4 w-100 flex flex-row justify-end">
        <el-button
          class="ttu fw6"
          @click="onBackClick"
          v-show="active_idx > 0"
        >
          Back
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="onNextClick"
        >
          {{is_last_item ? 'Done' : 'Next'}}
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import test_def from '../data/builder/test2-def.yml'
  import BuilderItemForm from '@/components/BuilderItemForm'

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      BuilderItemForm
    },
    data() {
      return {
        def: test_def,
        active_idx: 0
      }
    },
    computed() {
      is_last_item() {
        return this.active_idx == this.def.prompts.length - 1
      }
    },
    methods: {
      onBackClick() {
        this.active_idx--
      },
      onNextClick() {
        this.active_idx++
      }
    }
  }
</script>
