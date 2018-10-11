call yarn
REM call yarn add flexio-sdk-js
call git rm dist\js\*.js
call git rm dist\css\*.css
call git reset HEAD dist\css\tachyons.min.css
call git checkout -- dist\css\tachyons.min.css
call yarn run deploy
call erase dist\css\*.map
call git add dist\js\*.js
call git add dist\css\*.css
call git add dist\assets\*
call git add package.json
