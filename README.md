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
yarn run build:debug
```

Once the development server has loaded, a new tab with location `localhost:8080` should open in your browser.

## Building the Flex.io Web App

In order to build the web app in debug mode, enter the following command:

```
yarn run build:debug
```

In order to build the web app in release mode, enter the following command:

```
yarn run build:release
```

Each of the above commands will run a webpack build script -- these scripts are located in the `/public/build` folder. It's not terribly important to understand these build scripts, but it is nice to have a good handle on what happens when the above commands are run.

**NOTE:** Before running any of the above commands, it is generally good practice to update the version in the `package.json` file to whatever the release version will be. This will keep the `package.json` version in sync with the release version that gets tagged in the Git repository.

After running either of the above build commands, it is important to do a `git status` to see what files have changed. For release builds, there will always be new files in the `/dist` folder that need to be added to the git repository using `git add` due to the fact that we add cache busting hash values to the end of our files now.

In general, from the `/public` folder, you can simply enter the following commands:

```
git add dist/*
git commit -a -m "Release build."
git push
```

