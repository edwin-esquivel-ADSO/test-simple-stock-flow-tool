# Flujo Detallado de Sembrado — test-simple-stock-flow-tool

> **Catálogo de Datos Sembrados y Validación de Respuestas**  
> **Prueba técnica · Ficha ADSO 3413974**

---

## 1. Productos Sembrados por Defecto

| Producto | Categoría | Precio Base (COP) | Stock Inicial |
|---|---|---|---|
| Martillo de Uña Curva 16oz | Herramientas | $28.500 | 25 unidades |
| Taladro Percutor 650W | Herramientas | $185.000 | 10 unidades |
| Bombillo LED 12W Luz Blanca | Electricidad | $8.500 | 100 unidades |
| Cable Eléctrico THHN #12 (Metro) | Electricidad | $3.200 | 500 metros |
| Llave de Paso PVC 1/2 pulgada | Fontanería | $6.800 | 40 unidades |
| Tubo Sanitario PVC 2m | Fontanería | $18.900 | 30 unidades |
| Pintura Vinilo Tipo 1 Blanco (Galón) | Pinturas | $48.000 | 15 galones |
| Cinta Enmascarar 1 pulgada | General | $4.500 | 60 unidades |

---

## 2. Validación de Venta

El sembrador simula una venta multiproducto:
- 2 Martillos de Uña Curva
- 5 Bombillos LED 12W
- 1 Pintura Vinilo Blanco

Verifica que el stock restante en la API refleje el descuento exacto y que el total reportado coincida matemáticamente con la suma de las líneas de venta.
