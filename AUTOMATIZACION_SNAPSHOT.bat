@echo off

cd /d "D:\VIDEOJUEGOS"

set NOMBRE_INSTANTANEA=Guardado_%DATE:~10,4%%DATE:~4,2%%DATE:~7,2%_%TIME:~0,2%%TIME:~3,2%%TIME:~6,2%

VBoxManage snapshot VISOR_FOTOGRAFIAS_WEB take "%NOMBRE_INSTANTANEA%"
