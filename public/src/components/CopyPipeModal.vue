<template>
  <ui-modal
    ref="dialog"
    size="large"
    @open="onOpen"
    @hide="onHide"
  >
    <div slot="header" class="w-100">
      <div class="flex flex-row items-center">
        <div class="flex-fill">
          <span class="f4">Copy Pipe</span>
        </div>
      </div>
    </div>

    <div class="flex flex-column">
      <textarea
        type="text"
        ref="json-textarea"
        class="flex-fill input-reset lh-copy pa2 ba b--black-10"
        style="height: 400px"
        spellcheck="false"
        v-deferred-focus
        v-model.trim="pipe_json"
        :id="textarea_id"
      ></textarea>
      <btn
        btn-md
        btn-primary
        btn-square
        class="br1 mt3 hint--top clipboardjs"
        aria-label="Copy to Clipboard"
        :data-clipboard-target="'#'+textarea_id"
      ><span class="ttu b">Copy</span></btn>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  export default {
    components: {
      Btn
    },
    data() {
      return {
        textarea_id: _.uniqueId('textarea-'),
        pipe: {}
      }
    },
    computed: {
      pipe_json() {
        var json = _.pick(this.pipe, ['name', 'description', 'task'])
        json.task = _.map(_.get(json, 'task', []), (t) => {
          return _.omit(t, 'eid')
        })

        return JSON.stringify(json, null, 2)
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      reset(attrs) {
        this.pipe = _.extend({}, attrs)
      },
      selectNone() {
        var el = this.$refs['json-textarea']

        setTimeout(function() {
          el.selectionStart = 0
          el.selectionEnd = 0
          el.focus()
        }, 10)
      },
      selectAll() {
        var el = this.$refs['json-textarea']

        setTimeout(function() {
          el.selectionStart = 0
          el.selectionEnd = el.value.length
          el.focus()
        }, 10)
      },
      onOpen() {
        this.selectNone()
      },
      onHide() {
        this.$emit('hide', this)
      }
    }
  }
</script>
