<template>
  <!-- joining -->
  <div class="flex-fill flex flex-column bg-nearer-white" v-if="is_action_join">
    <!-- logged in user is not the same as the invited user -->
    <PageNotFound
      class="flex-fill"
      v-if="!is_joining_member_logged_in"
    />

    <!-- logged in user is the same as the invited user -->
    <div class="pa5">
      <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box" style="max-width: 36rem">
        <div class="tc">
          <i class="material-icons moon-gray" style="font-size: 4rem">people</i>
          <h3 class="flex-fill mt2 fw6 f3">{{join_title}}</h3>
        </div>
        <p>You've been invited to become a member of the team <strong>"{{active_team_name}}"</strong> on Flex.io. Would you like to join this team?</p>
        <div class="h2"></div>
        <div class="flex flex-row items-center justify-end">
          <el-button
            type="text"
            @click="leaveTeam"
          >
            No thanks, please remove me
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

  <!-- fetching -->
  <div v-else-if="is_fetching">
    <div class="flex flex-column justify-center h-100 bg-nearer-white">
      <Spinner size="large" message="Loading members..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column bg-nearer-white" v-else-if="is_fetched">
    <div class="pa5">
      <div class="w-100 center mw-doc pa4 bg-white br2 css-white-box" style="min-height: 20rem">
        <div class="flex flex-row items-start">
          <h3 class="flex-fill mt0 fw6 f3">Team Members</h3>
          <el-button
            class="ttu fw6"
            type="primary"
            @click="show_add_dialog = true"
          >
            Add Members
          </el-button>
        </div>
        <table class="el-table w-100 mv3">
          <tbody>
            <MemberItem
              :key="member.eid"
              :item="member"
              @resend-invite="resendInvite"
              @remove-member="removeMember"
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
            class="w-100"
            placeholder="Enter email addresses"
            spellcheck="false"
            multiple
            filterable
            allow-create
            default-first-option
            popper-class="dn"
            @visible-change="onEmailInviteSelectVisibleChange"
            @keydown.native.tab="addUserTag"
            @keydown.native.prevent.space="addUserTag"
            @keydown.native.prevent.188="addUserTag"
            v-model="add_dialog_model.users"
          >
            <el-option
              :label="option.label"
              :value="option.val"
              :key="option.val"
              v-for="option in add_dialog_model.options"
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
    data() {
      return {
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
      members() {
        return this.getAllMembers()
      },
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
      }
    },
    created() {
      if (!this.is_action_join) {
        this.tryFetchMembers()
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUserEmail': 'getActiveUserEmail'
      }),
      ...mapGetters('members', {
        'getAllMembers': 'getAllMembers'
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
      sendInvites() {
        var timeout = 20

        // quick hack to allow multiple users to be added until the API supports it
        _.forEach(this.add_dialog_model.users, user => {
          setTimeout(() => { this.sendInvite(user) }, timeout)
          timeout += 40
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
        var team_name = this.active_team_name
        var attrs = { member }
        this.$store.dispatch('members/create', { team_name, attrs })
      },
      resendInvite(member) {
        var team_name = this.active_team_name
        var eid = _.get(member, 'eid')
        this.$store.dispatch('members/resendInvite', { team_name, eid })
      },
      removeMember(member) {
        var team_name = this.active_team_name
        var eid = member.eid

        this.$store.dispatch('members/delete', { team_name, eid })
      },
      leaveTeam() {
        var team_name = this.active_team_name
        var eid = this.active_user_eid
        this.$store.dispatch('members/delete', { team_name, eid })
      },
      joinTeam() {
        var team_name = this.active_team_name
        var eid = this.active_user_eid
        var attrs = { member_status: 'A' }
        this.$store.dispatch('members/update', { team_name, eid, attrs })
      },
      onEmailInviteSelectVisibleChange(visible) {
        // this is somewhat of a hack, but it allows the final text that was
        // in the input to be added to the users array
        if (!visible) {
          this.addUserTag()
          this.$refs.form.validateField('users')
        }
      },
      addUserTag(evt) {
        var val = _.get(this.$refs['email-invite-select'], '$refs.input.value', '').trim()
        if (val.length > 0) {
          this.add_dialog_model.users = this.add_dialog_model.users.concat([val])
          evt && evt.preventDefault()
        }
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
