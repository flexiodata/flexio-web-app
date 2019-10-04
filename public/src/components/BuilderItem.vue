<template>
  <component
    :is="component_name"
    :item="item"
    v-bind="$attrs"
    v-on="$listeners"
  />
</template>

<script>
  import BuilderItemAuth from '@/components/BuilderItemAuth'
  import BuilderItemForm from '@/components/BuilderItemForm'

  export default {
    inheritAttrs: false,
    props: {
      item: {
        type: Object,
        required: true
      },
    },
    components: {
      BuilderItemAuth,
      BuilderItemForm
    },
    computed: {
      component_name() {
        var element = this.item.element
        element = _.startCase(element)
        element = element.replace(/\s/g, '')
        return 'BuilderItem' + element
      },
    }
  }
</script>
