# File manager plugin for the Lithium framework

## Instalation

Checkout the code to either of your library directories:

	cd libraries
	git clone git://github.com/djordje/li3_filemanager.git

Include the library in yor `/app/config/bootstrap/libraries.php`

	Libraries::add('li3_filemanager');

## Usage

	Go to your application URL /file
	By default you are browsing libraries/li3_filemanager/resources/fs

## TODO:
	Problems on live Linux box, recursive delete not working, define 0777 mode on dir creation, fix redirect and refresh after delete action.