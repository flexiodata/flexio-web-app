import Vue from 'vue'
import Vuebar from 'vuebar'
import store from '@/store' // Vuex store
import member_roles from '@/data/member-roles.yml'

Vue.use(Vuebar)

Vue.directive('tag-input', {
  inserted: function(el, binding, vnode) {
    // only apply this directive for `el-select` components
    if (_.get(vnode.componentOptions, 'tag') != 'el-select') {
      return
    }

    var component = vnode.componentInstance

    component.$refs.input.addEventListener('blur', (evt) => {
      component.selectOption()
    })

    el.addEventListener('keydown', (evt) => {
      switch (evt.keyCode) {
        case 188 /* comma */:
        case 32 /* space */:
          evt.preventDefault()
          component.selectOption()
          break
        case 9 /* tab */:
          component.selectOption()
          break
      }
    })
  }
})

Vue.directive('require-rights', {
  inserted: function(el, binding, vnode) {
    var eid = store.state.users.active_user_eid
    var members = store.getters['members/getAllMembers']
    var active_member = _.find(members, { eid })
    var active_member_role = _.get(active_member, 'role', '')
    var role = _.find(member_roles, r => r.type == active_member_role)
    var rights = _.get(role, 'rights', {})
    var is_allowed = true

    if (!active_member) {
      // active user is not a member of this team; bail out
      is_allowed = false
    } else if (active_member_role == 'O') {
      // active user is the owner of this team; move along
      is_allowed = true
    } else {
      var arg = _.get(binding, 'arg', '')
      var mods = _.get(binding, 'modifiers', {})

      // directive argument is required
      try {
        if (arg.length == 0) {
          throw({ message: '`v-require-rights` directive argument is required' })
        } else if (mods.length == 0) {
          throw({ message: '`v-require-rights` directive modifier is required' })
        }
      }
      catch (e) {
        is_allowed = false
      }

      // map friendly-looking modifiers to single-letter UNIX-style characters
      mods = _.map(mods, (val, key) => {

        switch (key) {
          case 'r':
          case 'read':
            return 'r'
          case 'w':
          case 'write':
            return 'w'
          case 'd':
          case 'delete':
            return 'd'
          case 'x':
          case 'execute':
            return 'x'
        }
        return null
      })

      // remove empty items (modifiers that are not accepted)
      mods = _.compact(mods)

      // check directive value against the role's UNIX-style permission string
      var rights_str = _.get(rights, arg, '')
      _.each(mods, m => {
        if (rights_str.indexOf(m) < 0) {
          is_allowed = false
        }
      })
    }

    if (!is_allowed) {
      el.classList.add('no-flexio-rights')
    } else {
      el.classList.remove('no-flexio-rights')
    }
  }
})

/*
import Vue from 'vue'

// global directives (move to a new file if we get too many here)

Vue.directive('focus', {
  // when the bound element is inserted into the DOM...
  inserted: function(el) {
    // ...focus the element
    el.focus()
  }
})

Vue.directive('deferred-focus', {
  // when the bound element is inserted into the DOM...
  inserted: function(el, binding) {
    // ...focus the element after a delay (in milliseconds)
    var delay = typeof binding.value == 'number' ? binding.value : 10
    setTimeout(() => { el.focus() }, delay)
  }
})

Vue.directive('select-all', {
  // when the bound element is inserted into the DOM...
  inserted: function(el, binding) {
    // ...select all text after a delay (in milliseconds)
    var delay = typeof binding.value == 'number' ? binding.value : 10
    setTimeout(() => {
      el.selectionStart = 0
      el.selectionEnd = el.value.length
      el.focus()
    }, delay)
  }
})
*/
