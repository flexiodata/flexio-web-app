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
  await page.goto(options.url,{waitUntil:"networkidle","networkIdleTimeout" : 3000});
  //await page.goto(options.url);

 // await page.waitForNavigation({timeout:3000})

  //page.evaluate(_ => {
  //  window.scrollBy(0, window.innerHeight);
  //});

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

  const snooze = ms => new Promise(resolve => setTimeout(resolve, ms));
  await snooze(10000)

  if (options.format == 'pdf') {
    await page.pdf(params)
  } else {
    await page.screenshot(params);
  }


  await browser.close();


  var rs = require('fs').createReadStream(output_filename);
  rs.pipe(process.stdout);
  
})();

