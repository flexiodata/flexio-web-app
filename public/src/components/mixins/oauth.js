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
      var wnd
      var wnd_timeout

      if (_.isNil(oauth_url)) {
        return
      }

      // 1. open popup window
      var wnd_options = getWindowOptions()
      var wnd = window.open(oauth_url, 'ConnectWithOAuth', wnd_options)

      // 2. update data when event is detected
      function updateAuthInfo(evt) {
        // 2a. fire oauth info back via the callback
        callback(evt.data)

        // 2b. close popup
        wnd.close()

        // 2c. remove the event listener we created above
        window.removeEventListener('message', updateAuthInfo)
      }

      // 3. listen for message from popup
      window.addEventListener('message', updateAuthInfo)

      // 4. bring the window to the front
      if (wnd) {
        wnd.focus()
      }

      // 5. close the window if it's been open for 20 minutes
      var wnd_timeout = setTimeout(function() {
        try { wnd.close() } catch(e) {}
      }, 1200 * 1000)
    }
  }
}
