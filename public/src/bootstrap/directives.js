import Vue from 'vue'
import Vuebar from 'vuebar'
Vue.use(Vuebar)

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
