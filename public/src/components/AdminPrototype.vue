<template>
  <AdminBuilderUi
    class="pa5 overflow-y-scroll"
    :prompts="setup_template.prompts"
  />
</template>

<script>
  //import hubspot_setup_template from '@/data/builder/hubspot-example.yml'
  import setup_template from '@/data/builder/docusign-example.yml'
  import AdminBuilderUi from '@/components/AdminBuilderUi'

  const getDefaultState = (setup_template) => {
    return {
      setup_template
    }
  }

  export default {
    metaInfo: {
      title: '[Admin] Prototype'
    },
    components: {
      AdminBuilderUi
    },
    data() {
      return getDefaultState(setup_template)
    }
  }
</script>
