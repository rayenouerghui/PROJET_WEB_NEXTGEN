<?php

class Reservation
{
    private int $id_reservation;
    private int $id_evenement;
    private string $nom_complet;
    private string $email;
    private string $telephone;
    private int $nombre_places;
    private string $message;
    private int $points_generes;
    private string $date_reservation;

    public function __construct($id_evenement = 0, $nom_complet = '', $email = '', $telephone = '', $nombre_places = 0, $message = '', $id_reservation = 0, $date_reservation = '')
    {
        $this->id_evenement = $id_evenement;
        $this->nom_complet = $nom_complet;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->nombre_places = $nombre_places;
        $this->message = $message;
        $this->points_generes = 0;
        $this->id_reservation = $id_reservation;
        $this->date_reservation = $date_reservation ? $date_reservation : date('Y-m-d H:i:s');
    }

    // ==================== GETTERS ====================

    public function getIdReservation(): int
    {
        return $this->id_reservation;
    }

    public function getIdEvenement(): int
    {
        return $this->id_evenement;
    }

    public function getNomComplet(): string
    {
        return $this->nom_complet;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getNombrePlaces(): int
    {
        return $this->nombre_places;
    }

    public function getPointsGeneres(): int
    {
        return $this->points_generes;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDateReservation(): string
    {
        return $this->date_reservation;
    }

    // ==================== SETTERS ====================

    public function setNomComplet(string $nom_complet): void
    {
        $this->nom_complet = $nom_complet;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setNombrePlaces(int $nombre_places): void
    {
        $this->nombre_places = $nombre_places;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setPointsGeneres(int $points): void
    {
        $this->points_generes = $points;
    }

    public function setIdReservation(int $id_reservation): void
    {
        $this->id_reservation = $id_reservation;
    }

    public function setIdEvenement(int $id_evenement): void
    {
        $this->id_evenement = $id_evenement;
    }

    public function setDateReservation(string $date_reservation): void
    {
        $this->date_reservation = $date_reservation;
    }
}
?>
