# Como funciona el proyecto

## Arquitectura general

```
┌──────────────────────────────────────────────────────────┐
│  NAVEGADOR                                                │
│                                                           │
│  /login (Breeze Blade)                                    │
│    │  POST → valida credenciales → cookie laravel_session │
│    │  Redirige a /dashboard                               │
│    ▼                                                      │
│  /cargos (Blade + Alpine)                                 │
│    │  Alpine.init() → fetch('/api/cargos')                │
│    │  Navegador adjunta cookie automaticamente            │
│    ▼                                                      │
│  Sanctum middleware lee la cookie → usuario autenticado   │
│    │                                                      │
│    ▼                                                      │
│  JSON response → Alpine renderiza la tabla                │
└──────────────────────────────────────────────────────────┘
```

El proyecto tiene dos caras que conviven:

| Capa | Ruta | Autenticacion | Motor |
|------|------|---------------|-------|
| **Web (vistas)** | `/cargos`, `/empleados`, `/funciones-cargo` | Breeze (sesion) | Blade + Alpine.js |
| **API (datos)** | `/api/cargos`, `/api/empleados`, `/api/funciones-cargo` | Sanctum (cookie o token) | Laravel |
| **API (auth)** | `/api/login`, `/api/register`, `/api/logout` | Publica / Sanctum | Laravel |

---

## Como funciona la autenticacion SPA (cookies)

### 1. El usuario inicia sesion por la web

Las rutas de Breeze (`/login`, `/register`) usan sesiones de Laravel (cookie `laravel_session` + `XSRF-TOKEN`).

### 2. La cookie viaja sola a la API

Como la API y las vistas estan en el mismo dominio (`localhost:8000`), el navegador adjunta la cookie automaticamente en cada `fetch()` a `/api/*`.

### 3. Sanctum reconoce la cookie

El middleware `auth:sanctum` en `routes/api.php` esta configurado para aceptar tanto tokens Bearer como cookies de sesion SPA. Si detecta la cookie `laravel_session`, resuelve el usuario autenticado sin necesidad de token explícito.

### Configuracion relevante

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS',
    'localhost,localhost:8000,127.0.0.1,127.0.0.1:8000')),
```

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cargos', CargoController::class);
    // ...
});
```

---

## El cliente API (`resources/js/api.js`)

Archivo unico que centraliza todas las llamadas fetch. Lo importan las 3 vistas.

```js
import { crearClienteAPI } from '/js/api.js';
const api = crearClienteAPI();

api.listar('cargos')              // GET    /api/cargos
api.crear('cargos', datos)        // POST   /api/cargos
api.actualizar('cargos', id, d)   // PUT    /api/cargos/{id}
api.eliminar('cargos', id)        // DELETE /api/cargos/{id}
```

### Que hace por dentro

1. **CSRF automatico**: lee la cookie `XSRF-TOKEN` y la envia como header `X-XSRF-TOKEN` en POST/PUT/DELETE. Sin esto, Laravel rechaza la peticion con 419.
2. **Headers fijos**: `Content-Type: application/json` y `Accept: application/json`.
3. **Errores**: si el status no es 2xx, lanza el body como error (para que Alpine pueda manejarlo).

---

## Como funciona el patron CRUD en las vistas

Cada vista sigue la misma estructura con Alpine.js:

```
┌─────────────────────────────────────────────┐
│  <div x-data="crudX" x-init="cargar()">     │
│                                              │
│  1. AL CARGAR LA PAGINA                      │
│     x-init="cargar()" → fetch a /api/x       │
│     → guarda en this.lista                   │
│                                              │
│  2. TABLA                                     │
│     <template x-for="item in lista">          │
│       renderiza cada fila                    │
│                                              │
│  3. MODAL (crear/editar)                      │
│     @open-modal.window → abre modal          │
│     El mismo form sirve para crear y editar  │
│     si editando != null → PUT                │
│     si editando == null → POST               │
│                                              │
│  4. ELIMINAR                                  │
│     confirm() → DELETE → recargar lista      │
│                                              │
└─────────────────────────────────────────────┘
```

### Ciclo de vida Alpine

```
abrirForm()          → limpia form, editando=null, abre modal
editar(item)         → llena form con datos, editando=id, abre modal
guardar()            → POST o PUT segun editando, cierra modal, recarga
eliminar(id)         → DELETE, recarga
```

### Comunicacion entre componentes

- El `index.blade.php` contiene la data Alpine con `lista`, `form`, `editando`
- El `form.blade.php` es el modal incluido con `@include`
- Se comunican con eventos Alpine: `$dispatch('open-modal')` / `$dispatch('close-modal')`

---

## Estructura de archivos relevante

```
resources/
├── js/
│   ├── api.js              ← Cliente fetch reutilizable
│   └── app.js              ← Bootstrap de Alpine (Breeze)
├── views/
│   ├── layouts/
│   │   ├── app.blade.php   ← Layout principal (slot + navigation)
│   │   └── navigation.blade.php ← Menu con links a modulos
│   ├── cargos/
│   │   ├── index.blade.php ← Tabla CRUD de cargos
│   │   └── form.blade.php  ← Modal crear/editar cargo
│   ├── empleados/
│   │   ├── index.blade.php ← Tabla CRUD + filtros
│   │   └── form.blade.php  ← Modal con select de cargo
│   ├── funciones/
│   │   ├── index.blade.php ← Tabla CRUD + filtros
│   │   └── form.blade.php  ← Modal con select de cargo
│   └── dashboard.blade.php

routes/
├── api.php                 ← Endpoints REST (Sanctum)
└── web.php                 ← Vistas Blade (Breeze)
```

---

## Flujo completo: crear un cargo

```
1. Usuario visita /login → inicia sesion → cookie laravel_session

2. Usuario hace clic en "Cargos" en el menu → GET /cargos
   → Laravel devuelve cargos/index.blade.php

3. Alpine.init() ejecuta cargar():
   → fetch('/api/cargos')
   → Navegador envia cookie laravel_session + XSRF-TOKEN
   → Sanctum resuelve usuario → CargoController@index
   → JSON: { data: [...], meta: { total, per_page, ... } }

4. Usuario hace clic en "+ Nuevo Cargo"
   → Alpine: abrirForm() → $dispatch('open-modal')
   → Modal se abre con form vacio

5. Usuario llena nombre_cargo y descripcion, clic en "Guardar"
   → Alpine: guardar()
   → editando es null → fetch POST /api/cargos
   → api.js agrega X-XSRF-TOKEN de la cookie
   → Sanctum + StoreCargoRequest validan
   → 201 Created

6. Modal se cierra, Alpine vuelve a llamar cargar()
   → Tabla actualizada con el nuevo cargo
```

---

## Los 3 modulos

| Modulo | Endpoint API | Campos |
|--------|-------------|--------|
| Cargos | `/api/cargos` | nombre_cargo, descripcion |
| Empleados | `/api/empleados` | id_cargo, nombres, apellidos, fecha_nacimiento, fecha_ingreso, salario, estado |
| Funciones | `/api/funciones-cargo` | id_cargo, descripcion_funcion, estado |

Empleados y Funciones cargan la lista de cargos en paralelo (`Promise.all`) para llenar los `<select>` en filtros y formularios.

---

## Por que no localStorage ni tokens manuales

- La cookie `laravel_session` la maneja el navegador automaticamente.
- Sanctum la lee sin configuracion adicional.
- No hay que guardar, renovar ni adjuntar tokens manualmente.
- CSRF ya lo maneja el helper `csrf()` en `api.js`.
