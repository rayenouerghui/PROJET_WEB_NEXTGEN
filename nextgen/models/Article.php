<?php

class Article
{
    private ?int $id_article = null;
    private string $titre;
    private string $content;
    private string $date_publication;
    private string $categorie;
    private ?string $image = null;
    private int $id_auteur;
    private int $rating_count = 0;
    private int $rating_sum = 0;

    // Extra property for comment count (not in DB)
    public int $comment_count = 0;

    public function __construct(
        string $titre,
        string $content,
        string $date_publication,
        string $categorie,
        int $id_auteur,
        ?string $image = null,
        ?int $id_article = null,
        int $rating_count = 0,
        int $rating_sum = 0
    ) {
        $this->id_article = $id_article;
        $this->titre = $titre;
        $this->content = $content;
        $this->date_publication = $date_publication;
        $this->categorie = $categorie;
        $this->image = $image;
        $this->id_auteur = $id_auteur;
        $this->rating_count = $rating_count;
        $this->rating_sum = $rating_sum;
    }

    // --- Getters ---
    public function getIdArticle(): ?int { return $this->id_article; }
    public function getTitre(): string { return $this->titre; }
    public function getContent(): string { return $this->content; }
    public function getDatePublication(): string { return $this->date_publication; }
    public function getCategorie(): string { return $this->categorie; }
    public function getImage(): ?string { return $this->image; }
    public function getIdAuteur(): int { return $this->id_auteur; }
    public function getRatingCount(): int { return $this->rating_count; }
    public function getRatingSum(): int { return $this->rating_sum; }
    public function getAverageRating(): float {
        return $this->rating_count > 0 ? round($this->rating_sum / $this->rating_count, 1) : 0;
    }

    // --- Setters ---
    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function setContent(string $content): void { $this->content = $content; }
    public function setDatePublication(string $date): void { $this->date_publication = $date; }
    public function setCategorie(string $categorie): void { $this->categorie = $categorie; }
    public function setImage(?string $image): void { $this->image = $image; }
    public function setIdAuteur(int $id_auteur): void { $this->id_auteur = $id_auteur; }
    public function setRatingCount(int $count): void { $this->rating_count = $count; }
    public function setRatingSum(int $sum): void { $this->rating_sum = $sum; }
}
?>