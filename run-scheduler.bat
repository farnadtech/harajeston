@echo off
:loop
"D:\xamp8.1\php\php.exe" "D:\xamp8.1\htdocs\haraj\artisan" schedule:run >> "D:\xamp8.1\htdocs\haraj\storage\logs\scheduler.log" 2>&1
timeout /t 60 /nobreak > nul
goto loop
