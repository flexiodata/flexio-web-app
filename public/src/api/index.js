import {
  BaseResource,
  SignupResource,
  LoginResource,
  LogoutResource,
  UserResource,
  ConnectionResource,
  PipeResource,
  ProcessResource,
  RightsResource,
  TokenResource,
  StatisticsResource,
  StreamResource,
  AccountResource,
  AdminInfoResource,
  AdminTestResource,
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

  // auth
  signUp:                         function({ attrs })                   { return SignupResource[POS] ({}, attrs)                            },
  login:                          function({ attrs })                   { return LoginResource[POS] ({}, attrs)                             },
  logout:                         function()                            { return LogoutResource[POS] ()                                     },

  // validation
  validate:                       function({ attrs })                   { return ValidateResource[POS] ({}, attrs)                          },

  // rights
  fetchRights:                    function({ attrs })                   { return RightsResource[GET] (attrs)                                },
  createRights:                   function({ attrs })                   { return RightsResource[POS] ({}, attrs)                            },
  updateRight:                    function({ eid, attrs })              { return RightsResource[POS] ({ eid }, attrs)                       },
  deleteRight:                    function({ eid })                     { return RightsResource[DEL] ({ eid })                              },

  // token
  fetchTokens:                    function()                            { return TokenResource[GET] ()                                      },
  createToken:                    function()                            { return TokenResource[POS] ()                                      },
  deleteToken:                    function({ eid })                     { return TokenResource[DEL] ({ eid })                               },

  // user
  fetchUser:                      function({ eid })                     { return AccountResource[GET] ({ eid })                             },
  updateUser:                     function({ eid, attrs })              { return AccountResource[POS] ({ eid }, attrs)                      },
  changePassword:                 function({ eid, attrs })              { return AccountResource[POS] ({ eid, p1: 'credentials' }, attrs)   },
  resetPassword:                  function({ attrs })                   { return AccountResource[DEL] ({ eid, p1: 'credentials' }, attrs)   },

  // connection
  fetchConnections:               function()                            { return ConnectionResource[GET] ()                                 },
  fetchConnection:                function({ eid })                     { return ConnectionResource[GET] ({ eid })                          },
  createConnection:               function({ attrs })                   { return ConnectionResource[POS] ({}, attrs)                        },
  updateConnection:               function({ eid, attrs })              { return ConnectionResource[POS] ({ eid }, attrs)                   },
  deleteConnection:               function({ eid })                     { return ConnectionResource[DEL] ({ eid })                          },
  testConnection:                 function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'connect' }, attrs)    },
  disconnectConnection:           function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'disconnect' }, attrs) },

  // pipe
  fetchPipes:                     function()                            { return PipeResource[GET] ()                                       },
  fetchPipe:                      function({ eid })                     { return PipeResource[GET] ({ eid })                                },
  createPipe:                     function({ attrs })                   { return PipeResource[POS] ({}, attrs)                              },
  updatePipe:                     function({ eid, attrs })              { return PipeResource[POS] ({ eid }, attrs)                         },
  deletePipe:                     function({ eid })                     { return PipeResource[DEL] ({ eid })                                },

  // process
  fetchProcesses:                 function({ attrs })                   { return ProcessResource[GET] (attrs)                               },
  fetchProcess:                   function({ eid })                     { return ProcessResource[GET] ({ eid })                             },
  createProcess:                  function({ attrs })                   { return ProcessResource[POS] ({}, attrs)                           },
  cancelProcess:                  function({ eid })                     { return ProcessResource[POS] ({ eid, p1: 'cancel' }, {})           },

  // process (other)
  fetchProcessLog:                function({ eid })                     { return ProcessResource[GET] ({ eid, p1: 'log' })                  },

  // stream
  fetchStream:                    function({ eid })                     { return StreamResource[GET] ({ eid })                              },

  // statistics
  fetchStatistics:                function({ type, action })            { return BaseResource[GET] ({ p1: type, p2: action })               },

  // admin
  fetchAdminInfo:                 function({ type, action })            { return AdminInfoResource[GET] ({ p1: type, p2: action })          },
  fetchAdminTests:                function()                            { return AdminTestResource[GET] ({ p1: 'configure' })               },
  runAdminTest:                   function({ id })                      { return AdminTestResource[GET] ({ p1: 'run', id })                 },

  // vfs
  vfsListFiles:                   function({ path })                     { return VfsResource[GET] ({ p1: 'list', q: path })                },
  vfsGetFile:                     function({ path })                     { return VfsResource[GET] ({ p1: path })                           },
  vfsPutFile:                     function({ path })                     { return VfsResource[PUT] ({ p1: path })                           },
  vfsCreateDirectory:             function({ path })                     { return VfsResource[PUT] ({ p1: path })                           }
}

/*
1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
2) Get File            GET https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
3) Put File            PUT https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder ?????
5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
*/
