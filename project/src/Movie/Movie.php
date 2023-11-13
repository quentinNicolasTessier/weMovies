<?php

namespace App\Movie;

class Movie
{
    private int $id;
    private string $title;
    private string $description;
    private string $releaseDate;
    private float $rating;
    private int $voteNumber;
    private array $producers;

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param string $releaseDate
     * @param float $rating
     * @param int $voteNumber
     * @param array $producers
     */
    public function __construct(int $id,string $title, string $description, string $releaseDate, float $rating, int $voteNumber, array $producers)
    {
        $this->id=$id;
        $this->title = $title;
        $this->description = $description;
        $this->releaseDate = $releaseDate;
        $this->rating = $rating;
        $this->voteNumber = $voteNumber;
        $this->producers = $producers;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }



    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getReleaseDate(): string
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(string $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    public function getVoteNumber(): int
    {
        return $this->voteNumber;
    }

    public function setVoteNumber(int $voteNumber): void
    {
        $this->voteNumber = $voteNumber;
    }

    public function getProducers(): array
    {
        return $this->producers;
    }

    public function setProducers(array $producers): void
    {
        $this->producers = $producers;
    }



}