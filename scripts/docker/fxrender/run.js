const puppeteer = require('puppeteer');
const commandLineArgs = require('command-line-args')

const options = commandLineArgs([
   { name: 'url' },
   { name: 'format', defaultValue: "png" },
   { name: 'paper', defaultValue: "Letter" },
   { name: 'width' },
   { name: 'height' },
   { name: 'scrollbars' },
   { name: 'sleep' }
]);


(async () => {
  var output_filename = '/tmp/output.' + options.format;


  const browser = await puppeteer.launch({args:['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto(options.url);

  if (options.format == 'pdf') {
    await page.pdf({path: output_filename, format: options.paper})
  } else {
    await page.screenshot({path: output_filename});
  }

  await browser.close();


  var rs = require('fs').createReadStream(output_filename);
  rs.pipe(process.stdout);
  
})();

