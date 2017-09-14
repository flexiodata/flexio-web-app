

// API TAG: bind user-supplied data with data from an API; allow API data
// to be pulled from user-supplied data; follows pattern for:
// * 1.4 Create file list that can be enriched and queried with file tags


// tag data a file
var t = Flexio.task()
.read('stdin')
.assoc({
    name: 'file-tags',
    ref1: '=params.filekey',
    ref2: '=params.tags'
})
.write('stdout', {
    success: true
});

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/files/tags',
    method: 'POST'
})
.task(t)
.save();

// get tagged files
var t = Flexio.task()
.read('stdin')
.validate({
    tags: {type: 'array'}
})
.assoc_get('file-tags', ':params.tags')
.write('stdout', {
    name: null,
    ref1: null,
    ref2: null
});

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/tags',
    method: 'GET'
})
.task(t)
.save();
