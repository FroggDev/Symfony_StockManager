@echo Off




(for %%a in (materialize\*.js) do (

	uglifyjs %%a -o %%a.min.js
	
	REM Doing concatenation to formatedPath var with list of path to add
		call set "formatedPath=%%formatedPath%% %%a.min.js"
))

echo %formatedPath%

uglifyjs %formatedPath% -o test.min.js