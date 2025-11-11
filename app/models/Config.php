<?php
// app/models/Config.php

class Config {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtiene todos los ajustes de la tabla de configuración.
     * Devuelve un array asociativo donde la clave es 'clave' y el valor es 'valor'.
     * @return array
     */
    public function getAllSettings() {
        $stmt = $this->db->query("SELECT clave, valor FROM config");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $settings;
    }

    /**
     * Actualiza múltiples ajustes en la base de datos.
     * Si una clave no existe, la crea. Si existe, la actualiza.
     * @param array $settings - Un array asociativo de [clave => valor].
     * @return bool
     */
    public function updateSettings($settings) {
        if (empty($settings) || !is_array($settings)) {
            return false;
        }

        $this->db->beginTransaction();

        try {
            // Usamos INSERT ... ON DUPLICATE KEY UPDATE para manejar tanto la creación como la actualización
            // Usar VALUES(valor) es más robusto que volver a vincular el parámetro.
            $sql = "INSERT INTO config (clave, valor) VALUES (:clave, :valor)
                    ON DUPLICATE KEY UPDATE valor = VALUES(valor)";

            $stmt = $this->db->prepare($sql);

            foreach ($settings as $key => $value) {
                $stmt->execute([
                    ':clave' => $key,
                    ':valor' => $value
                ]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Opcional: registrar el error
            error_log("Error al actualizar la configuración: " . $e->getMessage());
            return false;
        }
    }
}