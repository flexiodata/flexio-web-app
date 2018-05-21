// allows any component to show an oauth popup window

export default {
  methods: {
    $_Oauth_showPopup(oauth_url, callback) {
      var wnd
      var wnd_timeout

      if (_.isNil(oauth_url)) {
        return
      }

      // default window settings
      var wnd_dimensions = {
        width: Math.floor(window.outerWidth * 0.8),
        height: Math.floor(window.outerHeight * 0.5)
      }

      // minimum window dimensions
      if (wnd_dimensions.height < 600) {
        wnd_dimensions.height = 600
      }

      if (wnd_dimensions.width < 800) {
        wnd_dimensions.width = 800
      }

      // window positioning
      wnd_dimensions.left = window.screenX + (window.outerWidth - wnd_dimensions.width) / 2
      wnd_dimensions.top = window.screenY + (window.outerHeight - wnd_dimensions.height) / 8

      // window options
      var wnd_options = 'width=' + wnd_dimensions.width + ',height=' + wnd_dimensions.height
      wnd_options += ',toolbar=0,scrollbars=1,status=1,resizable=1,location=1,menuBar=0'
      wnd_options += ',left=' + wnd_dimensions.left + ',top=' + wnd_dimensions.top

      wnd_timeout = setTimeout(function() {
        try { wnd.close() } catch(e) {}
      }, 1200 * 1000)

      wnd = window.open(oauth_url, 'ConnectWithOAuth', wnd_options)

      var timer = setInterval(function() {
        if (wnd.closed) {
          clearInterval(timer)

          if (typeof wnd.getOauthParams == 'function') {
            callback(wnd.getOauthParams())
          } else {
            callback()
          }
        }
      }, 500)

      if (wnd) {
        wnd.focus()
      }
    }
  }
}
