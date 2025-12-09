<?php

class Historique {
    public $id_historique;
    public $id_user;
    public $type_action;
    public $description;
    public $date_action;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id_historique = $data['id_historique'] ?? null;
            $this->id_user = $data['id_user'] ?? null;
            $this->type_action = $data['type_action'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->date_action = $data['date_action'] ?? null;
        }
    }
}
