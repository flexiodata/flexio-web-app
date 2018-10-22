import {
  AxiosResource,
  SignupResource,
  LoginResource,
  LogoutResource,
  UserResource,
  ConnectionResource,
  PipeResource,
  ProcessResource,
  UserProcessResource,
  RightsResource,
  TokenResource,
  StatisticsResource,
  StreamResource,
  AccountResource,
  AdminInfoResource,
  AdminTestResource,
  ValidateResource,
  ResetPasswordResource,
  VfsResource
} from './resources'

import axios from 'axios'

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

const GET = 'get'
const POS = 'save'
const PUT = 'update'
const DEL = 'delete'

const V2_GET = 'get'
const V2_POS = 'post'
const V2_PUT = 'put'
const V2_DEL = 'delete'

export default {

  // auth
  signUp:                         function({ attrs })                   { return SignupResource[POS] ({}, attrs)                                          },
  login:                          function({ attrs })                   { return LoginResource[POS] ({}, attrs)                                           },
  logout:                         function()                            { return LogoutResource[POS] ()                                                   },
  resetPassword:                  function({ attrs })                   { return ResetPasswordResource[POS] ({}, attrs)                                   },

  // validation
  validate:                       function({ attrs })                   { return ValidateResource[POS] ({}, attrs)                                        },

  // token
  fetchTokens:                    function()                            { return TokenResource[GET] ()                                                    },
  createToken:                    function()                            { return TokenResource[POS] ()                                                    },
  deleteToken:                    function({ eid })                     { return TokenResource[DEL] ({ eid })                                             },

  // user
  fetchUser:                      function({ eid })                     { return AccountResource[GET] ({ eid })                                           },
  updateUser:                     function({ eid, attrs })              { return AccountResource[POS] ({ eid }, attrs)                                    },
  deleteUser:                     function({ eid, attrs })              { return AccountResource[DEL] ({ eid }, attrs)                                    },
  changePassword:                 function({ eid, attrs })              { return AccountResource[POS] ({ eid, p1: 'credentials' }, attrs)                 },

  // connection
  fetchConnections:               function()                            { return ConnectionResource[GET] ()                                               },
  fetchConnection:                function({ eid })                     { return ConnectionResource[GET] ({ eid })                                        },
  createConnection:               function({ attrs })                   { return ConnectionResource[POS] ({}, attrs)                                      },
  updateConnection:               function({ eid, attrs })              { return ConnectionResource[POS] ({ eid }, attrs)                                 },
  deleteConnection:               function({ eid })                     { return ConnectionResource[DEL] ({ eid })                                        },
  testConnection:                 function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'connect' }, attrs)                  },
  disconnectConnection:           function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'disconnect' }, attrs)               },

  // pipe
  fetchPipes:                     function()                            { return PipeResource[GET] ()                                                     },
  fetchPipe:                      function({ eid })                     { return PipeResource[GET] ({ eid })                                              },
  createPipe:                     function({ attrs })                   { return PipeResource[POS] ({}, attrs)                                            },
  updatePipe:                     function({ eid, attrs })              { return PipeResource[POS] ({ eid }, attrs)                                       },
  deletePipe:                     function({ eid })                     { return PipeResource[DEL] ({ eid })                                              },

  // process
  fetchProcesses:                 function({ attrs })                   { return ProcessResource[GET] (attrs)                                             },
  fetchProcess:                   function({ eid, user_eid }) {
    var user_eid = user_eid || 'me'
    return UserProcessResource[GET] ({ eid, user_eid })
  },
  createProcess:                  function({ attrs })                   { return ProcessResource[POS] ({}, attrs)                                         },
  cancelProcess:                  function({ eid })                     { return ProcessResource[POS] ({ eid, p1: 'cancel' }, {})                         },

  // `attrs` differs here from other calls in that it wraps up all of the options (headers, etc.)
  runProcess: function({ eid, attrs }) {
    var config = {
      method: 'post',
      url: '/api/v2/me/processes/' + eid + '/run',
      withCredentials: true,
      headers: attrs.headers,
      data: attrs.data
    }

    return axios(config)
  },

  // process (other)
  fetchProcessLog:                function({ eid })                     { return ProcessResource[GET] ({ eid, p1: 'log' })                                },
  fetchProcessSummary:            function()                            { return ProcessResource[GET] ({ p1: 'summary' })                                 },

  // stream
  fetchStream:                    function({ eid })                     { return StreamResource[GET] ({ eid })                                            },

  // admin
  fetchAdminInfo:                 function({ type, action })            { return AdminInfoResource[GET] ({ p1: type, p2: action })                        },
  fetchAdminProcesses:            function({ attrs })                   { return AdminInfoResource[GET] (Object.assign({ p1: 'processes' }, attrs))       },
  fetchAdminTests:                function()                            { return AdminTestResource[GET] ({ p1: 'configure' })                             },
  runAdminTest:                   function({ id })                      { return AdminTestResource[GET] ({ p1: 'run', id })                               },

  // vfs
  vfsListFiles:                   function({ path })                    { return VfsResource[GET] ({ p1: 'list', q: path })                               },
  vfsGetFile:                     function({ path })                    { return VfsResource[GET] ({ p1: path })                                          },
  vfsPutFile:                     function({ path })                    { return VfsResource[PUT] ({ p1: path })                                          },
  vfsCreateDirectory:             function({ path })                    { return VfsResource[PUT] ({ p1: path })                                          },

  /* -- Axios-based v2 API -- */

  // connections
  v2_fetchConnections:     function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/connections`)                          },
  v2_fetchConnection:      function(user_eid, eid)        { return AxiosResource(user_eid)[V2_GET] (`/connections/${eid}`)                   },
  v2_createConnection:     function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/connections`, attrs)                   },
  v2_updateConnection:     function(user_eid, eid, attrs) { return AxiosResource(user_eid)[V2_POS] (`/connections/${eid}`, attrs)            },
  v2_deleteConnection:     function(user_eid, eid)        { return AxiosResource(user_eid)[V2_DEL] (`/connections/${eid}`)                   },
  v2_testConnection:       function(user_eid, eid, attrs) { return AxiosResource(user_eid)[V2_POS] (`/connections/${eid}/connect`, attrs)    },
  v2_disconnectConnection: function(user_eid, eid, attrs) { return AxiosResource(user_eid)[V2_POS] (`/connections/${eid}/disconnect`, attrs) },

  // pipes
  v2_fetchPipes:           function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/pipes`)                                },
  v2_fetchPipe:            function(user_eid, eid)        { return AxiosResource(user_eid)[V2_GET] (`/pipes/${eid}`)                         },
  v2_createPipe:           function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/pipes`, attrs)                         },
  v2_updatePipe:           function(user_eid, eid, attrs) { return AxiosResource(user_eid)[V2_POS] (`/pipes/${eid}`, attrs)                  },
  v2_deletePipe:           function(user_eid, eid)        { return AxiosResource(user_eid)[V2_DEL] (`/pipes/${eid}`)                         },

  // processes
  v2_fetchProcesses:       function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_GET] (`/processes`, attrs)                     },
  v2_fetchProcess:         function(user_eid, eid)        { return AxiosResource(user_eid)[V2_GET] (`/processes/${eid}`)                     },
  v2_createProcess:        function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/processes`, attrs)                     },
  v2_cancelProcess:        function(user_eid, eid)        { return AxiosResource(user_eid)[V2_POS] (`/processes/${eid}/cancel`)              },
  v2_runProcess:           function(user_eid, eid, cfg)   { return AxiosResource(user_eid)[V2_POS] (`/processes/${eid}/run`, {}, cfg)        },
  v2_fetchProcessLog:      function(user_eid, eid)        { return AxiosResource(user_eid)[V2_GET] (`/processes/${eid}/log`)                 },
  v2_fetchProcessSummary:  function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/processes/summary`)                    },

  // streams
  v2_fetchStream:          function(user_eid, eid)        { return AxiosResource(user_eid)[V2_GET] (`/streams/${eid}`)                       },

  // admin
  v2_fetchAdminProcesses:  function(attrs)                { return AxiosResource('admin')[V2_GET] (`/info/processes`, attrs)                 },
  v2_fetchAdminTests:      function()                     { return AxiosResource('admin')[V2_GET] (`/tests/configure`)                       },
  v2_runAdminTest:         function(attrs)                { return AxiosResource('admin')[V2_GET] (`/tests/run`, attrs)                      },
}

/*
1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
2) Get File            GET https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
3) Put File            PUT https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder ?????
5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
*/


