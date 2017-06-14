call npm version minor
call yarn run build:release
call erase dist\*.map
call erase dist\css\*.map
call git add dist\*.js
call git add dist\css\*.css
call git add package.json
