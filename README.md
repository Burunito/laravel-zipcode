# practice-laravel
Practica laravel

1. Clonamos el repositorio 		git clone https://github.com/Burunito/practice-laravel.git 
2. Inicializamos las dependencias 		composer install
3. Renombramos el .env-example a .env y configuramos nuestras variables de la base de datos acorde a nuestro servidor de bd
4. Inicializamos las keys de laravel php artisan key:generate
5. Configuramos nuestro vhost si usamos xampp, iniciamos nuestro php y nuestra base de datos, o si no tenemos, iniciamos el proyecto con php artisan serve
6. Corremos las migraciones 	php artisan migrate
7. Si no hemos cargado la informacion, entramos a nuestra pagina principal, iniciamos sesion  con el usuario 
Admin
admin
y corremos la siguiente ruta
http://127.0.0.1:8000/import-data
Para cargar los datos del excel y agilizar las busquedas de los codigos
Este es un ejemplo para una busqueda
http://127.0.0.1:8000/api/zip-codes/46960
