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

  // vfs
  v2_vfsListFiles:            function(user_eid, path)    { return AxiosResource(user_eid)[V2_GET] (`/vfs/list`, { q: path })                },
  //v2_vfsGetFile:              function(user_eid, path)    { return AxiosResource(user_eid)[V2_GET] (TODO)                                    },
  //v2_vfsPutFile:              function(user_eid, path)    { return AxiosResource(user_eid)[V2_PUT] (TODO)                                    },
  //v2_vfsCreateDirectory:      function(user_eid, path)    { return AxiosResource(user_eid)[V2_PUT] (TODO)                                    },

  // user
  v2_fetchUser:            function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/account`)                              },
  v2_updateUser:           function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/account`, attrs)                       },
  v2_deleteUser:           function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_DEL] (`/account`, attrs)                       },
  v2_changePassword:       function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/account/credentials`, attrs)           },

  // token
  v2_fetchTokens:          function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/auth/keys`)                            },
  v2_createToken:          function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/auth/keys`, attrs)                     },
  v2_deleteToken:          function(user_eid, eid)        { return AxiosResource(user_eid)[V2_DEL] (`/auth/keys/${eid}`)                     },

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


