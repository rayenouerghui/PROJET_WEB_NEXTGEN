<?php

class PasswordReset {
    public $id;
    public $id_user;
    public $code;
    public $token;
    public $expiration;
    public $used;
    public $created_at;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->id_user = $data['id_user'] ?? null;
            $this->code = $data['code'] ?? '';
            $this->token = $data['token'] ?? '';
            $this->expiration = $data['expiration'] ?? null;
            $this->used = $data['used'] ?? 0;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
