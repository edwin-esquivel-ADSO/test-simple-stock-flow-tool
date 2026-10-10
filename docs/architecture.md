# Arquitectura de Herramienta Sembradora — test-simple-stock-flow-tool

> **Cliente CLI de Demostración y Simulación de Negocio**  
> **Prueba técnica · Ficha ADSO 3413974**

---

## 1. Alcance
Herramienta CLI en PHP para poblar datos de prueba (usuarios, productos y ventas de muestra).

---

## 2. Invariante de Acceso (Regla Estricta)
- Esta herramienta actúa exclusivamente como **cliente HTTP** de la API (`test-simple-stock-flow-api`).
- **NUNCA se conecta directamente a la base de datos MySQL**.
- Siembra datos consumiendo los endpoints estándar autenticados de la API mediante cURL y JSON.
- Asegura que todas las invariantes de negocio de la API (validaciones sintácticas, restricciones de stock y unicidad) sean respetadas rigurosamente durante la siembra.
