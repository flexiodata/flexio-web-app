import Vue from 'vue'
import Vuebar from 'vuebar'
import store from '@/store' // Vuex store
import { isMemberAllowed } from '@/utils/member'

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

const updateRightsElement = (el, binding) => {
  // if the value is false, we're going to ignore the rights requirement
  if (_.get(binding, 'value') === false) {
    return
  }

  var eid = store.state.users.active_user_eid
  var members = store.getters['members/getAllMembers']
  var active_member = _.find(members, { eid })

  // `arg` should be 'member', 'connection', etc.
  var arg = _.get(binding, 'arg', '')
  var mods = _.get(binding, 'modifiers', {})
  var rights_cls = _.get(mods, 'hidden') ? 'member-rights-hidden' : 'member-rights-disabled'

  // directive argument and modifier are both required
  try {
    if (arg.length == 0) {
      throw({ message: '`v-require-rights` directive argument is required' })
    } else if (mods.length == 0) {
      throw({ message: '`v-require-rights` directive modifier is required' })
    }
  }
  catch (e) {
    el.classList.add(rights_cls)
    return
  }

  // remove any modifiers that aren't member rights actions
  var actions = _.omit(mods, ['hidden'])

  // convert actions into an array and them map them to action strings
  // that the backend uses (e.g. 'action.connection.read', etc.)
  actions = _.keys(actions)
  actions = _.map(actions, action => {
    return `action.${arg}.${action}`
  })

  // add or remove the rights class depending on the active member's rights
  if (isMemberAllowed(active_member, actions)) {
    el.classList.remove(rights_cls)
  } else {
    el.classList.add(rights_cls)
  }
}

Vue.directive('require-rights', {
  bind: function(el, binding) {
    //console.log('bind:require-rights')
    updateRightsElement(el, binding)
  },
  componentUpdated: function(el, binding) {
    //console.log('componentUpdated:require-rights')
    updateRightsElement(el, binding)
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
