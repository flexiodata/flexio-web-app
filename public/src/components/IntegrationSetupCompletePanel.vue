<template>
  <div>
    <p class="center mw7">You're all set and ready to go. Here's a set of example spreadsheets and other resources to help get you started. If you need anything, please let us know!</p>

    <h3 class="mv4 tc">Select an example sheet</h3>
    <IntegrationTemplateList
      :key="t.name"
      :integration-name="t.name"
      :integration-icon="t.icon"
      :templates="t.templates"
      @template-click="onTemplateClick"
      v-for="t in our_integrations"
    />

    <div class="h2"></div>
    <h3 class="mv4 tc">Get additional resources</h3>
    <div class="flex-l flex-row-l flex-wrap-l nl3 nr3">
      <a
        class="flex-fill flex flex-row db ma3 pv3 ph4 br2 fw4 dark-gray no-underline w-third-l last-step-item"
        target="_blank"
        :href="item.link"
        :key="item.title"
        v-for="item in last_step_items"
      >
        <div>
          <h3 class="mt2 mb0 fw6">{{item.title}}</h3>
          <p class="flex-fill f6 lh-copy mv3">{{item.description}}</p>
          <div class="mb3">
            <span class="flex flex-row items-center blue fw6">
              <span>View</span>
              <i class="material-icons ml1">arrow_right_alt</i>
            </span>
          </div>
        </div>
      </a>
    </div>

    <div class="h2"></div>
    <h3 class="mv4 tc">Get started with the Flex.io App</h3>
    <p class="center mw7">Go off road. Access just about any data with your own code and share integrations with your team without sharing your credentials.</p>
    <div class="mv4 tc">
      <button
        class="db dib-ns pv3 ph5 tc b ttu blue br2 ba b--blue hover-bg-blue hover-white"
        @click="$emit('open-app-click')"
      >
        Open the Flex App
      </button>
    </div>
    <p class="tc">Want some code examples? Fork one of our open source function packs from GitHub: <a class="ml1 v-mid" href="https://github.com/flexiodata?utf8=%E2%9C%93&q=functions" target="_blank" title="View function packs on Github"><svg xmlns="http://www.w3.org/2000/svg" fill="black" width="32" height="32" viewBox="0 0 16 16"><path d="M8 .198c-4.418 0-8 3.582-8 8 0 3.535 2.292 6.533 5.47 7.59.4.075.548-.173.548-.384 0-.19-.008-.82-.01-1.49-2.227.485-2.696-.943-2.696-.943-.364-.924-.888-1.17-.888-1.17-.726-.497.055-.486.055-.486.802.056 1.225.824 1.225.824.714 1.223 1.872.87 2.328.665.072-.517.28-.87.508-1.07-1.776-.202-3.644-.888-3.644-3.954 0-.874.313-1.588.824-2.148-.083-.202-.357-1.015.077-2.117 0 0 .672-.215 2.2.82.64-.177 1.323-.266 2.003-.27.68.004 1.365.093 2.004.27 1.527-1.035 2.198-.82 2.198-.82.435 1.102.162 1.916.08 2.117.512.56.822 1.274.822 2.147 0 3.072-1.872 3.748-3.653 3.946.288.248.544.735.544 1.48 0 1.07-.01 1.933-.01 2.196 0 .213.145.462.55.384 3.178-1.06 5.467-4.057 5.467-7.59 0-4.418-3.58-8-8-8z"></path></svg></a></p>

    <p class="tc">
      <span>Need help with a script?</span>
      <el-button type="text" style="font-size: 100%" @click="onNeedHelpClick">Let us know and we'll point you in the right direction!</el-button>
    </p>
  </div>
</template>

<script>
  import IntegrationTemplateList from '@/components/IntegrationTemplateList'

  const quick_start_templates = {
    name: 'quick-start',
    icon: 'https://static.flex.io/assets/logos2/quick-start.png',
    templates: [{
       name: 'flexio-quick-start-guide',
       title: 'Flex.io quick start spreadsheet',
       description: 'Explore a set of examples for using Flex.io with this spreadsheet guide',
       gsheets_spreadsheet_id: '14samTw-5MJ8IkBy89LIu4i2K4bshJ-qNKRJpY5mp8SU',
       excel_spreadsheet_path: 'https://static.flex.io/templates/quick-start/flexio-quick-start-guide.xlsx',
       is_public: true,
       is_private: false
    }]
  }

  const last_step_items = [{
    title: 'Getting Started Guide',
    description: 'Learn how to work with Flex.io in your spreadsheet',
    link: 'https://www.flex.io/resources/getting-started'
  },{
    title: 'Basic Examples',
    description: 'Explore some basic examples you can try immediately',
    link: 'https://www.flex.io/resources/examples'
  },{
    title: 'Explore Integrations',
    description: 'See how you can use Flex with other web services',
    link: 'https://www.flex.io/explore'
  }]

  const getDefaultState = () => {
    return {
      quick_start_templates,
      last_step_items
    }
  }

  export default {
    props: {
      integrationInfo: {
        type: Array, // array of objects with structure { icon, templates }
        default: () => []
      }
    },
    components: {
      IntegrationTemplateList
    },
    data() {
      return getDefaultState()
    },
    computed: {
      our_integrations() {
        return [].concat(this.quick_start_templates).concat(this.integrationInfo)
      }
    },
    methods: {
      onTemplateClick(template, integration_name) {
        this.$emit('template-click', template, integration_name)
      },
      onNeedHelpClick() {
        if (window.Intercom) {
          window.Intercom('showNewMessage')
        }
      },
    }
  }
</script>

<style lang="stylus" scoped>
  .last-step-item
    background-color: rgba(255,255,255,0.2)
    box-shadow: 0 8px 24px -1px rgba(0,0,0,0.075)
    transition: all 0.2s ease-out
    &:hover
      box-shadow: 0 8px 24px 0 rgba(0,0,0,0.2)
</style>
