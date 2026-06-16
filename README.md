# API Empleados y Cargos

API REST para gestion de empleados, cargos y funciones de cargo. Construida con Laravel 13, Sanctum y Pest.

## Requisitos

- PHP ^8.3 (extensiones: PDO, pdo_mysql, mbstring, xml, curl, json, tokenizer, ctype)
- Composer
- MySQL
- Node.js (opcional, para assets)

## Instalacion paso a paso

```bash
# 1. Clonar el repositorio
git clone https://github.com/ISRAELDAVIDGUERRAGIL/PROYECTORS.git
cd proyecto-api

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env

# 4. Generar key
php artisan key:generate

# 5. Crear base de datos en MySQL
#    Entrar a MySQL y crear:
#    CREATE DATABASE db_3066552;

# 6. Editar .env con tus datos de MySQL
#    DB_DATABASE=db_3066552
#    DB_USERNAME=root
#    DB_PASSWORD=tu_password

# 7. Ejecutar migraciones y seeders (siempre limpia y recrea todo)
php artisan migrate:fresh --seed

# 9. Ejecutar tests
php artisan test

# Tambien con Pest
php vendor/bin/pest

# Test especifico
php vendor/bin/pest --filter="crear cargo"

# 10. Iniciar servidor local
php artisan serve
```

## Usuario de prueba

| Campo | Valor |
|-------|-------|
| Email | `admin@test.com` |
| Password | `12345678` |

## Como obtener y usar el token

### 1. Iniciar sesion

```bash
curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"12345678"}'
```

Respuesta:
```json
{
  "usuario": { "id": 1, "name": "Admin", "email": "admin@test.com" },
  "token": "1|abc123def456...",
  "type": "Bearer"
}
```

### 2. Copiar el token de la respuesta

Copia el valor de `"token"` que aparece en la respuesta (ejemplo: `1|abc123def456...`).

### 3. Usar el token

Pega el token copiado en el encabezado `Authorization: Bearer` de cada peticion (reemplaza `<token>` por el valor real):

```bash
curl -s http://localhost:8000/api/empleados \
  -H "Authorization: Bearer <token>"

curl -s -X POST http://localhost:8000/api/cargos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"nombre_cargo":"Gerente"}'
```

### 4. Cerrar sesion

```bash
curl -s -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer <token>"
```

> Si tienes `jq` instalado puedes extraer el token automaticamente:
> ```bash
> TOKEN=$(curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin@test.com","password":"12345678"}' | jq -r '.token')
> ```

## Endpoints

### Autenticacion

| Metodo | Ruta | Auth | Descripcion |
|--------|------|------|-------------|
| POST | `/api/login` | No | Iniciar sesion |
| POST | `/api/logout` | Si | Cerrar sesion |

### Cargos

| Metodo | Ruta | Auth | Descripcion |
|--------|------|------|-------------|
| GET | `/api/cargos` | Si | Listar cargos |
| POST | `/api/cargos` | Si | Crear cargo |
| GET | `/api/cargos/{id}` | Si | Mostrar cargo |
| PUT | `/api/cargos/{id}` | Si | Actualizar cargo |
| DELETE | `/api/cargos/{id}` | Si | Eliminar cargo |

### Empleados

| Metodo | Ruta | Auth | Descripcion |
|--------|------|------|-------------|
| GET | `/api/empleados` | Si | Listar empleados |
| POST | `/api/empleados` | Si | Crear empleado |
| GET | `/api/empleados/{id}` | Si | Mostrar empleado |
| PUT | `/api/empleados/{id}` | Si | Actualizar empleado |
| DELETE | `/api/empleados/{id}` | Si | Eliminar empleado |

### Funciones de Cargo

| Metodo | Ruta | Auth | Descripcion |
|--------|------|------|-------------|
| GET | `/api/funciones-cargo` | Si | Listar funciones |
| POST | `/api/funciones-cargo` | Si | Crear funcion |
| GET | `/api/funciones-cargo/{id}` | Si | Mostrar funcion |
| PUT | `/api/funciones-cargo/{id}` | Si | Actualizar funcion |
| DELETE | `/api/funciones-cargo/{id}` | Si | Eliminar funcion |

## Filtros

### Empleados

```
GET /api/empleados?nombre=Juan
GET /api/empleados?estado=activo
GET /api/empleados?cargo=1
GET /api/empleados?cargos=1,2,3
GET /api/empleados?salario_min=1000000&salario_max=5000000
```

### Cargos

```
GET /api/cargos?nombre=Gerente
GET /api/cargos?ids=1,2,3
```

### Funciones

```
GET /api/funciones-cargo?id_cargo=1
GET /api/funciones-cargo?estado=activo
```

## Trabajar con curl paso a paso

Abre dos terminales. En una corre el servidor y en otra ejecuta los comandos.

### Terminal 1: Iniciar servidor
```bash
php artisan serve
```

### Terminal 2: Probar la API

```bash
# 1. LOGIN - guardar token en variable
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"12345678"}' | jq -r '.token')
echo $TOKEN

# 2. SIN TOKEN (debe dar 401)
curl -s http://localhost:8000/api/empleados

# 3. CREAR CARGO
curl -s -X POST http://localhost:8000/api/cargos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"nombre_cargo":"Gerente","descripcion":"Responsable del area"}'

# 4. LISTAR CARGOS
curl -s http://localhost:8000/api/cargos \
  -H "Authorization: Bearer $TOKEN"

# 5. MOSTRAR CARGO
curl -s http://localhost:8000/api/cargos/1 \
  -H "Authorization: Bearer $TOKEN"

# 6. ACTUALIZAR CARGO
curl -s -X PUT http://localhost:8000/api/cargos/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"nombre_cargo":"Gerente General"}'

# 7. CREAR EMPLEADO
curl -s -X POST http://localhost:8000/api/empleados \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"id_cargo":1,"nombres":"Juan","apellidos":"Perez","fecha_nacimiento":"1990-05-15","fecha_ingreso":"2024-01-10","salario":2500000.50,"estado":"activo"}'

# 8. LISTAR EMPLEADOS
curl -s http://localhost:8000/api/empleados \
  -H "Authorization: Bearer $TOKEN"

# 9. LISTAR EMPLEADOS CON FILTROS
curl -s "http://localhost:8000/api/empleados?nombre=Juan&estado=activo" \
  -H "Authorization: Bearer $TOKEN"

# 10. MOSTRAR EMPLEADO
curl -s http://localhost:8000/api/empleados/1 \
  -H "Authorization: Bearer $TOKEN"

# 11. MOSTRAR EMPLEADO 404
curl -s http://localhost:8000/api/empleados/99999 \
  -H "Authorization: Bearer $TOKEN"

# 12. ACTUALIZAR EMPLEADO
curl -s -X PUT http://localhost:8000/api/empleados/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"nombres":"Carlos","salario":3000000}'

# 13. CREAR FUNCION
curl -s -X POST http://localhost:8000/api/funciones-cargo \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"id_cargo":1,"descripcion_funcion":"Supervisar el equipo","estado":"activo"}'

# 14. LISTAR FUNCIONES
curl -s http://localhost:8000/api/funciones-cargo \
  -H "Authorization: Bearer $TOKEN"

# 15. ACTUALIZAR FUNCION
curl -s -X PUT http://localhost:8000/api/funciones-cargo/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"descripcion_funcion":"Funcion actualizada"}'

# 16. ELIMINAR FUNCION
curl -s -X DELETE http://localhost:8000/api/funciones-cargo/1 \
  -H "Authorization: Bearer $TOKEN"

# 17. ELIMINAR EMPLEADO
curl -s -X DELETE http://localhost:8000/api/empleados/1 \
  -H "Authorization: Bearer $TOKEN"

# 18. ELIMINAR CARGO
curl -s -X DELETE http://localhost:8000/api/cargos/1 \
  -H "Authorization: Bearer $TOKEN"

# 19. LOGOUT (invalida el token)
curl -s -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer $TOKEN"

# 20. ACCESO SIN TOKEN (debe dar 401)
curl -s http://localhost:8000/api/empleados
```

> Si no tienes `jq` instalado, usa `apt install jq` (Linux), `brew install jq` (Mac) o descargalo de https://jqlang.github.io/jq/. En Windows PowerShell puedes extraer el token manualmente:
> ```bash
> $RESPONSE=curl -s -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"admin@test.com","password":"12345678"}'
> $TOKEN=($RESPONSE | ConvertFrom-Json).token
> ```

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── CargoController.php
│   │   ├── EmpleadoController.php
│   │   └── FuncionCargoController.php
│   ├── Requests/
│   │   ├── EmpleadoRequest.php
│   │   ├── StoreCargoRequest.php
│   │   ├── UpdateCargoRequest.php
│   │   ├── StoreFuncionCargoRequest.php
│   │   └── UpdateFuncionCargoRequest.php
│   └── Resources/
│       ├── CargoResource.php
│       ├── EmpleadoResource.php
│       └── FuncionCargoResource.php
├── Models/
│   ├── Cargo.php
│   ├── Empleado.php
│   ├── FuncionCargo.php
│   └── User.php
database/
├── factories/
│   ├── CargoFactory.php
│   ├── EmpleadoFactory.php
│   ├── FuncionCargoFactory.php
│   └── UserFactory.php
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2026_06_05_120030_create_cargos_table.php
│   ├── 2026_06_05_120035_create_empleados_table.php
│   └── 2026_06_05_120036_create_funciones_cargo_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── CargoSeeder.php
    ├── EmpleadoSeeder.php
    └── FuncionCargoSeeder.php
routes/
└── api.php
tests/
├── Feature/
│   ├── AuthTest.php
│   ├── CargoTest.php
│   ├── EmpleadoApiTest.php
│   ├── FuncionCargoTest.php
│   └── ExampleTest.php
└── Pest.php
```

## Base de datos

### Tabla cargos
| Columna | Tipo | Descripcion |
|---------|------|-------------|
| id_cargo | BIGINT (PK) | ID del cargo |
| nombre_cargo | VARCHAR(100) | Nombre del cargo |
| descripcion | TEXT | Descripcion del cargo |

### Tabla empleados
| Columna | Tipo | Descripcion |
|---------|------|-------------|
| id_empleado | BIGINT (PK) | ID del empleado |
| id_cargo | BIGINT (FK) | Cargo del empleado |
| nombres | VARCHAR(100) | Nombres |
| apellidos | VARCHAR(100) | Apellidos |
| fecha_nacimiento | DATE | Fecha de nacimiento |
| fecha_ingreso | DATE | Fecha de ingreso |
| salario | DECIMAL(12,2) | Salario |
| estado | ENUM('activo','inactivo') | Estado |

### Tabla funciones_cargo
| Columna | Tipo | Descripcion |
|---------|------|-------------|
| id_funcion | BIGINT (PK) | ID de la funcion |
| id_cargo | BIGINT (FK) | Cargo asociado |
| descripcion_funcion | TEXT | Descripcion |
| estado | ENUM('activo','inactivo') | Estado |
