# ToDo-Co
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e0dcd24af7774fe58cd340a4861845b9)](https://app.codacy.com/gh/PavelKlimovich/ToDo-Co/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
## Technologies
<ul>
 <li>PHP 8.1.2</li>
 <li>Symfony 5.4</li> 
 <li>MySQL 5.7.34</li> 
</ul>

<hr>

## Installation and configuration

1. Clone project with `git clone https://github.com/PavelKlimovich/ToDo-Co.git
2. Install dependencies with `cd ToDo-Co && composer install`
3. In`.env` fill up your database configuration
example config in MYSQL: `DATABASE_URL: `DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7.34&charset=utf8"`
4. Create database with: `php bin/console doctrine:database:create` (or with symfony Client: `symfony console doctrine:database:create`)
5. Create schema on database with: `php bin/console doctrine:migrations:migrate -n`
6. Load the fixture with :  `php bin/console doctrine:fixtures:load`

7. Run the server : `symfony server:start`


## Admin connection access

"username": `admin@gmail.com`,
"password": `password`