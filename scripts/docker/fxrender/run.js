const puppeteer = require('puppeteer');
const commandLineArgs = require('command-line-args')

const options = commandLineArgs([
   { name: 'url' },
   { name: 'format', defaultValue: "png" },
   { name: 'paper', defaultValue: "Letter" },
   { name: 'landscape', defaultValue: false, type: Boolean },
   { name: 'fullPage', defaultValue: false, type: Boolean },
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

  var params = {
    path: output_filename,
  };

  if (options.hasOwnProperty('paper')) {
    params.format = options.paper
  }
  if (options.hasOwnProperty('fullPage') && options.fullPage) {
    params.fullPage = true
  }

  if (options.hasOwnProperty('landscape')) {
    params.landscape = options.landscape
  }

  if (options.format == 'pdf') {
    await page.pdf(params)
  } else {
    await page.screenshot(params);
  }

  await browser.close();


  var rs = require('fs').createReadStream(output_filename);
  rs.pipe(process.stdout);
  
})();

