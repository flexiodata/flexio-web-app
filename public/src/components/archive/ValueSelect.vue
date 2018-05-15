<template>
  <div>
    <ui-select
      ref="input"
      :multiple="multiple"
      :label="label"
      :placeholder="placeholder"
      :value="processed_value"
      :keys="keys"
      :options="processed_options"
      @input="update"
      @change="update"
    />
  </div>
</template>

<script>
  export default {
    props: {
      'label': {},
      'placeholder': {},
      'value': {},
      'multiple': {},
      'allow-numbers': {},
      'value-key': {
        type: String,
        default: 'val'
      },
      'keys': {
        type: Object,
        default: () => {
          return { label: 'label', val: 'val' }
        }
      },
      'options': {}
    },
    computed: {
      processed_options() {
        if (!_.isNil(this.allowNumbers))
        {
          var retval = _.map(this.options, (option) => {
            return { label: ''+option, val: option }
          })

          return retval
        }
         else
        {
          return this.options
        }
      },
      processed_value() {
        var me = this

        if (!_.isNil(this.multiple))
        {
          return _.filter(this.processed_options, (option) => {
            return _.includes(me.value, _.get(option, me.valueKey))
          })
        }
         else
        {
          return _.find(this.processed_options, [this.valueKey, this.value])
        }
      }
    },
    methods: {
      update(value) {
        var me = this
        var val

        if (!_.isNil(this.multiple))
        {
          val = _.map(value, (v) => {
            return _.get(v, me.valueKey, '')
          })
        }
         else
        {
          val = _.get(value, this.valueKey, '')
        }

        this.$emit('input', val)
        this.$emit('change', val)
      }
    }
  }
</script>
