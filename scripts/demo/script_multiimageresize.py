
import PIL
from PIL import Image
from io import BytesIO
import mimetypes

def flexio_handler(context):

    imagedata = context.input.read()

    for size in range(50,250,50):
        extension = mimetypes.guess_extension(context.input.content_type)
        extension = '.jpg'
        context.output = outputs.create(name='thumbnail'+str(size)+extension, content_type=context.input.content_type)

        file_imagedata = BytesIO(imagedata)
        img = Image.open(file_imagedata)
        imgformat = img.format

        basewidth = size
        wpercent = (basewidth / float(img.size[0]))
        hsize = int((float(img.size[1]) * float(wpercent)))
        img = img.resize((basewidth, hsize), PIL.Image.ANTIALIAS)

        file_output = BytesIO()
        img.save(file_output, format = imgformat)
        file_output.seek(0)
        contents = file_output.getvalue()
        file_output.close()

        context.output.write(contents)

