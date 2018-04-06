# flexio
The Flex.io Web App

## Getting Started

To set up the Flex.io web app for development or build purposes, all of the necessary dependencies need to be included. Flex.io uses a number of modules from NPM and needs to bring these down. If you have not yet installed NPM, do so now:

> NPM can be downloaded from: **https://nodejs.org/en/download/** (download the Windows 64-bit version)

We will also be utilizing Yarn (a deterministic package manager similar to NPM). If you have not yet installed Yarn, do so now:

> Yarn can be downloaded from: **https://yarnpkg.com/en/docs/install**
  
Once both NPM and Yarn have been installed, in the `/public` folder, run one of the following commands:

```
npm install
yarn
```

NPM and Yarn can be used interchangeably for building, however, it is important to use the `yarn` command when bringing down dependencies from NPM since it utilizes the `yarn.lock` file to make sure the exact structure of dependencies is brought down on each machine. We'll use Yarn for the rest of our examples.

After running `yarn`, if you do a directory listing, you'll notice that there is now a `node_modules` folder which contains many files. 

**NOTE:** The `node_modules` folder is created from the list of dependencies in the `package.json` (see below). You should never manually add or remove files/folders from this folder and, of utmost importance, this folder should **NEVER** be committed or pushed to the git repository.

## Folder Structure

Understanding the basic folder structure and flow of the build files is important to being able to build the Flex.io web app. It's also important to know this information in order to run the development server.

The following files and folders are pertinent for understanding the build process:

* /public/package.json
* /public/build

## NPM and Yarn

All executables that can be run from the command prompt for running the development server or building the app are listed in the `scripts` node in the `package.json` file. Command are entered from the command line as follows:

```
npm run <script name>
yarn run <script name>
```

## Running the Development Server

In order to run the development server, enter the following command:

```
yarn run dev
```

Once the development server has loaded, a new tab with location `localhost:8080` should open in your browser.

## Building the Flex.io Web App

Before building the web app, it's important to make sure that all of the node module dependencies have been loaded and updated. To do this, got to the `/public` folder and enter the following command:

`yarn`

Once that process is complete, you will need to update the version of the app in the `package.json` file. This ensures that any version information that is used in the build process is up-to-date with what the release version will be when it is tagged on GitHub.

```
{
  "name": "Flex.io",
  "version": "1.1.6",
  "description": "Flex.io Client Web Application",
  ...
}
```

Once the `package.json` file has been edited and saved, you're ready to actually build the app.

In order to build the web app in debug mode, enter the following command:

```
yarn && yarn run build:debug
```

In order to build the web app in release mode, enter the following command:

```
yarn && yarn run build:release
```

Each of the above commands will run a webpack build script -- these scripts are located in the `/public/build` folder. It's not terribly important to understand these build scripts, but it is nice to have a good handle on what happens when the above commands are run. The primary difference between the two build modes is that debug mode will not add cache busting hash strings to the files that are output.

After running either of the above build commands, it is important to do a `git status` to see what files have changed. For release builds, there will always be new files in the `/dist` folder that need to be added to the git repository using `git add` due to the fact that we add cache busting hash values to the end of our files now.

In general, from the `/public` folder, you can simply enter the following commands with the appropriate release version:

For patch releases:

```
release-patch
git commit -a -m "Release build (v1.4.5)."
git push
```

For minor releases:

```
release-minor
git commit -a -m "Release build (v1.5.0)."
git push
```

For major releases

```
npm version major
yarn run build:release
erase dist\css\*.map
git add dist\*.js
git add dist\css\*.css
git add dist\assets\*
git add package.json
git commit -a -m "Release build (v2.0.0)."
git push
```

Once the push is complete, open up the [Flex.io Web App](https://github.com/flexiodata/flexio) on GitHub and go to the `Releases` tab and click 'Draft a new release' button. Name the release the same thing as the version number in the `package.json` file, but prefixed with the letter 'v' (e.g. for version 1.1.6, both the Tag version and the Release title would be 'v1.1.6').. Add any pertinent release notes and click the 'Publish release' button to finalize the release.

## Updating a package dependency (e.g. Flex.io JS SDK)

To update a package dependency, do the following:

```
yarn add <package-name>
git commit -a -m "Updated <package-name>."
```

For example, to update the Flex.io JS SDK to the latest version, you would do the following:

```
yarn add flexio-sdk-js
git commit -a -m "Updated Flex.io JS SDK."
```
