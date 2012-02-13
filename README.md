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

## Add location

		Filesystem:
		Location::add('default', array(
			'adapter' => 'Filesystem',
			'location' => LITHIUM_APP_PATH.'/libraries/li3_filemanager/resources/fs'
		));

		Filesystem with file URL:
		Location::add('default', array(
			'adapter' => 'Filesystem',
			'location' => LITHIUM_APP_PATH.'/webroot/img',
			'url' => 'http://example.com/img/'
		));

		FTP:
		Locations::add('default', array(
			'adapter' => 'Ftp',
			'host' => 'ftp.yourdomain.com',
			'username' => 'username@yourdomain.com',
			'password' => 'yourPassword'
		));

## Testing

	Filesystem adapter have location setup for testing
	Ftp adapter can be tested if you add FTP location config

## INFO:
	- If PHP runs in safe mode plugin does not work correctly

## TO DO:
	- Add ability to filter uploadable files!