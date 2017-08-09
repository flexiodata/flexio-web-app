<template>
  <div class="flex flex-row items-center cursor-default">
    <router-link
      v-if="show_breadcrumbs"
      to="/home"
      class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns link b--black-10 bl mid-gray hover-black"
    ><i class="material-icons">home</i>
    </router-link>
    <i v-if="show_breadcrumbs" class="material-icons md-24 black-20 rotate-270">arrow_drop_down</i>
    <inline-edit-text
      v-if="show_breadcrumbs && allowDocumentNameEdit"
      class="mid-gray"
      placeholder-cls="black-40 hover-mid-gray"
      static-cls="ba b--transparent hover-b--light-gray cursor-text"
      placeholder="Name"
      input-key="name"
      :val="document_name"
      :show-edit-button="false"
    ></inline-edit-text>
    <div
      v-else-if="show_breadcrumbs"
      class="mid-gray"
    >{{document_name}}</div>
  </div>
</template>

<script>
  import { ROUTE_ACCOUNT, ROUTE_PIPEHOME } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'
  import InlineEditText from './InlineEditText.vue'

  export default {
    props: {
      'allow-document-name-edit': {
        type: Boolean,
        default: false
      }
    },
    components: {
      InlineEditText
    },
    computed: {
      ...mapState([
        'active_document_eid'
      ]),
      show_breadcrumbs() {
        return this.$route.name == ROUTE_ACCOUNT || this.$route.name == ROUTE_PIPEHOME
      },
      document_name() {
        if (this.$route.name == ROUTE_ACCOUNT)
          return 'Account'

        return _.get(this.getActiveDocument(), 'name', '')
      }
    },
    methods: {
      ...mapGetters([
        'getActiveDocument'
      ])
    }
  }
</script>
