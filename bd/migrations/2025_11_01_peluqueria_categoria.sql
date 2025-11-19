-- Crear columna de categoría en servicios (si no existe) y etiquetar peluquería
ALTER TABLE servicios
  ADD COLUMN IF NOT EXISTS categoria VARCHAR(50) NULL AFTER nombre;

-- Marcar como peluquería los servicios típicos (ajusta según tus nombres)
UPDATE servicios
   SET categoria = 'peluqueria'
 WHERE LOWER(nombre) LIKE '%peluquer%'
    OR LOWER(nombre) LIKE '%bañ%'
    OR LOWER(nombre) LIKE '%ban%'
    OR LOWER(nombre) LIKE '%cort%'
    OR LOWER(nombre) LIKE '%spa%';

-- Índice opcional para acelerar filtros por categoría
CREATE INDEX IF NOT EXISTS idx_servicios_categoria ON servicios(categoria);

