# Arquitectura de Herramienta Sembradora — test-simple-stock-flow-tool

> **Cliente CLI de Demostración**
> **Prueba técnica · Ficha ADSO 3413974**

---

## 1. Alcance
Herramienta CLI para poblar datos de prueba (usuarios, productos de muestra, ventas de prueba).

## 2. Invariante de Acceso (Regla Estricta)
- Esta herramienta actúa exclusivamente como **cliente HTTP** de la API (`test-simple-stock-flow-api`).
- **NUNCA se conecta directamente a la base de datos MySQL**.
- Siembra datos consumiendo los endpoints estándar autenticados de la API.
