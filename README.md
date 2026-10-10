# test-simple-stock-flow-tool

> **Prueba técnica · Ficha ADSO 3413974**
> Horario: de **9:00 a. m. a 3:00 p. m.** (15:00)

Este repositorio contiene las **utilidades** de *Simple Stock Flow*: el sembrador de datos de demostración (CLI Seeder). **Empieza vacío a propósito**: se construye en el fork de cada aprendiz.

## Instrucciones

Cada aprendiz debe **crear el fork** de los seis repositorios del proyecto y **resolver el proyecto
con el spec planteado**.

1. Hacer fork, a su cuenta de GitHub, de cada repositorio de la tabla del final.
2. Leer el spec en [`test-simple-stock-flow-docs`](https://github.com/code-sena/test-simple-stock-flow-docs).
   Se entrega en dos versiones: `spec-python/` y `spec-.net/`.
3. Desarrollar en los forks.

## El reto se desarrolla con React y PHP (Laravel)

El spec está escrito para Python y para .NET, pero el reto **no** se hace en esos lenguajes:

| Capa | Tecnología del reto |
|---|---|
| Frontend | React |
| Backend | PHP con Laravel |

Lo que el spec define sobre el negocio —historias, criterios de aceptación, reglas, contrato de la
API, modelo de datos— se respeta. Lo que define sobre la tecnología se traduce a React y Laravel.

## La prueba no consiste en escribir el código

El propósito principal es ver la **capacidad de desempeño con SDD** (*Spec-Driven Development*,
desarrollo guiado por especificación): cómo se lee, se interpreta y se aplica una especificación
para llevarla a un stack distinto. El código es el medio, no el fin.

## Los seis repositorios

| Repositorio | Qué va ahí |
|---|---|
| [`test-simple-stock-flow-docs`](https://github.com/code-sena/test-simple-stock-flow-docs) | El spec: `spec-python/` y `spec-.net/` |
| [`test-simple-stock-flow-api`](https://github.com/code-sena/test-simple-stock-flow-api) | Backend en PHP (Laravel) |
| [`test-simple-stock-flow-app`](https://github.com/code-sena/test-simple-stock-flow-app) | Frontend en React |
| [`test-simple-stock-flow-page`](https://github.com/code-sena/test-simple-stock-flow-page) | Sitio público estático de presentación |
| [`test-simple-stock-flow-infra`](https://github.com/code-sena/test-simple-stock-flow-infra) | Contenedores, red, volúmenes y motor de base de datos vacío |
| [`test-simple-stock-flow-tool`](https://github.com/code-sena/test-simple-stock-flow-tool) | Utilidades: sembrador de datos de demostración |

---

# Documentación Técnica de la Herramienta — Nivel Senior

## 1. Alcance y Propósito del CLI Seeder

`test-simple-stock-flow-tool` es un cliente de consola autónomo (CLI) diseñado para poblar el sistema con datos de demostración representativos para pruebas de carga, demostraciones comerciales y validación end-to-end de flujos de negocio.

### Invariante Arquitectónica Estricta:
- **Cliente HTTP Puro:** Este script actúa **exclusivamente como cliente HTTP de la API REST**.
- **PROHIBIDO el acceso a Base de Datos:** **Nunca** se conecta por socket, PDO o TCP a MySQL. Todas las operaciones respetan las políticas de autenticación, roles, invariantes de dominio y validaciones de la API oficial.

---

## 2. Flujo de Ejecución del Sembrado

```
                                SECUENCIA DE SEMBRADO
                                
  CLI (seed.php)                                            API (test-simple-stock-flow-api)
        │                                                                 │
        ├────── 1. POST /api/auth/login (Admin credentials) ─────────────►│
        │◄───── Retorna accessToken de Admin ─────────────────────────────┤
        │                                                                 │
        ├────── 2. POST /api/auth/register (Vendedor Demo) ──────────────►│
        │◄───── 201 Created ──────────────────────────────────────────────┤
        │                                                                 │
        ├────── 3. GET /api/categories (Mapeo de UUIDs) ─────────────────►│
        │◄───── Lista de categorías oficiales ────────────────────────────┤
        │                                                                 │
        ├────── 4. POST /api/products (Lote de productos con stock) ─────►│
        │◄───── Productos persistidos con version = 1 ────────────────────┤
        │                                                                 │
        ├────── 5. POST /api/auth/login (Credenciales Vendedor Demo) ────►│
        │◄───── Retorna accessToken de Vendedor ──────────────────────────┤
        │                                                                 │
        ├────── 6. POST /api/sales (Transacciones de venta atómicas) ────►│
        │◄───── Stock descontado y líneas congeladas ─────────────────────┤
        │                                                                 │
        ├────── 7. GET /api/reports/sales (Validación de balances) ──────►│
        │◄───── Reporte financiero consolidado en COP ────────────────────┤
```

---

## 3. Variables de Entorno de Configuración

| Variable | Valor por Defecto | Descripción |
|---|---|---|
| `API_URL` | `http://localhost:8000` | Dirección base del backend a sembrar. |
| `ADMIN_USERNAME` | `admin` | Nombre del usuario administrador preexistente. |
| `ADMIN_PASSWORD` | `Admin123456*` | Contraseña del usuario administrador. |

---

## 4. Instrucciones de Ejecución

### Prerrequisitos:
- PHP 8.1+ con extensión `curl` habilitada.
- Contenedores de la API y Base de Datos encendidos y con migraciones aplicadas.

### Ejecución Directa:
```bash
# Ejecutar contra API local
php seed.php

# O especificando variables de entorno personalizadas
API_URL=http://localhost:8000 ADMIN_PASSWORD=Admin123456* php seed.php
```

### Salida Esperada:
El comando emitirá confirmación paso a paso con emojis indicadores:
- Autenticación de Admin exitosa.
- Registro o verificación de vendedor demo.
- 5 categorías mapeadas.
- Lote de productos de ferretería sembrados (Martillo, Taladro, Bombillos LED, etc.).
- Ventas registradas exitosamente con descuento de existencias.
- Validación final del balance financiero reportado por `/api/reports/sales`.
