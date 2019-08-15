<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center h-100 bg-nearer-white">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- joining -->
  <div
    class="flex-fill flex flex-column bg-nearer-white overflow-y-scroll"
    v-else-if="is_action_join"
  >
    <!-- logged in user is not the same as the invited user -->
    <PageNotFound
      class="flex-fill"
      v-if="!is_joining_member_logged_in"
    />

    <!-- logged in user is the same as the invited user -->
    <div
      class="pa5"
      v-else
    >
      <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box" style="max-width: 36rem">
        <div class="tc">
          <i class="material-icons moon-gray" style="font-size: 4rem">people</i>
          <h3 class="flex-fill mt2 fw6 f3">{{join_title}}</h3>
        </div>
        <div class="mb3" v-if="join_error_msg">
          <div class="el-alert el-alert--error is-light">
            {{join_error_msg}}
          </div>
        </div>
        <p>You've been invited to become a member of the team <strong>"{{active_team_name}}"</strong> on Flex.io. Would you like to join this team?</p>
        <div class="h2"></div>
        <div class="flex flex-row items-center justify-end">
          <el-button
            class="ttu fw6"
            @click="rejectJoinTeam"
          >
            No thanks
          </el-button>
          <el-button
            class="ttu fw6"
            type="primary"
            @click="joinTeam"
          >
            Yes, I want to join
          </el-button>
        </div>
      </div>
    </div>
  </div>

  <!-- fetched -->
  <div
    class="flex flex-column bg-nearer-white overflow-y-scroll"
    v-else-if="is_fetched"
  >
    <div class="pa5">
      <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box overflow-hidden" style="min-height: 20rem">
        <el-alert
          style="margin-bottom: 2rem"
          type="warning"
          show-icon
          title="You are already a member of this team."
          :closable="false"
          v-show="is_already_member"
        />
        <div class="flex flex-row items-start">
          <h3 class="flex-fill mt0 fw6 f3">Team Members</h3>
          <el-button
            class="ttu fw6"
            type="primary"
            @click="show_add_dialog = true"
            v-require-rights:teammember.update
          >
            Add Members
          </el-button>
        </div>
        <table class="el-table w-100 mv3">
          <tbody>
            <MemberItem
              :item="member"
              :key="member.eid"
              v-for="member in members"
            />
          </tbody>
        </table>
      </div>
    </div>

    <el-dialog
      width="42rem"
      top="4vh"
      title="Add Team Members"
      :modal-append-to-body="false"
      :visible.sync="show_add_dialog"
      @open="onAddDialogOpen"
      @close="onAddDialogClose"
    >
      <el-form
        ref="form"
        class="el-form--cozy el-form__label-tiny"
        label-position="top"
        :model="add_dialog_model"
        :rules="add_dialog_rules"
        v-if="show_add_dialog"
      >
        <p class="f5">Enter the email addresses of the people you would like to invite to your team. New team members will get an email with a link to accept the invitation.</p>
        <el-form-item
          key="users"
          prop="users"
          label="Send invites to the following email addresses"
        >
          <el-select
            ref="email-invite-select"
            multiple
            filterable
            allow-create
            default-first-option
            popper-class="dn"
            class="w-100"
            spellcheck="false"
            placeholder="Enter email addresses"
            v-model="add_dialog_model.users"
            v-tag-input
          >
            <el-option
              :label="item.label"
              :value="item.value"
              :key="item.value"
              v-for="item in []"
            />
          </el-select>
        </el-form-item>
      </el-form>
      <div class="mt4 w-100 flex flex-row justify-end">
        <el-button
          class="ttu fw6"
          @click="show_add_dialog = false"
        >
          Cancel
        </el-button>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="sendInvites"
          :disabled="add_dialog_has_errors == true"
        >
          Send Invites
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import { OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'
  import { ROUTE_APP_PIPES } from '@/constants/route'
  import { mapState, mapGetters } from 'vuex'
  import { isValidEmail } from '@/utils'
  import Spinner from 'vue-simple-spinner'
  import MemberItem from '@/components/MemberItem'
  import PageNotFound from '@/components/PageNotFound'

  export default {
    metaInfo() {
      return {
        title: 'Members',
        titleTemplate: (chunk) => {
          if (this.is_action_join) {
            return `${this.join_title} | Flex.io`
          }

          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io'
        }
      }
    },
    components: {
      Spinner,
      MemberItem,
      PageNotFound
    },
    watch: {
      is_action_join: {
        immediate: true,
        handler: 'checkAlreadyMember'
      },
      is_fetched: {
        immediate: true,
        handler: 'checkAlreadyMember'
      },
      'add_dialog_model.users'() {
        // for some reason the 'change' trigger doesn't work here --
        // using a watcher here will have the same effect
        this.$refs.form.validateField('users')
      }
    },
    data() {
      return {
        is_checking_already_member: false,
        is_already_member: false,
        join_error_msg: '',
        show_add_dialog: false,
        add_dialog_has_errors: false,
        add_dialog_model: {
          users: [],
          options: []
        },
        add_dialog_rules: {
          users: [
            { validator: this.formValidateEmailArray }
          ],
        }
      }
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        is_fetching: state => state.members.is_fetching,
        is_fetched: state => state.members.is_fetched,
        active_team_name: state => state.teams.active_team_name,
        active_user_eid: state => state.users.active_user_eid,
      }),
      joining_member_email() {
        return _.get(this.$route, 'query.email', '')
      },
      is_joining_member_logged_in() {
        return this.joining_member_email == this.getActiveUserEmail()
      },
      is_action_join() {
        return _.get(this.$route, 'params.action') == 'join'
      },
      join_title() {
        return `Join Team "${this.active_team_name}"`
      },
      members() {
        // only show members that have a role -- this is important because
        // we don't want to show system admin. users in the list, but we *DO*
        // need them to be members of the team (but without a role)
        return _.filter(this.getAllMembers(), m => _.get(m, 'role', '').length > 0)
      },
    },
    created() {
      this.tryFetchMembers()
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUserEmail': 'getActiveUserEmail'
      }),
      ...mapGetters('members', {
        'getAllMembers': 'getAllMembers',
        'isActiveUserMemberOfTeam': 'isActiveUserMemberOfTeam'
      }),
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
      tryFetchMembers() {
        if (!this.is_fetched && !this.is_fetching) {
          var team_name = this.active_team_name

          this.$store.dispatch('members/fetch', { team_name })
        }
      },
      checkAlreadyMember() {
        // this method is to make sure existing members don't get shown
        // the 'join' message, but instead are moved back to the member list
        if (this.is_action_join && !this.is_checking_already_member) {
          // this user is already a member of this team; show the member list
          // with some feedback that they're already a member of the team
          if (this.isActiveUserMemberOfTeam()) {
            this.is_already_member = true

            var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
            new_route.params = _.assign({}, _.omit(new_route.params, ['action']))
            new_route.query = {}
            this.$router.replace(new_route)

            setTimeout(() => { this.is_already_member = false }, 4000)
          }

          this.is_checking_already_member = true
          setTimeout(() => { this.is_checking_already_member = false }, 10)
        }
      },
      sendInvites() {
        var timeout = 1

        // quick hack to allow multiple users to be added until the API supports it
        _.forEach(this.add_dialog_model.users, user => {
          setTimeout(() => { this.sendInvite(user) }, timeout)
          timeout += 50
        })

        this.show_add_dialog = false
      },
      formValidateEmailArray(rule, value, callback) {
        var has_errors = false
        _.each(value, v => {
          has_errors = has_errors || !isValidEmail(v)
        })

        if (value.length == 0) {
          this.add_dialog_has_errors = true
          callback(new Error('Please input at least one email address'))
        } else if (has_errors) {
          this.add_dialog_has_errors = true
          callback(new Error('One or more of the email addresses entered is invalid'))
        } else {
          this.add_dialog_has_errors = false
          callback()
        }
      },
      sendInvite(member) {
        var team_name = this.active_team_name
        var attrs = { member }
        this.$store.dispatch('members/create', { team_name, attrs })
      },
      rejectJoinTeam() {
        this.$router.push({ name: ROUTE_APP_PIPES })
      },
      joinTeam() {
        this.join_error_msg = ''

        var team_name = this.active_team_name
        var member = _.get(this.$route.query, 'email')
        var verify_code = _.get(this.$route.query, 'verify_code')
        var attrs = { member, verify_code }
        this.$store.dispatch('members/join', { team_name, attrs }).then(response => {
          this.$router.replace({ name: ROUTE_APP_PIPES, params: { team_name } })
        }).catch(error => {
          //this.join_error_msg = _.get(error, 'response.data.error.message', '')
          this.join_error_msg = 'There was a problem joining the team'
        })
      },
      onAddDialogOpen() {
        this.$nextTick(() => this.$refs['email-invite-select'].focus())
      },
      onAddDialogClose() {
        this.add_dialog_model = {
          users: [],
          options: []
        }
      }
    }
  }
</script>
