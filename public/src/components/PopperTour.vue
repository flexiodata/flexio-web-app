<template>
  <div class="vue-popper-tour">
    <slot
      :current-step="current_step"
      :steps="steps"
      :prev-click="prevClick"
      :next-click="nextClick"
      :skip-click="skipClick"
      :done-click="doneClick"
      :jump-click="jumpClick"
      :is-first-step="is_first_step"
      :is-last-step="is_last_step"
    >
      <PopperStep
        :key="index"
        :index="index"
        :steps="steps"
        :prev-click="prevClick"
        :next-click="nextClick"
        :skip-click="skipClick"
        :done-click="doneClick"
        :jump-click="jumpClick"
        :is-first-step="is_first_step"
        :is-last-step="is_last_step"
        v-bind="step"
        v-for="(step, index) of steps"
        v-if="current_step === index"
      />
    </slot>
  </div>
</template>

<script>
  import PopperStep from './PopperStep.vue'

  const DEFAULT_CALLBACKS = {
    onSkip: () => {},
    onStart: () => {},
    onFinish: () => {},
    onJump: () => {},
    onPrevStep: (current_step) => {},
    onNextStep: (current_step) => {}
  }

  const DEFAULT_OPTIONS = {
    start_timeout: 0
  }

  export default {
    name: 'vue-popper-tour',
    inheritAttrs: false,
    props: {
      name: {
        type: String
      },
      autoStart: {
        type: Boolean,
        default: true
      },
      steps: {
        type: Array,
        default: () => []
      },
      options: {
        type: Object,
        default: () => { return DEFAULT_OPTIONS }
      },
      callbacks: {
        type: Object,
        default: () => { return DEFAULT_CALLBACKS }
      }
    },
    components: {
      PopperStep
    },
    data() {
      return {
        current_step: -1
      }
    },
    mounted() {
      if (this.autoStart === true) {
        this.start()
      }
    },
    computed: {
      custom_options() {
        return {
          ...DEFAULT_OPTIONS,
          ...this.options
        }
      },
      custom_callbacks() {
        return {
          ...DEFAULT_CALLBACKS,
          ...this.callbacks
        }
      },
      is_running() {
        return this.current_step > -1 && this.current_step < this.step_count
      },
      is_first_step() {
        return this.current_step === 0
      },
      is_last_step() {
        return this.current_step === this.steps.length - 1
      },
      step_count() {
        return this.steps.length
      }
    },
    methods: {
      start(start_step) {
        setTimeout(() => {
          this.custom_callbacks.onStart()
          this.current_step = typeof start_step !== 'undefined' ? parseInt(start_step, 10) : 0
        }, this.custom_options.start_timeout)
      },
      prevClick() {
        if (this.current_step > 0) {
          this.custom_callbacks.onPrevStep(this.current_step)
          this.current_step--
        }
      },
      nextClick() {
        if (this.current_step < this.step_count - 1 && this.current_step !== -1) {
          this.custom_callbacks.onNextStep(this.current_step)
          this.current_step++
        }
      },
      skipClick() {
        this.custom_callbacks.onSkip()
        this.current_step = -1
      },
      doneClick() {
        this.custom_callbacks.onFinish()
        this.current_step = -1
      },
      jumpClick(idx) {
        this.custom_callbacks.onJump()
        this.current_step = idx
      }
    }
  }
</script>
