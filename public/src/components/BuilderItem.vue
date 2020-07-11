<template>
  <component
    :is="component_name"
    :item="item"
    v-bind="$attrs"
    v-on="$listeners"
    v-if="is_valid_component_name"
  />
  <div
    v-else
  >
    <em>Invalid element</em>
  </div>
</template>

<script>
  import BuilderItemAuth from '@/components/BuilderItemAuth'
  import BuilderItemFileChooser from '@/components/BuilderItemFileChooser'
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
      BuilderItemFileChooser,
      BuilderItemForm
    },
    computed: {
      component_name() {
        var element = this.item.element
        element = _.startCase(element)
        element = element.replace(/\s/g, '')
        return 'BuilderItem' + element
      },
      is_valid_component_name() {
        var name = _.get(this.item, 'element', '')
        return _.includes(['auth', 'file-chooser', 'form'], name)
      }
    }
  }
</script>
