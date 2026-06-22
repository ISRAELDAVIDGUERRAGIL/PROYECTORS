# Guia de configuracion y consumo de la API

Este documento explica como esta construida la API, como configurarla para distintos entornos y como consumirla desde cualquier cliente (frontend separado, mobile app, Postman, otra API, etc.).

---

## Indice

1. [Como se hizo (stack y arquitectura)](#1-como-se-hizo)
2. [Endpoints completos](#2-endpoints-completos)
3. [Autenticacion: los dos modos](#3-autenticacion-los-dos-modos)
4. [Configuracion para consumir desde otro dominio](#4-configuracion-para-consumir-desde-otro-dominio)
5. [Consumo desde JavaScript (SPA separada)](#5-consumo-desde-javascript-spa-separada)
6. [Consumo desde PHP (servidor)](#6-consumo-desde-php-servidor)
7. [Consumo desde Postman o curl](#7-consumo-desde-postman-o-curl)
8. [Consumo desde mobile (iOS/Android/Flutter)](#8-consumo-desde-mobile)
9. [Configuracion de produccion](#9-configuracion-de-produccion)

---

## 1. Como se hizo

### Stack

| Capa | Tecnologia |
|------|-----------|
| Backend | Laravel 13 (PHP ^8.3) |
| Base de datos | MySQL |
| Autenticacion API | Laravel Sanctum (cookies SPA + tokens Bearer) |
| Validacion | Form Requests dedicados por entidad |
| Transformacion JSON | API Resources (CargoResource, EmpleadoResource, FuncionCargoResource) |
| Vistas web | Blade + Alpine.js + Tailwind CSS (Breeze) |
| Tests | Pest PHP (46 tests, SQLite en memoria) |
| Compilacion assets | Vite |

### Arquitectura

```
peticion → routes/api.php → middleware auth:sanctum
                              │
                              ├── cookie laravel_session? → resuelve usuario web
                              ├── header Authorization: Bearer xxx? → resuelve token
                              └── ninguna? → 401
                              │
                              ▼
                         FormRequest (validacion)
                              │
                              ▼
                         Controller (logica)
                              │
                              ▼
                         API Resource → JSON
```

### Que hay en cada capa

```
app/Http/
├── Controllers/Api/
│   ├── AuthController.php       ← login, register, logout
│   ├── CargoController.php      ← CRUD cargos (filtros: nombre, ids)
│   ├── EmpleadoController.php   ← CRUD empleados (filtros: nombre, estado, cargo, salario_min/max)
│   └── FuncionCargoController.php ← CRUD funciones (filtros: id_cargo, estado)
├── Requests/
│   ├── RegisterRequest.php      ← name, email, password (validacion registro)
│   ├── StoreCargoRequest.php    ← nombre_cargo (required|unique)
│   ├── UpdateCargoRequest.php   ← nombre_cargo (sometimes|unique)
│   ├── EmpleadoRequest.php      ← todos los campos del empleado
│   ├── StoreFuncionCargoRequest.php ← id_cargo, descripcion_funcion
│   └── UpdateFuncionCargoRequest.php
└── Resources/
    ├── CargoResource.php        ← incluye funciones anidadas
    ├── EmpleadoResource.php     ← incluye cargo anidado + nombre_completo
    └── FuncionCargoResource.php ← incluye cargo anidado
```

---

## 2. Endpoints completos

### Autenticacion (publicos)

| Metodo | Ruta | Body | Respuesta |
|--------|------|------|-----------|
| POST | `/api/register` | `name`, `email`, `password`, `password_confirmation` | `{ usuario, token, type: "Bearer" }` (201) |
| POST | `/api/login` | `email`, `password` | `{ usuario, token, type: "Bearer" }` (200) |
| POST | `/api/logout` | - (requiere auth) | `{ message }` (200) |

### Cargos (auth requerida)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/cargos` | Listar. Filtros: `?nombre=X&ids=1,2,3` |
| POST | `/api/cargos` | Crear. Body: `{ nombre_cargo, descripcion? }` |
| GET | `/api/cargos/{id}` | Ver uno (incluye funciones anidadas) |
| PUT | `/api/cargos/{id}` | Actualizar. Body: `{ nombre_cargo?, descripcion? }` |
| DELETE | `/api/cargos/{id}` | Eliminar |

### Empleados (auth requerida)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/empleados` | Listar. Filtros: `?nombre=X&estado=activo&cargo=1&cargos=1,2,3&salario_min=1000000&salario_max=5000000` |
| POST | `/api/empleados` | Crear. Body: `{ id_cargo, nombres, apellidos, fecha_nacimiento, fecha_ingreso, salario, estado }` |
| GET | `/api/empleados/{id}` | Ver uno (incluye cargo anidado) |
| PUT | `/api/empleados/{id}` | Actualizar parcial |
| DELETE | `/api/empleados/{id}` | Eliminar |

### Funciones de Cargo (auth requerida)

| Metodo | Ruta | Descripcion |
|--------|------|-------------|
| GET | `/api/funciones-cargo` | Listar. Filtros: `?id_cargo=1&estado=activo` |
| POST | `/api/funciones-cargo` | Crear. Body: `{ id_cargo, descripcion_funcion, estado? }` |
| GET | `/api/funciones-cargo/{id}` | Ver uno (incluye cargo anidado) |
| PUT | `/api/funciones-cargo/{id}` | Actualizar |
| DELETE | `/api/funciones-cargo/{id}` | Eliminar |

### Formato de respuesta (listados)

```json
{
  "data": [
    {
      "id_cargo": 1,
      "nombre_cargo": "Gerente",
      "descripcion": "Responsable del area",
      "funciones": [
        {
          "id_funcion": 1,
          "descripcion_funcion": "Supervisar equipo",
          "estado": "activo"
        }
      ]
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "from": 1, "last_page": 1, "total": 40 }
}
```

---

## 3. Autenticacion: los dos modos

Sanctum soporta dos modos simultaneamente. No hay que elegir uno:

### Modo A: Cookie SPA (mismo dominio)

- El usuario inicia sesion via web (`/login` de Breeze)
- Laravel establece la cookie `laravel_session`
- Los `fetch()` a `/api/*` envian la cookie automaticamente
- Sanctum la reconoce → usuario autenticado
- **Ventaja**: cero configuracion en el cliente
- **Limitacion**: solo mismo dominio (localhost, mismo servidor)

### Modo B: Token Bearer (cross-domain, mobile, Postman)

- El cliente hace `POST /api/login` con email y password
- Recibe un token: `"1|abc123def456..."`
- Lo envia en cada request: `Authorization: Bearer 1|abc123def456...`
- **Ventaja**: funciona desde cualquier cliente, cualquier dominio
- **Desventaja**: hay que gestionar el almacenamiento seguro del token (no localStorage en produccion)

### Como elige Sanctum cual usar

El middleware `auth:sanctum` revisa en orden:

```
1. ¿Hay cookie laravel_session de un estado "stateful"?
   → Si → usuario de sesion web
   
2. ¿Hay header Authorization: Bearer xxx?
   → Si → busca el token en personal_access_tokens → usuario del token
   
3. Ninguno → 401 Unauthenticated
```

---

## 4. Configuracion para consumir desde otro dominio

Si tu frontend va a estar en otro dominio (ej: `https://miapp.com` llamando a `https://api.miapp.com`), necesitas:

### Paso 1: Sanctum stateful domains

```env
# .env
SANCTUM_STATEFUL_DOMAINS=miapp.com,localhost:3000
```

Esto solo aplica si el frontend usa cookies. Si usas tokens Bearer, no hace falta.

### Paso 2: CORS

Laravel 13 no incluye `config/cors.php` por defecto (usa defaults integrados). Si necesitas personalizar, publica el archivo:

```bash
php artisan config:publish cors
```

Y configuralo:

```php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://miapp.com'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### Paso 3: APP_URL

```env
APP_URL=https://api.miapp.com
```

### Paso 4: Sanctum expiration (produccion)

```env
# .env - opcional, en segundos (ej: 1 hora)
SANCTUM_TOKEN_EXPIRATION=3600
```

Y en `config/sanctum.php`:

```php
'expiration' => env('SANCTUM_TOKEN_EXPIRATION', null),
```

---

## 5. Consumo desde JavaScript (SPA separada)

### Con tokens Bearer

```js
async function login(email, password) {
  const res = await fetch('https://api.miapp.com/api/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ email, password }),
  });
  const data = await res.json();
  return data.token; // guardar en memoria (variable) o httpOnly cookie
}

async function getCargos(token) {
  const res = await fetch('https://api.miapp.com/api/cargos', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json',
    },
  });
  return res.json();
}
```

### Con cookies SPA (mismo dominio, mismo servidor)

```js
// No necesitas token. Solo hacer fetch normalmente.
const res = await fetch('/api/cargos');
const data = await res.json();
```

### Reutilizando el api.js del proyecto

El archivo `resources/js/api.js` de este proyecto se puede copiar a cualquier frontend JS. Solo cambia el `baseUrl`:

```js
import { crearClienteAPI } from './api.js';
const api = crearClienteAPI('https://api.miapp.com/api');
```

---

## 6. Consumo desde PHP (servidor)

### Con Laravel HTTP Client

```php
use Illuminate\Support\Facades\Http;

// Login para obtener token
$response = Http::post('https://api.miapp.com/api/login', [
    'email' => 'admin@test.com',
    'password' => '12345678',
]);
$token = $response->json('token');

// Consumir endpoints protegidos
$cargos = Http::withToken($token)
    ->get('https://api.miapp.com/api/cargos')
    ->json();

$nuevoCargo = Http::withToken($token)
    ->post('https://api.miapp.com/api/cargos', [
        'nombre_cargo' => 'Gerente',
        'descripcion' => 'Nuevo cargo',
    ])
    ->json();
```

### Con Guzzle (sin Laravel)

```php
$client = new GuzzleHttp\Client();

$login = $client->post('https://api.miapp.com/api/login', [
    'json' => ['email' => 'admin@test.com', 'password' => '12345678'],
]);
$token = json_decode($login->getBody(), true)['token'];

$cargos = $client->get('https://api.miapp.com/api/cargos', [
    'headers' => ['Authorization' => "Bearer $token"],
]);
```

---

## 7. Consumo desde Postman o curl

### 1. Registrar o loguearse

```bash
# Registrar nuevo usuario
curl -s -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Admin","email":"admin@test.com","password":"12345678","password_confirmation":"12345678"}'

# O iniciar sesion
curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"12345678"}'
```

### 2. Usar el token

```bash
# Guardar token
TOKEN="1|abc123def456..."

# Listar cargos
curl -s http://localhost:8000/api/cargos \
  -H "Authorization: Bearer $TOKEN"

# Crear empleado
curl -s -X POST http://localhost:8000/api/empleados \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"id_cargo":1,"nombres":"Juan","apellidos":"Perez","fecha_nacimiento":"1990-05-15","fecha_ingreso":"2024-01-10","salario":2500000,"estado":"activo"}'

# Cerrar sesion (invalida el token)
curl -s -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer $TOKEN"
```

### En Postman

1. `POST /api/login` con `{ "email": "admin@test.com", "password": "12345678" }`
2. Copia el `token` de la respuesta
3. En la coleccion o request, pestaña **Auth** → Type: **Bearer Token** → pega el token
4. Todos los requests siguientes heredan el token

---

## 8. Consumo desde mobile

### Flutter

```dart
final response = await http.post(
  Uri.parse('https://api.miapp.com/api/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({'email': email, 'password': password}),
);
final token = jsonDecode(response.body)['token'];

final cargos = await http.get(
  Uri.parse('https://api.miapp.com/api/cargos'),
  headers: {'Authorization': 'Bearer $token'},
);
```

### Swift (iOS)

```swift
var request = URLRequest(url: URL(string: "https://api.miapp.com/api/login")!)
request.httpMethod = "POST"
request.setValue("application/json", forHTTPHeaderField: "Content-Type")
request.httpBody = try? JSONEncoder().encode(["email": email, "password": password])

let (data, _) = try await URLSession.shared.data(for: request)
let token = try JSONDecoder().decode(LoginResponse.self, from: data).token

var cargosRequest = URLRequest(url: URL(string: "https://api.miapp.com/api/cargos")!)
cargosRequest.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
```

### Kotlin (Android)

```kotlin
val loginBody = JSONObject().apply {
    put("email", email)
    put("password", password)
}

val loginResponse = Fuel.post("https://api.miapp.com/api/login")
    .header("Content-Type", "application/json")
    .body(loginBody.toString())
    .responseJson()

val token = loginResponse.second.obj().getString("token")

val cargos = Fuel.get("https://api.miapp.com/api/cargos")
    .header("Authorization", "Bearer $token")
    .responseJson()
```

---

## 9. Configuracion de produccion

### .env de produccion

```env
APP_NAME="API Empleados y Cargos"
APP_ENV=production
APP_KEY=base64:...          ← generar con php artisan key:generate
APP_DEBUG=false
APP_URL=https://api.tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_produccion
DB_USERNAME=usuario_seguro
DB_PASSWORD=password_segura

SANCTUM_STATEFUL_DOMAINS=tudominio.com

# Opcional: expirar tokens
# SANCTUM_TOKEN_EXPIRATION=3600

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.tudominio.com
```

### Checklist de produccion

- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generada y unica
- [ ] Base de datos con usuario de permisos limitados
- [ ] `APP_URL` con dominio real
- [ ] Cookies configuradas con `SESSION_SECURE_COOKIE=true`
- [ ] CORS limitado a dominios especificos (no `*`)
- [ ] HTTPS obligatorio (certificado SSL)
- [ ] `SANCTUM_STATEFUL_DOMAINS` con dominio real
- [ ] Rate limiting en endpoints (opcional, via `throttle` middleware)

### Agregar rate limiting (opcional)

En `routes/api.php`:

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::apiResource('cargos', CargoController::class);
    // ...
});
```

---

## Resumen: que modo de auth usar segun tu caso

| Caso | Modo | Configuracion necesaria |
|------|------|------------------------|
| Vistas Blade en el mismo Laravel | Cookie SPA | Nada, ya funciona |
| Frontend JS en el mismo dominio | Cookie SPA | Nada |
| Frontend JS en subdominio (`app.miapp.com` → `api.miapp.com`) | Cookie SPA | `SANCTUM_STATEFUL_DOMAINS` + CORS + `SESSION_DOMAIN` |
| Frontend JS en otro dominio distinto | Token Bearer | Solo CORS |
| Mobile app (iOS/Android) | Token Bearer | Nada (no aplica CORS) |
| Postman / curl / pruebas | Token Bearer | Nada (sin CORS) |
| Otra API / microservicio | Token Bearer | Nada |
