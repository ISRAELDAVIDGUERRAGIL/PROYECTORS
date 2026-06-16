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

```
POST /api/login
Content-Type: application/json

{"email":"admin@test.com","password":"12345678"}
```

Respuesta:
```json
{
  "usuario": { "id": 1, "name": "Admin", "email": "admin@test.com" },
  "token": "1|abc123def456...",
  "type": "Bearer"
}
```

### 2. Copiar el token

Copia el valor de `"token"` de la respuesta (ej: `1|abc123def456...`).

### 3. Usar el token

Pega el token donde dice `<token>` en el encabezado `Authorization: Bearer`:

```
GET /api/empleados
Authorization: Bearer <token>
```

```
POST /api/cargos
Content-Type: application/json
Authorization: Bearer <token>

{"nombre_cargo":"Gerente"}
```

### 4. Cerrar sesion

```
POST /api/logout
Authorization: Bearer <token>
```

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

## Acciones HTTP

> Copia el `"token"` de la respuesta del login y usalo donde dice `<token>`.

### 1. Login
```
POST /api/login
Content-Type: application/json

{"email":"admin@test.com","password":"12345678"}
```

### 2. Crear cargo
```
POST /api/cargos
Content-Type: application/json
Authorization: Bearer <token>

{"nombre_cargo":"Gerente","descripcion":"Responsable del area"}
```

### 3. Listar cargos
```
GET /api/cargos
Authorization: Bearer <token>
```

### 4. Mostrar cargo
```
GET /api/cargos/1
Authorization: Bearer <token>
```

### 5. Actualizar cargo
```
PUT /api/cargos/1
Content-Type: application/json
Authorization: Bearer <token>

{"nombre_cargo":"Gerente General"}
```

### 6. Crear empleado
```
POST /api/empleados
Content-Type: application/json
Authorization: Bearer <token>

{"id_cargo":1,"nombres":"Juan","apellidos":"Perez","fecha_nacimiento":"1990-05-15","fecha_ingreso":"2024-01-10","salario":2500000.50,"estado":"activo"}
```

### 7. Listar empleados
```
GET /api/empleados
Authorization: Bearer <token>
```

### 8. Listar empleados con filtros
```
GET /api/empleados?nombre=Juan&estado=activo
Authorization: Bearer <token>
```

### 9. Mostrar empleado
```
GET /api/empleados/1
Authorization: Bearer <token>
```

### 10. Mostrar empleado inexistente (404)
```
GET /api/empleados/99999
Authorization: Bearer <token>
```

### 11. Actualizar empleado
```
PUT /api/empleados/1
Content-Type: application/json
Authorization: Bearer <token>

{"nombres":"Carlos","salario":3000000}
```

### 12. Crear funcion de cargo
```
POST /api/funciones-cargo
Content-Type: application/json
Authorization: Bearer <token>

{"id_cargo":1,"descripcion_funcion":"Supervisar el equipo","estado":"activo"}
```

### 13. Listar funciones de cargo
```
GET /api/funciones-cargo
Authorization: Bearer <token>
```

### 14. Actualizar funcion
```
PUT /api/funciones-cargo/1
Content-Type: application/json
Authorization: Bearer <token>

{"descripcion_funcion":"Funcion actualizada"}
```

### 15. Eliminar funcion
```
DELETE /api/funciones-cargo/1
Authorization: Bearer <token>
```

### 16. Eliminar empleado
```
DELETE /api/empleados/1
Authorization: Bearer <token>
```

### 17. Eliminar cargo
```
DELETE /api/cargos/1
Authorization: Bearer <token>
```

### 18. Logout
```
POST /api/logout
Authorization: Bearer <token>
```

### 19. Acceso sin token (debe dar 401)
```
GET /api/empleados
```

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
