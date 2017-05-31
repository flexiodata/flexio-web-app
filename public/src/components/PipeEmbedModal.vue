<template>
  <ui-modal
    ref="dialog"
    size="large"
    @hide="onHide"
  >
    <div slot="header" class="f4">Embed '{{pipe.name}}'</div>

    <div class="flex flex-row">
      <input
        type="text"
        ref="linkinput"
        class="flex-fill input-reset lh-copy pa2 bt bb bl b--black-20 br1 br--left"
        spellcheck="false"
        v-deferred-focus
        v-select-all
        v-model="embed"
        :id="input_id"
        @focus="selectAll"
      >
      <btn
        btn-primary
        btn-square
        class="pv2a ph3 hint--bottom-left br1 br--right clipboardjs"
        aria-label="Copy to Clipboard"
        :data-clipboard-target="'#'+input_id"
      ><span class="ttu b">Copy</span></btn>
    </div>
    <div class="fw6 black-60 mt4 mb1">Preview:</div>
    <div v-html="embed"></div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  const defaultAttrs = () => {
    return {
      eid: null,
      name: ''
    }
  }

  export default {
    components: {
      Btn
    },
    data() {
      return {
        input_id: _.uniqueId('input-'),
        pipe: _.extend({}, defaultAttrs())
      }
    },
    computed: {
      embed_src() {
        var loc = window.location
        return loc.protocol+'//'+loc.host+'/app/embed/'+this.pipe.eid
      },
      embed() {
        var w = '100%'
        var h = '80'

        return '' +
          '<iframe ' +
            'src="'+this.embed_src+'" ' +
            'width="'+w+'" ' +
            'height="'+h+'" ' +
            'frameborder="0" ' +
            // we have to set the CSS height here since the stylesheet that VueMaterial
            // imposes on us sets all iframe heights to 'auto'
            'style="border:0; height: '+h+'px" ' +
            'allowfullscreen' +
          '></iframe>'
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
        this.pipe = _.extend({}, defaultAttrs(), attrs)
      },
      selectAll() {
        var el = this.$refs['linkinput']

        setTimeout(function() {
          el.selectionStart = 0
          el.selectionEnd = el.value.length
          el.focus()
        }, 10)
      },
      onHide() {
        this.$emit('hide', this)
      }
    }
  }
</script>
