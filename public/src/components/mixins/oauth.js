// allows any component to show an oauth popup window

function getWindowOptions() {
  // default window settings
  var wnd_dimensions = {
    width: Math.floor(window.outerWidth * 0.5),
    height: Math.floor(window.outerHeight * 0.75)
  }

  // minimum window height
  if (wnd_dimensions.height < 600) {
    wnd_dimensions.height = 600
  }

  // minimum window width
  if (wnd_dimensions.width < 600) {
    wnd_dimensions.width = 600
  }

  // window positioning
  wnd_dimensions.left = window.screenX + (window.outerWidth - wnd_dimensions.width) / 2
  wnd_dimensions.top = window.screenY + (window.outerHeight - wnd_dimensions.height) / 8

  // window options
  var wnd_options = 'width=' + wnd_dimensions.width + ',height=' + wnd_dimensions.height
  wnd_options += ',toolbar=0,scrollbars=1,status=1,resizable=1,location=1,menuBar=0'
  wnd_options += ',left=' + wnd_dimensions.left + ',top=' + wnd_dimensions.top

  return wnd_options
}

export default {
  methods: {
    $_Oauth_showPopup(oauth_url, callback) {
      if (_.isNil(oauth_url)) {
        return
      }

      // open popup window
      var wnd_options = getWindowOptions()
      var wnd = window.open(oauth_url, 'ConnectWithOAuth', wnd_options)

      // close the window if it's been open for 20 minutes
      var wnd_timeout = setTimeout(function() {
        try { wnd.close() } catch(e) {}
      }, 1200 * 1000)

      // update data when event is detected
      function updateAuthInfo(evt) {
        debugger
        wnd.close()
        callback(evt.data)
      }

      // listen for message from popup
      window.addEventListener('message', updateAuthInfo)

      // bring the window to the front
      if (wnd) {
        wnd.focus()
      }

      // continue to check if the window is closed (by other means
      // than the OAuth callback) so that we can do cleanup regardless
      var wnd_timer = setInterval(function() {
        if (wnd.closed) {
          clearInterval(wnd_timer)
          clearTimeout(wnd_timeout)
          window.removeEventListener('message', updateAuthInfo)
        }
      }, 500)
    }
  }
}
