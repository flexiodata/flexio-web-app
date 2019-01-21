import { AxiosResource } from './resources'

const V2_GET = 'get'
const V2_POS = 'post'
const V2_PUT = 'put'
const V2_DEL = 'delete'

export default {
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
  v2_vfsListFiles:         function(user_eid, path)       { return AxiosResource(user_eid)[V2_GET] (`/vfs/list`, { q: path })                },
//v2_vfsGetFile:           function(user_eid, path)       { return AxiosResource(user_eid)[V2_GET] (TODO)                                    },
//v2_vfsPutFile:           function(user_eid, path)       { return AxiosResource(user_eid)[V2_PUT] (TODO)                                    },
//v2_vfsCreateDirectory:   function(user_eid, path)       { return AxiosResource(user_eid)[V2_PUT] (TODO)                                    },

  // users
  v2_fetchUser:            function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/account`)                              },
  v2_updateUser:           function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/account`, attrs)                       },
  v2_deleteUser:           function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_DEL] (`/account`, attrs)                       },
  v2_changePassword:       function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/account/credentials`, attrs)           },

  // cards
  v2_fetchCards:           function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/account/cards`)                        },
  v2_createCard:           function(user_eid, attrs)      { return AxiosResource(user_eid)[V2_POS] (`/account/cards`, attrs)                 },
  v2_deleteCard:           function(user_eid, eid)        { return AxiosResource(user_eid)[V2_DEL] (`/account/cards/${eid}`)                     },

  // tokens
  v2_fetchTokens:          function(user_eid)             { return AxiosResource(user_eid)[V2_GET] (`/auth/keys`)                            },
  v2_createToken:          function(user_eid)             { return AxiosResource(user_eid)[V2_POS] (`/auth/keys`)                            },
  v2_deleteToken:          function(user_eid, eid)        { return AxiosResource(user_eid)[V2_DEL] (`/auth/keys/${eid}`)                     },

  // auth
//v2_signUp:               function(attrs)                { return AxiosResource(null)[V2_POS] (`/signup`)                                   },
//v2_login:                function(attrs)                { return AxiosResource(null)[V2_POS] (`/login`)                                    },
  v2_logout:               function()                     { return AxiosResource(null)[V2_POS] (`/logout`)                                   },
  v2_resetPassword:        function(attrs)                { return AxiosResource(null)[V2_POS] (`/resetpassword`, attrs)                     },

  // validation
  v2_validate:             function(attrs)                { return AxiosResource(null)[V2_POS] (`/validate`, attrs)                          },

  // admin
  v2_fetchAdminProcesses:  function(attrs)                { return AxiosResource('admin')[V2_GET] (`/info/processes`, attrs)                 },
  v2_fetchAdminTests:      function()                     { return AxiosResource('admin')[V2_GET] (`/tests/configure`)                       },
  v2_runAdminTest:         function(attrs)                { return AxiosResource('admin')[V2_GET] (`/tests/run`, attrs)                      },
}

/*
  VFS Notes...

  1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
  2) Get File            GET https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
  3) Put File            PUT https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
  4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder ?????
  5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-alias/my-folder/my-subfolder/aphist.csv
*/
