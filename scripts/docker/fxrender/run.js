const puppeteer = require('puppeteer');
const commandLineArgs = require('command-line-args')

const options = commandLineArgs([
   { name: 'url' },
   { name: 'format' },
   { name: 'width' },
   { name: 'height' },
   { name: 'scrollbars' },
   { name: 'sleep' }
]);


(async () => {
  const browser = await puppeteer.launch({args:['--no-sandbox']});
  const page = await browser.newPage();
  await page.goto(options.url);
  await page.screenshot({path: '/tmp/output.png'});

  await browser.close();


  var rs = require('fs').createReadStream('/tmp/output.png');
  rs.pipe(process.stdout);
  
})();

