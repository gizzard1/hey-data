## Qué es Hey-data

Hey-data es una agenda de citas, punto de venta, manejo de inventarios, CRM, API de hey-data-mobile y más... Construido con [Laravel 10](https://laravel.com), Livewire 2, Bootstrap 5 y MySQL.

## Requisitos del sistema

- PHP >= 8.2
- Composer
- Laragon (recomendado para entornos Windows)
- MySQL o cualquier otra base de datos compatible con Laravel

## Características de Hey-data

- Gestión de citas
- Punto de venta (POS)
- Manejo de inventarios
- CRM integrado
- API para aplicaciones móviles
- Interfaz de usuario intuitiva

## Instalación

1. Clona el repositorio en el directorio que maneja Laragon (C:/laragon/www/):
   ```bash
    git clone https://github.com/gizzard1/hey-data.git
    cd hey-data
   ```

2. Instala las dependencias usando Composer:
   ```bash
    composer install
    ```

3. Configura tu archivo `.env`:
    ```bash
    cp .env.example .env
    ```
   Luego, edita el archivo `.env` para configurar tu base de datos y otras variables de entorno.

4. Ejecuta el archivo mysql-seed.sql para crear la base de datos y las tablas necesarias.

6. Levanta la aplicación (acción recomendada con [Laragon](https://laragon.org/) en un entorno de Windows):
   - Abre Laragon y asegúrate de que Apache y MySQL estén corriendo.
   - Accede a la aplicación desde tu navegador en `http://hey-data.test`.

7. Crea una cuenta de administrador:
   - Accede a la aplicación y regístrate con un correo electrónico válido.
   - Automáticamente se te asignará el rol de administrador.

## Contribuciones

¡Las contribuciones son bienvenidas! Si deseas contribuir, por favor sigue estos pasos:
1. Haz un fork del repositorio.
2. Crea una nueva rama para tu característica o corrección de errores.
3. Realiza tus cambios y haz commit.
4. Envía un pull request describiendo tus cambios.

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.