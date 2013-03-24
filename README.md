[![project status](http://stillmaintained.com/djordje/li3_filemanager.png)]
(http://stillmaintained.com/djordje/li3_filemanager)

# File manager plugin for the Lithium framework

## Instalation

Checkout the code to either of your library directories:

	cd libraries
	git clone git://github.com/djordje/li3_filemanager.git

Include the library in yor `/app/config/bootstrap/libraries.php`

	Libraries::add('li3_filemanager');

Require `session.php` in your app bootstrap file

## Dependencies

Your application shoud have:

	jQuery, Twitter Bootstrap (CSS and JS)

For building JS (li3_filemanager.min.js) you need:

	node with this modules: grunt, uglify-js, jshint

## Usage

	Go to your application URL /fm
	By default you are browsing app/webroot/img

## Add location

		Filesystem:
		Location::add('default', array(
			'adapter' => 'FileSystem',
			'location' => LITHIUM_APP_PATH.'/webroot/files'
		));

		Filesystem with file URL:
		Location::add('default', array(
			'adapter' => 'Filesystem',
			'location' => LITHIUM_APP_PATH.'/webroot/files'
			'url' => 'http://example.com/files/'
		));

		FTP:
		Locations::add('default', array(
			'adapter' => 'FTP',
			'host' => 'ftp.yourdomain.com',
			'username' => 'username@yourdomain.com',
			'password' => 'yourPassword'
		));

## Testing

	FileSystem adapter have location setup for testing
	FTP adapter can be tested if you add FTP location config