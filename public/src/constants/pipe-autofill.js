import {
  TASK_OP_REQUEST,
  TASK_OP_CONVERT,
  TASK_OP_EMAIL_SEND,
  TASK_OP_EXECUTE,
  TASK_OP_RENDER
} from '../constants/task-op'

import {
  CONNECTION_TYPE_HTTP,
  CONNECTION_TYPE_RSS
} from '../constants/connection-type'

export default [
  {
    label: 'Request from Web',
    task_json: {
      'op': TASK_OP_REQUEST,
      'method': 'GET',
      'url': 'https://raw.githubusercontent.com/flexiodata/data/master/mockaroo/names-and-ip-addresses.csv'
    }
  },{
    label: 'Render a webpage',
    task_json: {
      'op': TASK_OP_RENDER,
      'url': 'https://www.flex.io',
      'format': 'png',
      'width': '800',
      'height': '600',
      'scrollbars': false
    }
  },{
    label: 'Execute Python Code',
    task_json: {
      'op': TASK_OP_EXECUTE,
      'lang': 'python',
      // "Hello, World!" example code
      'code': 'ZGVmIGZsZXhpb19oYW5kbGVyKGNvbnRleHQpOg0KICAgIGNvbnRleHQub3V0cHV0LmNvbnRlbnRfdHlwZSA9ICJ0ZXh0L3BsYWluIg0KICAgIGNvbnRleHQub3V0cHV0LndyaXRlKCdIZWxsbyBXb3JsZCEnKQ=='
    }
  }
  },{
    label: 'Execute Javascript Code',
    task_json: {
      'op': TASK_OP_EXECUTE,
      'lang': 'javascript',
      // "Hello, World!" example code
      'code': 'ZXhwb3J0cy5mbGV4aW9faGFuZGxlciA9IGZ1bmN0aW9uKGNvbnRleHQpIHsNCiAgICBjb250ZXh0Lm91dHB1dC5jb250ZW50X3R5cGUgPSAidGV4dC9wbGFpbiINCiAgICBjb250ZXh0Lm91dHB1dC53cml0ZSgnSGVsbG8sIFdvcmxkIScpDQp9'
    }
  },{
    label: 'Send Email',
    task_json: {
      'op': TASK_OP_EMAIL_SEND,
      'to': ['john.smith@example.com'],
      'subject': 'My Subject',
      'body_text': 'This is a test of Flex.io'
    }
  },{
    label: 'Convert From CSV to JSON',
    task_json: {
      'op': TASK_OP_CONVERT,
      'input': {
        'format': 'delimited',
        'delimiter': '{comma}',
        'qualifier': '{double-quote}'
      },
      'output': {
        'format': 'json'
      }
    }
  }
]
