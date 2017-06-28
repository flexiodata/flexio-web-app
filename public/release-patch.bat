call npm version patch
call yarn run build:release
call erase dist\css\*.map
call git add dist\*.js
call git add dist\css\*.css
call git add dist\assets\*
call git add package.json
