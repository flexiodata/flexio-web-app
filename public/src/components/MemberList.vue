<template>
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <spinner size="large" message="Loading members..."></spinner>
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
      @delete="onItemDelete"
    >
    </member-item>
  </div>
</template>

<script>
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
        type: String
      }
    },
    mixins: [CommonFilter],
    components: {
      Spinner,
      MemberItem,
      EmptyItem
    },
    computed: {
      members() {
        return this.commonFilter(this.getOurMembers(), this.filter, ['first_name', 'last_name', 'user_name'])
      },
      is_fetching() {
        return false
      }
    },
    methods: {
      ...mapGetters([
        'getAllUsers'
      ]),
      getOurMembers() {
        var object_eid = this.objectEid

        // NOTE: it's really important to include the '_' on the same line
        // as the 'return', otherwise JS will return without doing anything
        return _
          .chain(this.getAllUsers())
          .filter(function(m) { return _.includes(_.get(m, 'object_eid', []), object_eid ) })
          .value()
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>
