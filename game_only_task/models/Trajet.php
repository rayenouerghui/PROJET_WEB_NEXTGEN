<?php
class Trajet
{
    private ?int $id_trajet = null;
    private int $id_livraison;
    private float $position_lat;
    private float $position_lng;
    private string $date_update;

    public function __construct(int $id_livraison, float $position_lat, float $position_lng, ?int $id_trajet = null, ?string $date_update = null)
    {
        $this->id_trajet = $id_trajet;
        $this->id_livraison = $id_livraison;
        $this->position_lat = $position_lat;
        $this->position_lng = $position_lng;
        $this->date_update = $date_update ?? date('Y-m-d H:i:s');
    }

    // GETTERS & SETTERS standards...
    public function getIdTrajet(): ?int { return $this->id_trajet; }
    public function getIdLivraison(): int { return $this->id_livraison; }
    public function getPositionLat(): float { return $this->position_lat; }
    public function getPositionLng(): float { return $this->position_lng; }
    public function setPosition(float $lat, float $lng): void {
        $this->position_lat = $lat;
        $this->position_lng = $lng;
    }
}