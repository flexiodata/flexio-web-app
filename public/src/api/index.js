import {
  //SignupResource,
  //LoginResource,
  LogoutResource,
  HelpResource,
  UserResource,
  UserTokenResource,
  ProjectResource,
  ConnectionResource,
  PipeResource,
  ProcessResource,
  RightsResource,
  AdminResource,
  StatisticsResource,
  StreamResource,
  TestResource,
  ValidateResource,
  VfsResource
} from './resources'

/*
  // global Vue object
  Vue.http.get('/someUrl', [options]).then(successCallback, errorCallback);
  Vue.http.post('/someUrl', [body], [options]).then(successCallback, errorCallback);

  // in a Vue instance
  this.$http.get('/someUrl', [options]).then(successCallback, errorCallback);
  this.$http.post('/someUrl', [body], [options]).then(successCallback, errorCallback);

  // resource default actions
  get:    { method: 'GET'    },
  save:   { method: 'POST'   },
  query:  { method: 'GET'    },
  update: { method: 'PUT'    },
  remove: { method: 'DELETE' },
  delete: { method: 'DELETE' }
*/

var GET = 'get'
var POS = 'save'
var PUT = 'update'
var DEL = 'delete'

export default {
  // NO V2 // login:                          function({ attrs })                   { return LoginResource[POS] ({}, attrs)                                                           },
  // NO V2 // signUp:                         function()                            { return SignupResource[POS] ({}, attrs)                                                          },
  // NO V2 // checkSignup:                    function({ attrs })                   { return SignupResource[POS] ({ p1: 'check' }, attrs)                                             },
  // NO V2 // signUp:                         function({ attrs })                   { return UserResource[POS] ({}, attrs)                                                            },

  // auth
  logout:                         function()                            { return LogoutResource[POS] ()                                                                   },

  // validation
  validate:                       function({ attrs })                   { return ValidateResource[POS] ({}, attrs)                                                        },

  // rights
  fetchRights:                    function({ attrs })                   { return RightsResource[GET] (attrs)                                                              },
  createRights:                   function({ attrs })                   { return RightsResource[POS] ({}, attrs)                                                          },
  updateRight:                    function({ eid, attrs })              { return RightsResource[POS] ({ eid }, attrs)                                                     },
  deleteRight:                    function({ eid })                     { return RightsResource[DEL] ({ eid })                                                            },

  // user
  fetchUser:                      function({ eid })                     { return UserResource[GET] ({ eid })                                                              },
  createUser:                     function({ attrs })                   { return UserResource[POS] ({}, attrs)                                                            },
  updateUser:                     function({ eid, attrs })              { return UserResource[POS] ({ eid }, attrs)                                                       },
  changePassword:                 function({ eid, attrs })              { return UserResource[POS] ({ eid, p1: 'changepassword'       }, attrs)                           },
  requestPasswordReset:           function({ attrs })                   { return UserResource[POS] ({      p1: 'requestpasswordreset' }, attrs)                           },
  resetPassword:                  function({ attrs })                   { return UserResource[POS] ({      p1: 'resetpassword'        }, attrs)                           },

  // token
  fetchUserTokens:                function({ eid })                     { return UserResource[GET] ({ eid, p1: 'tokens' })                                                },
  createUserToken:                function({ eid, attrs })              { return UserResource[POS] ({ eid, p1: 'tokens' }, attrs)                                         },
  deleteUserToken:                function({ eid, token_eid })          { return UserResource[DEL] ({ eid, p1: 'tokens', p2: token_eid })                                 },

  // connection
  fetchConnections:               function()                            { return ConnectionResource[GET] ()                                                               },
  fetchConnection:                function({ eid })                     { return ConnectionResource[GET] ({ eid })                                                        },
  createConnection:               function({ attrs })                   { return ConnectionResource[POS] ({}, attrs)                                                      },
  updateConnection:               function({ eid, attrs })              { return ConnectionResource[POS] ({ eid }, attrs)                                                 },
  deleteConnection:               function({ eid })                     { return ConnectionResource[DEL] ({ eid })                                                        },
  describeConnection:             function({ eid, path })               { return ConnectionResource[GET] ({ eid, p1: 'describe', q: path })                               },
  testConnection:                 function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'connect' }, attrs)                                  },
  disconnectConnection:           function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'disconnect' }, attrs)                               },

  // pipe
  fetchPipes:                     function()                            { return PipeResource[GET] ()                                                                     },
  fetchPipe:                      function({ eid })                     { return PipeResource[GET] ({ eid })                                                              },
  createPipe:                     function({ attrs })                   { return PipeResource[POS] ({}, attrs)                                                            },
  updatePipe:                     function({ eid, attrs })              { return PipeResource[POS] ({ eid }, attrs)                                                       },
  deletePipe:                     function({ eid })                     { return PipeResource[DEL] ({ eid })                                                              },

  // pipe (other)
  fetchPipeProcesses: function({ eid, attrs }) {
    var params = _.assign({ eid, p1: 'processes' }, attrs)
    return PipeResource[GET] (params)
  },

  // process
  fetchProcess:                   function({ eid })                     { return ProcessResource[GET] ({ eid })                                                           },
  createProcess:                  function({ attrs })                   { return ProcessResource[POS] ({}, attrs)                                                         },
  cancelProcess:                  function({ eid })                     { return ProcessResource[POS] ({ eid, p1: 'cancel' }, {})                                         },

  // process (other)
  fetchProcessLog:                function({ eid })                     { return ProcessResource[GET] ({ eid, p1: 'log' })                                                },

  // admin statistics
  fetchAdminStatistics:           function({ type })                    { return AdminResource[GET] ({ type })                                                            },

  // statistics
  fetchStatistics:                function({ type })                    { return StatisticsResource[GET] ({ type })                                                       },

  // stream
  fetchStream:                    function({ eid })                     { return StreamResource[GET] ({ eid })                                                            },

  // test
  fetchTests:                     function()                            { return TestResource[GET] ({ p1: 'configure' })                                                  },
  runTest:                        function({ id })                      { return TestResource[GET] ({ p1: 'run', id })                                                    },

  // vfs
  vfsListFiles:                   function({ path })                     { return VfsResource[GET] ({ p1: 'list', q: path })                                              },
  vfsGetFile:                     function({ path })                     { return VfsResource[GET] ({ p1: path })                                                         },
  vfsPutFile:                     function({ path })                     { return VfsResource[PUT] ({ p1: path })                                                         },
  vfsCreateDirectory:             function({ path })                     { return VfsResource[PUT] ({ p1: path })                                                         }
}

/*
1) List Directory      GET https://localhost/api/v1/vfs/list?q=/
2) Get File            GET https://localhost/api/v1/vfs/my-alias/my-folder/my-subfolder/aphist.csv
3) Put File            PUT https://localhost/api/v1/vfs/my-alias/my-folder/my-subfolder/aphist.csv
4) Create Directory    PUT https://localhost/api/v1/vfs/my-alias/my-folder/my-subfolder   ?????
5) Delete Files        DEL https://localhost/api/v1/vfs/my-alias/my-folder/my-subfolder/aphist.csv
*/
