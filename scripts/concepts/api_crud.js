

// BASIC CRUD: headless functions for getting/setting/deleting arbitrary key/values

// create a new contact:
var t = Flexio.task()
.validate({
    mime_type: 'application/json',
    params: {
        name: {type: string, required: true},
        address: {type: string, required: true},
        city: {type: string, required: true},
        state: {type: string, required: true},
        zip: {type: string, required: true}
    }
})
.write({
    connection: 'local',
    name: 'contacts'
})
.write('stdout', {
    id: params['id'],
    name: params['name']
});

Flexio.pipe()
.task(t)
.run({
    params: {
        name: 'me',
        address: 'mountains',
        city: 'pine',
        state: 'ID',
        zip: ''
    }
});



// BASIC CRUD: create api endpoints for getting/setting/deleting arbitrary key/values

// USE:

// create a new contact:
// POST /contacts

// update contact:
// POST /contacts/:id

// get a contact:
// DELETE /contacts/:id

// delete a contact:
// DELETE /contacts/:id

// get a list of contacts:
// GET /contacts


// DEFINITIONS:

// create a new contact:
var t = Flexio.task()
.validate({
    mime_type: 'application/json',
    params: {
        name: {type: string, required: true},
        address: {type: string, required: true},
        city: {type: string, required: true},
        state: {type: string, required: true},
        zip: {type: string, required: true}
    }
})
.write({
    connection: 'local',
    name: 'contacts'
})
.query({
    // limit the results returned in stdout
    id: params['id'],
    name: params['name']
})
.write('stdout');

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/contacts',
    method: 'POST'
})
.task(t)
.save();

// update a contact
var t = Flexio.task()
.validate({
    mime_type: 'application/json',
    params: {
        name: {type: string, required: true},
        address: {type: string, required: true},
        city: {type: string, required: true},
        state: {type: string, required: true},
        zip: {type: string, required: true}
    }
})
.write({
    connection: 'local',
    name: 'contacts',
    lookup: ':id'
});

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/contacts/:id',
    method: 'POST'
})
.task(t)
.save();

// get a contact:
var t = Flexio.task()
.read({
    connection: 'local',
    name: 'contacts',
    lookup: ':id'
})
.write('stdout');

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/contacts/:id',
    method: 'GET'
})
.task(t)
.save();

// delete a contact:
var t = Flexio.task()
.delete({
    connection: 'local',
    name: 'contacts',
    lookup: ':id'
})
.write('stdout', {
    success: true
});

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/contacts/:id',
    method: 'DELETE'
})
.task(t)
.save();

// get a list of contacts:
Flexio.pipe()
.trigger({
    type: 'http',
    url: '/contacts',
    method: 'GET'
})
.task(Flexio.task()
    .read({
        connection: 'local',
        name: 'contacts'
    })
    .write('stdout', {
        id: params['id'],
        name: params['name']
    })
)
.save();
