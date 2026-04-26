# Liberxo — Sistema de Gestión de Clientes y Expedientes

Aplicación web para la gestión integral de expedientes jurídicos de Ley de Segunda Oportunidad y exoneración de deudas, desarrollada como Trabajo de Fin de Grado.

## Funcionalidades principales

- **Gestión de clientes** (particulares y empresas) con consorte, expedientes y documentos asociados.
- **Expedientes jurídicos** con autómata de estados procesales para LSO sin masa, LSO con plan de pagos y otros procedimientos. Incluye registro de publicaciones BOE/RPC, transiciones de estado e historial completo de cambios.
- **Plan de pagos de honorarios** con generación automática de cuotas, registro de pagos por método (transferencia, efectivo, tarjeta, domiciliación), facturación y subida del PDF de cada factura.
- **Plan de pagos a acreedores** para expedientes LSO con plan.
- **Gestión documental** con vinculación a clientes y expedientes.
- **Calendario de eventos** con recordatorios personales y alertas automáticas generadas por el autómata.
- **Sistema de roles** (Administrador y Gestor) con autenticación, recuperación de contraseña por email y limitación de intentos de login (rate limiting por IP y por cuenta).

## Stack tecnológico

- **Backend**: PHP 8.2 · Laravel 12 · Eloquent ORM
- **Base de datos**: PostgreSQL 15
- **Frontend**: Blade · Tailwind CSS v4 · JavaScript vanilla · Vite (bundler)
- **Email**: Resend
- **Infraestructura**: Docker Compose (4 contenedores: app PHP-FPM, Nginx, PostgreSQL, Node.js on-demand para builds)

## Requisitos previos

- Docker Desktop con Docker Compose
- Git

No es necesario tener PHP, Composer, Node ni PostgreSQL instalados localmente: todo se ejecuta en contenedores.

## Instalación y arranque

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd SistemaDeGestionEyC

# 2. Copiar la plantilla de variables de entorno
cp src/.env.example src/.env

# 3. Levantar los contenedores (app, nginx, postgres)
docker compose up -d

# 4. Instalar dependencias de PHP
docker compose exec app composer install

# 5. Generar la clave de la aplicación
docker compose exec app php artisan key:generate

# 6. Crear y poblar la base de datos con datos de prueba
docker compose exec app php artisan migrate:fresh --seed

# 7. Compilar los assets de frontend (CSS y JS)
docker compose run --rm node
```

La aplicación quedará disponible en **http://localhost:8000**.

## Credenciales por defecto

Tras ejecutar el seeder se crean los siguientes usuarios:

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | `admin@liberxo.com` | `password` |
| Gestores (5 generados) | distintos emails generados | `password` |

Adicionalmente, el seeder genera 5 empresas, 30 clientes, ~20 expedientes con tareas, documentos y notificaciones para poder probar todas las funcionalidades.

## Configuración del envío de emails

El proyecto está configurado para enviar emails (recuperación de contraseña, notificaciones) a través de [Resend](https://resend.com/). Para que funcione es necesario indicar una `RESEND_API_KEY` válida en `src/.env`. Si no se configura, los emails se registran en `storage/logs/mail.log` (driver `log` por defecto).

## Comandos útiles

```bash
# Ver logs en tiempo real
docker compose logs -f app

# Acceder a la base de datos PostgreSQL
docker compose exec postgres psql -U user -d test

# Recompilar assets en modo desarrollo
docker compose run --rm node npm run dev

# Ejecutar tests
docker compose exec app php artisan test

# Acceder a Tinker (consola interactiva)
docker compose exec app php artisan tinker

# Resetear la base de datos manteniendo los datos de prueba
docker compose exec app php artisan migrate:fresh --seed
```

## Estructura del proyecto

```
.
├── docker/                  # Configuración de Nginx
├── docker-compose.yaml      # Orquestación de contenedores
├── Dockerfile               # Imagen de PHP-FPM
└── src/                     # Aplicación Laravel
    ├── app/
    │   ├── Http/Controllers # Controladores (Cliente, Expediente, ...)
    │   ├── Models/          # Modelos Eloquent
    │   └── Mail/            # Plantillas de email
    ├── database/
    │   ├── migrations/      # Esquema de la base de datos
    │   ├── seeders/         # Datos de prueba
    │   └── factories/       # Generadores de datos aleatorios
    ├── resources/
    │   ├── views/           # Plantillas Blade
    │   ├── css/             # Estilos (procesados por Tailwind)
    │   └── js/              # JavaScript del cliente
    └── routes/web.php       # Definición de rutas HTTP
```

## Licencia

Proyecto académico — Trabajo de Fin de Grado.
