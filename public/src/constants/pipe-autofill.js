import {
  TASK_TYPE_REQUEST,
  TASK_TYPE_CONVERT,
  TASK_TYPE_EMAIL_SEND,
  TASK_TYPE_EXECUTE,
  TASK_TYPE_RENDER
} from '../constants/task-type'

import {
  CONNECTION_TYPE_HTTP,
  CONNECTION_TYPE_RSS
} from '../constants/connection-type'

export default [
  {
    label: 'Request from Web',
    task_json: {
      'type': TASK_TYPE_REQUEST,
      'params': {
        'method': 'GET',
        'url': 'https://static.pexels.com/photos/51387/mount-everest-himalayas-nuptse-lhotse-51387.jpeg'
      }
    }
  },{
    label: 'Render a webpage',
    task_json: {
      'type': TASK_TYPE_RENDER,
      'params': {
        'url': 'https://www.flex.io',
        'format': 'png',
        'width': '800',
        'height': '600',
        'scrollbars': false
      }
    }
  },{
    label: 'Execute Python Code',
    task_json: {
      'type': TASK_TYPE_EXECUTE,
      'params': {
        'lang': 'python',
        // "Hello, World!" example code
        'code': 'ZGVmIGZsZXhpb19oYW5kbGVyKGNvbnRleHQpOg0KICAgIGNvbnRleHQub3V0cHV0LmNvbnRlbnRfdHlwZSA9ICJ0ZXh0L3BsYWluIg0KICAgIGNvbnRleHQub3V0cHV0LndyaXRlKCdIZWxsbyBXb3JsZCEnKQ=='
      }
    }
  },{
    label: 'Execute Javascript Code',
    task_json: {
      'type': TASK_TYPE_EXECUTE,
      'params': {
        'lang': 'javascript',
        // "Hello, World!" example code
        'code': 'ZXhwb3J0cy5mbGV4aW9faGFuZGxlciA9IGZ1bmN0aW9uKGNvbnRleHQpIHsNCiAgICBjb250ZXh0Lm91dHB1dC5jb250ZW50X3R5cGUgPSAidGV4dC9wbGFpbiINCiAgICBjb250ZXh0Lm91dHB1dC53cml0ZSgnSGVsbG8sIFdvcmxkIScpDQp9'
      }
    }
  },{
    label: 'Send Email',
    task_json: {
      'type': TASK_TYPE_EMAIL_SEND,
      'params': {
        'to': ['john.smith@example.com'],
        'subject': 'My Subject',
        'body_text': 'This is a test of Flex.io'
      }
    }
  },{
    label: 'Convert From CSV to JSON',
    task_json: {
      'type': TASK_TYPE_CONVERT,
      'params': {
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
  }
]
