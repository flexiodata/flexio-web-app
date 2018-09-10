import flexio as flexioext


def flexio_start_handler(context):
    import fxmodule
    handler = getattr(fxmodule, 'flexio_handler', None)
    if callable(handler):
        handler(context)


flexioext.run(flexio_start_handler)
