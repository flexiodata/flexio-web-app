<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner message="Loading..."></spinner>
    </div>
  </div>
  <empty-item v-else-if="members.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">person</i>
    <span slot="text">No members match the filter criteria</span>
  </empty-item>
  <empty-item v-else-if="members.length == 0">
    <i slot="icon" class="material-icons">person</i>
    <span slot="text">No members to show</span>
  </empty-item>
  <div v-else>
    <member-item
      v-for="(member, index) in members"
      :item="member"
      :index="index"
      :owner-eid="ownerEid"
      :key="member.eid"
    ></member-item>
  </div>
</template>

<script>
  import api from '../api'
  import { mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import MemberItem from './MemberItem.vue'
  import EmptyItem from './EmptyItem.vue'
  import CommonFilter from './mixins/common-filter'

  export default {
    props: {
      'filter': {
        type: String,
        default: ''
      },
      'object-eid': {
        type: String,
        default: ''
      },
      'owner-eid': {
        type: String,
        default: ''
      }
    },
    mixins: [CommonFilter],
    components: {
      Spinner,
      MemberItem,
      EmptyItem
    },
    data() {
      return {
        is_fetching: false
      }
    },
    computed: {
      members() {
        return this.getOurRights()
      }
    },
    mounted() {
      this.tryFetchRights()
    },
    methods: {
      ...mapGetters([
        'getAllRights'
      ]),
      tryFetchRights() {
        if (_.size(this.objectEid) > 0 && _.size(this.getOurRights()) == 0)
        {
          this.is_fetching = true
          this.$store.dispatch('fetchRights', { objects: this.objectEid }).then(response => {
            this.is_fetching = false
          })
        }
      },
      getOurRights() {
        var rights_items = this.getAllRights()
        rights_items = rights_items.concat([{
          'object_eid': this.objectEid,
          'access_code': 'public',
          'access_type': 'CAT',
          'actions': []
        }])

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(rights_items)
          .filter(r => { return _.get(r, 'object_eid', '') === this.objectEid })
          .sortBy([
            r => { return _.get(r, 'access_code') == 'public' },
            r => { return _.get(r, 'access_code') == this.ownerEid },
            r => { return new Date(r.created) }
          ])
          .reverse()
          .value()
      }
    }
  }
</script>
