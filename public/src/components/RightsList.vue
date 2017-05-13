<template>
  <div>
    <table class="dt--fixed">
      <thead>
        <tr>
          <th class="pa1 f6 fw6 dark-gray tl"></th>
          <th class="pa1 f6 fw6 dark-gray tc" v-for="(action, index) in actions">{{action.name}}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(right, role) in rights">
          <td class="pa1 f6 dark-gray">{{getMemberTypeLabel(role)}}</td>
          <td class="pa1 f6 dark-gray tc" v-for="(val, key) in right">
            <input
              type="checkbox"
              :checked="val"
              :disabled="isOwner(role)"
              @change="toggleRight(role, key, val)">
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  import { MEMBER_TYPE_OWNER } from '../constants/member-type'
  import * as members from '../constants/member-info'
  import * as actions from '../constants/action-info'

  export default {
    props: {
      'object': {
        type: Object,
        required: true
      }
    },
    watch: {
      'object': function(val, old_val) {
        this.rights = _.cloneDeep(_.get(val, 'rights', {}))
      }
    },
    data() {
      return {
        rights: _.cloneDeep(_.get(this.object, 'rights', {}))
      }
    },
    computed: {
      actions() {
        return actions
      },
      members() {
        return members
      }
    },
    methods: {
      getActionTypeLabel(type) {
        return _.get(_.find(this.actions, { type }), 'name', '')
      },
      getMemberTypeLabel(type) {
        return _.get(_.find(this.members, { type }), 'name', '')
      },
      getValueString(val) {
        return val ? "allowed" : "not allowed"
      },
      isOwner(role) {
        console.log(role)
        return role == MEMBER_TYPE_OWNER
      },
      toggleRight(role, key, val) {
        var new_rights = _.assign({}, this.rights)
        _.set(new_rights, role+'.'+key, !val)
        this.rights = _.assign({}, new_rights)

        this.$emit('change', this.rights)
      }
    }
  }
</script>
