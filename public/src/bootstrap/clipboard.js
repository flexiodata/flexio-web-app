// global clipboard access

var copy = new Clipboard('.clipboardjs, [data-clipboard-text], [data-clipboard-target]')

copy.on('success', function(evt) {
  var t = evt.trigger
  var old = t.getAttribute('aria-label')

  t.setAttribute('hint--always', '')
  t.setAttribute('aria-label', 'Copied!')

  setTimeout(() => {
    t.removeAttribute('hint--always')

    if (old)
      t.setAttribute('aria-label', old)
       else
      t.removeAttribute('aria-label')
  }, 4000)
})

copy.on('error', function(evt) {
  var t = evt.trigger
  var old = t.getAttribute('aria-label')

  t.setAttribute('hint--always', '')
  t.setAttribute('aria-label', 'Press Ctrl+C to copy')

  setTimeout(() => {
    t.removeAttribute('hint--always')

    if (old)
      t.setAttribute('aria-label', old)
       else
      t.removeAttribute('aria-label')
  }, 4000)
})
