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
        :show-dots="showDots"
        :allow-jump="allowJump"
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
  import PopperStep from '@/components/PopperStep'

  const DEFAULT_CALLBACKS = {
    onStart: () => {},
    onFinish: (callback) => { callback(true) },
    onSkip: (callback) => { callback(true) },
    onJump: () => {},
    onPrevStep: (current_step, callback) => { callback(true) },
    onNextStep: (current_step, callback) => { callback(true) }
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
      showDots: {
        type: Boolean,
        default: true
      },
      allowJump: {
        type: Boolean,
        default: false
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
      our_options() {
        return {
          ...DEFAULT_OPTIONS,
          ...this.options
        }
      },
      our_callbacks() {
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
          this.our_callbacks.onStart()
          this.current_step = typeof start_step !== 'undefined' ? parseInt(start_step, 10) : 0
        }, this.our_options.start_timeout)
      },
      prevClick() {
        if (this.current_step > 0) {
          this.our_callbacks.onPrevStep(this.current_step, (allowed) => {
            if (!!allowed) {
              this.current_step--
            }
          })
        }
      },
      nextClick() {
        if (this.current_step < this.step_count - 1 && this.current_step !== -1) {
          this.our_callbacks.onNextStep(this.current_step, (allowed) => {
            if (!!allowed) {
              this.current_step++
            }
          })
        }
      },
      skipClick() {
        this.our_callbacks.onSkip((allowed) => {
          if (!!allowed) {
            this.current_step = -1
          }
        })
      },
      doneClick() {
        this.our_callbacks.onFinish((allowed) => {
          if (!!allowed) {
            this.current_step = -1
          }
        })
      },
      jumpClick(idx) {
        this.our_callbacks.onJump()
        this.current_step = idx
      }
    }
  }
</script>
