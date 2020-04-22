<?php
define("FL_ARRAY_SUBACCESS", false);




/*------------------------------------------------*//*/
	DO NOT MODIFY UNTIL YOU WANT TO BREAK SMTHING
/*------------------------------------------------//*/
	//Quantified by classes registration routines
	define("OS_WINDOWS",0);
	define("OS_LINUX",1);
	define("OS_MACOS",2);
	define("OS_ANDROID",3);
	define("OS_FREEBSD",4);
	define("OS_SOLARIS",5);
	define("OS_WPE",6);
	define("OS_IOS",7);
	define("OS_UNIX",8);
	define("OS_DOS",9);
define("_FL_VCL_", extension_loaded("php4delphi_internal")  /*Delphi Compiled PHP Program*/
	|| extension_loaded("delphi_vcl") /*Native PHP Interpreter with Delphi Components*/);
define("_FL_LCL_", extension_loaded("php4lazarus_internal") /*Lazarus Compiled PHP Program*/
		|| extension_loaded("lazarus_lcl") /*Native PHP Interpreter with Lazarus Components*/ );
define("_FL_FMX_", extension_loaded("delphi_fmx") /*Native PHP Interpreter with FMX Components*/ );

define("_FL_PASCALTYPES_", _FL_VCL_ || _FL_LCL_ || _FL_FMX_ );
//Note: This constants is only used to define functions for LCL<>VCL<>FMX compatibility with FireLion

define("_FL_LIBUI_", extension_loaded("ui"));
//LIB UI
define("_FL_WX_", extension_loaded("wxwidgets"));
//WxWidgets
define("_FL_WB_", extension_loaded("WinBinder"));
//WindowBinder
define("_FL_T_TERMBOX_", extension_loaded("TermBox"));
//TerminalBox Terminal
define("_FL_NEWT_", extension_loaded("newt"));
//RedHat Newt Library
define("_FL_T_NCURSES_", extension_loaded("ncurses"));
//New Curses Terminal

//ALL OF THIS CONSTANTS IS USED TO PROVIDE COMPATIBILITY, IF YOU WANT TO, YOU CAN USE FireLion End-Point Data TO DRAW SMTH WITH
//LIBRARIES SPECIFIED UPPER. IF YOU WANT TO, YOU CAN DEFINE THIS CONSTANTS ON YOUR OWN AND USE COMPATIBILITY CLASSES TO PORT YOUR PROJECT

//Constants specified below are used for OS-conditional code
function __FL__GET__OS__()
{
	$uname = php_uname('s');
    $os_a =
    [
            "/windows nt/i"    		=>  ["Windows", php_uname('r')],
            "/windows xp/i"         =>  ["Windows", 5.1],
            "/windows me/i"         =>  ["Windows", 4.2],
            "/win98/i"              =>  ["Windows", 4.1],
            "/win95/i"              =>  ["Windows 95", 4],
            "/win16/i"              =>  ["Windows", 3],
            "/macintosh|mac os x/i" =>  ["MacOS", 10],
            "/mac_powerpc/i"        =>  ["Mac OS", 9],
            "/linux/i"              =>  "Linux",
            "/ubuntu/i"             =>  "Ubuntu",
            "/iphone/i"             =>  "IOS",
            "/ipad/i"               =>  "IOS",
			"/ipod/i"               =>  "IOS",
            "/android/i"            =>  "Android",
            "/blackberry/i"         =>  "BlackBerry",
            "/freebsd/i"            =>	"FreeBSD",
			"/windows pe/i"			=>	"WindowsPE",
			"/unix/i"				=>	"Unix",
			"/solaris/i"            =>	"Solaris",
    ];
 
    foreach ($os_a as $key => $value)
	{ 
        if (preg_match($key, $uname ))
            return $value;
    }   
 
    return ["DOS",3];
}


//Constants used for internal functions and supporting routines, etc.
define("_FL_USE_FFI_", extension_loaded("ffi"));
define("_FL_USE_WINCALL_", extension_loaded("wincall"));
define("_FL_LOADED_", extension_loaded("FireLion"));
$os = __FL__GET__OS__();
$os_arr = ["Windows", "Linux", "MacOS", "Android", "FreeBSD", "Solaris", "WPE", "IOS", "UNIX", "DOS"];
define("FL_OS", $os_arr[$os[0]]);
define("FL_OSV", $os[1]);

/*
define("_FLD_OS_",		0);
define("_FLD_OPENGL_",	1);
define("_FLD_DIRECTX_",	2);
define("_FLD_WEB_",		3);
define("_FLD_TEXT_",	4);
define("_FLD_QT_",		5);
define("_FLD_GTK_",		6);
define("_FLD_GDI_",		7);
define("_FLD_FL_",		8);
*/