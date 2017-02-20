@echo off

if "%FrameworkDir%"=="" (
    echo error: please run this script from the Visual Studio Command Prompt environment
	goto end
)


pushd %~dp0

echo Cleaning...
rmdir curl /s /q

echo Unzipping...
..\..\library\zipwin32\unzip supp\curl-7.51.0.zip -d temp
move temp\curl-7.51.0 curl
rmdir temp



echo Building...
pushd curl\winbuild
nmake /f makefile.vc mode=static VC=14 DEBUG=no ENABLE_IDN=no ENABLE_WINSSL=yes
nmake /f makefile.vc mode=static VC=14 DEBUG=yes ENABLE_IDN=no ENABLE_WINSSL=yes

popd
popd

:end
