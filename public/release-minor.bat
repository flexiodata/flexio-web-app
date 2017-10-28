call npm version minor
call yarn
call yarn run deploy
call erase dist\css\*.map
call git add dist\*.js
call git add dist\css\*.css
call git add dist\assets\*
call git add package.json
