# Talos Digital Quiz System

This project is an to quiz candidates web app.
Developed in [AngularJS](http://angularjs.org/), [ZendFramework2](http://framework.zend.com/), [Doctrine](http://www.doctrine-project.org/) and [Mongo](http://www.mongodb.org/)

## Apache2 VirtualHost sample
```
<VirtualHost *:80>
	ServerName quiz.talosdigital.com
	SetEnv APPLICATION_ENV development
	php_value error_reporting 30719
	php_flag display_errors on
	DocumentRoot /vagrant/htdocs/quiz-engine/angular/app
	<Directory /vagrant/htdocs/quiz-engine/angular/app>
		Options -Indexes FollowSymLinks
		AllowOverride All
		Order allow,deny
		allow from all
	</Directory>
</VirtualHost>
```

### ZendFramework2 dependecies

```
cd zf2
php composer.phar self-update
php composer.phar update
```

### Running unit tests

TODO

### License

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

## Contact

For more information please check out http://www.talosdigital.com/
