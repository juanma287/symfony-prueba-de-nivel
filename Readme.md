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

4. Reemplazar DATABASE_URL, variable de entorno de Symfony (en symfony/env) por:

DATABASE_URL=mysql://test_user:test_ps@db:3306/test_db?serverVersion=5.7

Observación: Puede cambiar el nombre, usuario y contraseña de la base de datos en el archivo `env` en la raíz del proyecto.


5. Acceder al container donde se encuentra PHP (acordarse de haberlo levantado) mediante el comando: docker exec -it  symfony-docker_php_1 bash

Ahora  dentro del container, instalar Symfony con el comando: composer install 


6. URL del proyecto: http://localhost:80/



Good luck 😀