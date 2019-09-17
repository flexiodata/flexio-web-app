<template>
  <div class="pa5">
    <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box trans-mw">
      <BuilderItemForm
        class="mb5"
        :key="prompt.name"
        :item="prompt"
        :form-errors.sync="item_form_errors[index]"
        :visible="index == active_idx"
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
          :disabled="!is_next_allowed"
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

  const getDefaultState = (def) => {

    return {
      def,
      is_next_allowed: true,
      item_form_errors: _.map(def.prompts, p => {}),
      active_idx: 0,
    }
  }

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      BuilderItemForm
    },
    data() {
      return getDefaultState(test_def)
    },
    computed: {
      is_last_item() {
        return this.active_idx == this.def.prompts.length - 1
      }
    },
    methods: {
      onBackClick() {
        this.is_next_allowed = true
        this.active_item_errors = {}
        this.active_idx--
      },
      onNextClick() {
        this.is_next_allowed = true
        this.active_item_errors = {}
        this.active_idx++
      }
    }
  }
</script>
