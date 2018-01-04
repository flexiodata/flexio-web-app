def flexio_handler(context):
    context.output.content_type = "text/plain"
    context.output.write("Hello, World!")
