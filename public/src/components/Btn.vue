<template>
  <button type="button" @click="click" class="border-box no-select" :class="cls">
    <slot></slot>
  </button>
</template>

<script>
  export default {
    props: {
      'btn-xs': {},
      'btn-sm': {},
      'btn-md': {},
      'btn-lg': {},
      'btn-block': {},
      'btn-square': {},
      'btn-cta': {},
      'btn-default': {},
      'btn-primary': {},
      'btn-danger': {},
      'btn-outline': {},
      'btn-hover': { default: true }
    },
    computed: {
      cls() {
        var outline = false
        var prefix = 'bg-'
        var cls = []

        if (this.btnOutline !== undefined)
          outline = true

        if (this.btnXs !== undefined)
          cls = cls.concat(['f6'])
         else if (this.btnSm !== undefined)
          cls = cls.concat(['f6','ph2','pv1'])
         else if (this.btnMd !== undefined)
          cls = cls.concat(['f6','ph3','pv2'])
         else if (this.btnLg !== undefined)
          cls = cls.concat(['ph4','pv2a','lh-title'])

        if (this.btnBlock !== undefined)
          cls = cls.concat(['db w-100'])

        if (outline)
        {
          cls.push('ba')
          prefix = ''
        }

        if (this.btnSquare === undefined)
          cls = cls.concat('br1')

        if (this.btnCta !== undefined)
          cls = cls.concat(['white',prefix+'orange','b--orange'])

        if (this.btnDefault !== undefined)
          cls = cls.concat(['black-60',prefix+'light-gray','b--light-gray'])

        if (this.btnPrimary !== undefined)
          cls = cls.concat(['white',prefix+'blue','b--blue'])

        if (this.btnDanger !== undefined)
          cls = cls.concat(['white',prefix+'dark-red','b--dark-red'])

        if (this.btnHover !== undefined)
          cls = cls.concat(['darken-10'])

        return cls.join(' ')
      }
    },
    methods: {
      click: function(evt) {
        this.$emit('click', evt)
      }
    }
  }
</script>
