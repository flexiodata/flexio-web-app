call npm version patch
call yarn
call yarn add flexio-sdk-js
call yarn run deploy
call erase dist\css\*.map
call git add dist\*.js
call git add dist\css\*.css
call git add dist\assets\*
call git add package.json
