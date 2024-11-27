# IDBI Invoice Recorder Challenge

API REST que permite registrar comprobantes en formato XML y consultarlos. A partir de estos comprobantes, se extrae
información relevante como los datos del emisor y receptor, los artículos o líneas incluidas y los montos totales.

La API utiliza JSON Web Token (JWT) para la autenticación.

## Componentes

El proyecto se ha desarrollado utilizando las siguientes tecnologías:

- PHP
- Nginx (servidor web)
- MySQL (base de datos)
- MailHog (gestión de envío de correos)

## Preparación del Entorno

El proyecto cuenta con una implementación de Docker Compose para facilitar la configuración del entorno de desarrollo.

> ⚠️ Si no estás familiarizado con Docker, puedes optar por otra configuración para preparar tu entorno. Si decides
> hacerlo, omite los pasos 1 y 2.

Instrucciones para iniciar el proyecto

1. Levantar los contenedores con Docker Compose:

```bash
docker compose up -d
```

2. Acceder al contenedor web:

```bash
docker exec -it idbi-invoice-recorder-challenge-web-1 bash
```

3. Configurar las variables de entorno:

```bash
cp .env.example .env
```

4. Configurar el secreto de JWT en las variables de entorno (genera una cadena de texto aleatoria):

```bash
JWT_SECRET=<random_string>
```

5. Instalar las dependencias del proyecto:

```bash
composer install
```

6. Generar una clave para la aplicación:

```bash
php artisan key:generate
```

7. Ejecutar las migraciones de la base de datos:

```bash
php artisan migrate
```

8. Rellenar la base de datos con datos iniciales:

```bash
php artisan db:seed
```

9. Verificar que los procesos en segundo plano se ejecuten correctamente:

```bash
artisan queue:work
```

10. Procesar los vouchers existentes en la base de datos:

```bash
php artisan app:process-existing-vouchers
```

**¡Y listo!** Ahora puedes empezar a desarrollar.

## Uso

La API estará disponible en: http://localhost:8080/api/v1

### Gestión de Correos

Para visualizar los correos enviados por la aplicación, puedes acceder a la interfaz de MailHog desde tu navegador
en: http://localhost:8025

### Colección de Postman
Puedes probar la API con la colección de Postman disponible en el siguiente enlace:
🔗 https://github.com/DavidRCHS/InvoiceRecorder/blob/main/InvoiceRecorder.postman_collection.json
