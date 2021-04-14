# 🐳 Docker + PHP 7.4 + MySQL + Nginx + Symfony 5.2 

## Descripción

Pila completa para ejecutar Symfony 5.2 en contenedores Docker, usando la herramienta docker-compose.

El proyecto está compuesto por 3 containers:

- `nginx`, servidor web.
- `php`, contenedor PHP-FPM,version 7.4 de PHP.
- `db` contenedor de MySQL, con la imagen **MySQL 8.0**.

## Pasos para la instalación

1. Clonar el repo.

2. Run `docker-compose up -d`

3. Se despliegan 3 containers: 

```
Creating symfony-docker_db_1    ... done
Creating symfony-docker_php_1   ... done
Creating symfony-docker_nginx_1 ... done
```

4. En el atchivo .env ubicado la raiz de la proyecto symfony, reemplazar DATABASE_URL por:

```
DATABASE_URL=mysql://test_user:test_ps@db:3306/test_db?serverVersion=5.7
```


5. Acceder al container donde se encuentra PHP mediante el comando: 

```
docker exec -it  symfony-docker_php_1 bash
```

6.  instalar Symfony mediante el comando: 
```
composer install 
```

7. URL del proyecto: http://localhost:80/


## Extras

1. Acceder a la base de datos por consola:
- Me conecto al bash en el contenedor MySQL en ejecución:
```
docker exec -it  symfony-docker_db_1 bash
```
- Ejecuto el cliente MySQL desde el contenedor bash MySQL:

```
mysql -utest_user -ptest_ps
```


Good luck 😀