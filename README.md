* Se debe disponer de una instalación de docker versión 20.10.7 o superior

* Se debe disponer de una instalación de docker-compose versión 1.29.2 o superior

* Establecer las variables de entorno, copiar el archivo ejemplo y editar el archivo **.env**

```sh
$ cp -f .env.example .env
```

* Establecer datos de administrador

```sh
$ cp -f admin_data.json.example storage/app/admin_data.json
```

* Editar los datos para el usuario administrador en el archivo **storage/admin_data.json**

* Editar el dominio en el archivo **sanctum.php**, dentro de la variable *stateful* reemplazar *localhost* por el dominio correcto

```sh
$ cp -f docker/.env.example laradock/.env
$ cp -f docker/docker-compose.yml laradock/docker-compose.yml
$ cd laradock
```

* Editar los nombres de usuario, contraseñas y puertos a utilizar en el archivo **laradock/.env**

```sh
$ docker-compose build --parallel nginx php-fpm workspace mysql
$ docker-compose up -d nginx php-fpm workspace mysql
```

* Compilar el código

```sh
$ docker-compose exec --user laradock workspace php artisan key:generate
$ docker-compose exec --user laradock workspace composer install
$ docker-compose exec --user laradock workspace yarn install
$ docker-compose exec --user laradock workspace yarn prod
$ docker-compose exec --user laradock workspace php artisan migrate:install
$ docker-compose exec --user laradock workspace php artisan migrate
```

* Cargar datos iniciales

```sh
$ docker-compose exec --user laradock workspace php artisan db:seed --class=DatabaseSeeder
```
