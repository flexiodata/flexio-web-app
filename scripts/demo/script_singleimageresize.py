
import PIL
from PIL import Image
from io import BytesIO

def flexio_file_handler(input,output):
	output.content_type = input.content_type

	imagedata = input.read()
	file_imagedata = BytesIO(imagedata)
	img = Image.open(file_imagedata)
	imgformat = img.format

	basewidth = 100
	wpercent = (basewidth / float(img.size[0]))
	hsize = int((float(img.size[1]) * float(wpercent)))
	img = img.resize((basewidth, hsize), PIL.Image.ANTIALIAS)

	file_output = BytesIO()
	img.save(file_output, format = imgformat)
	file_output.seek(0)
	contents = file_output.getvalue()
	file_output.close()

	output.write(contents)

