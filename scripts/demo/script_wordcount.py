
import json
from collections import defaultdict

def remove_non_ascii(text):
    return ''.join([i if ord(i) < 128 else ' ' for i in text])

def flexio_file_handler(input,output):


    content = input.read()
    content = content.decode()
    content = content.lower()

    d = defaultdict(int)
    for word in content.split():
        if len(word) > 3:
            d[word] += 1

    result = []
    for key, value in d.items():
        if value > 1:
            i = {"id": remove_non_ascii(key), "value": value}
            result.append(i)

    output.content_type = "application/json"
    output.write(json.dumps(result))
