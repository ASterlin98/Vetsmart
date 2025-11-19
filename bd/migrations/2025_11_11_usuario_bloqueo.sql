-- Migración: Agregar columna de bloqueo a usuarios
ALTER TABLE usuarios ADD COLUMN is_blocked TINYINT(1) NOT NULL DEFAULT 0 AFTER estado;