const puppeteer = require('puppeteer');
const commandLineArgs = require('command-line-args')

const options = commandLineArgs([
   { name: 'url' },
   { name: 'format', defaultValue: "png" },
   { name: 'paper' },
   { name: 'landscape', type: Boolean },
   { name: 'fullPage', type: Boolean },
   { name: 'viewport.width', type: Number },
   { name: 'viewport.height', type: Number },
   { name: 'scale', type: Number },
   { name: 'scrollbars' },
   { name: 'sleep' }
]);


(async () => {
  var output_filename = '/tmp/output.' + options.format;
  

  //console.log(options);
  //process.exit()

  const browser = await puppeteer.launch({args:['--no-sandbox']});
  const page = await browser.newPage();
 // await page.goto(options.url,{waitUntil:"networkidle","networkIdleTimeout" : 3000});


 //const loaded = await page.waitForNavigation({
 // waitUntil: 'load'
 //});
 //await loaded

 //await page.goto(options.url,{waitUntil:"load"})
// await page.goto(options.url,{waitUntil:"networkidle2"})
 await page.goto(options.url);

 // await page.injectFile(`${__dirname}/kits.js`);
 //await page.addScriptTag({path:`${__dirname}/kits.js`})
 //await page.waitForFunction(() => { if (window.page2image.scrollToBottom()) {return window.page2image.checkIfImageBeenLoaded() } return false; })
 
  //await page.goto(options.url);

 // await page.waitForNavigation({timeout:3000})

 // await page.evaluate(_ => {
 //   window.scrollBy(0, window.innerHeight);
 // });

 // const snooze = ms => new Promise(resolve => setTimeout(resolve, ms));
 // await snooze(10000)


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

  if (options.hasOwnProperty('scale')) {
    params.scale = options.scale
  }

  if (options.hasOwnProperty('viewport.width') && options.hasOwnProperty('viewport.height')) {
    page.setViewport({width: options['viewport.width'], height: options['viewport.height']})
  }


  if (options.format == 'pdf') {
    await page.emulateMedia('screen');
    await page.pdf(params)
  } else {
    await page.screenshot(params);
  }


  await browser.close();


  var rs = require('fs').createReadStream(output_filename);
  rs.pipe(process.stdout);
  
  //const snooze = ms => new Promise(resolve => setTimeout(resolve, ms));
  //await snooze(3000)
})();

