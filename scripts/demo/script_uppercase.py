import json
def flexio_handler(context):
    for row in context.input:
        context.output.write(json.dumps(row).uppercase() + "\\n")

