

// BASIC VALIDATION: take an upload file, validate it, and save it to a connection

// tag data from an api endpoint
Flexio.task()
.read('stdin')
.validate({
    // validation callback returns true/false
    mime_type: 'image/png',
    callback: '<code>'
})
.write({
    connection: 'dropbox'
})
.return({
    success: true
})
.save();

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/upload',
    method: 'POST'
})
.task(t)
.save();
