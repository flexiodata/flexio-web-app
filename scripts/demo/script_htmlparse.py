
from bs4 import BeautifulSoup

def flexio_file_handler(input,output):

    content = input.read()
    soup = BeautifulSoup(content, "html.parser")
    for script in soup(["script", "style"]):
        script.extract()
    text = soup.get_text()
    lines = (line.strip() for line in text.splitlines())
    chunks = (phrase.strip() for line in lines for phrase in line.split("  "))
    text = '\n'.join(chunk for chunk in chunks if chunk)
    output.content_type = 'text/plain'
    output.write(text.encode('utf-8','ignore'))
