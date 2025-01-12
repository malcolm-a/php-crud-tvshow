<?php

declare(strict_types=1);

namespace Entity;

use Database\MyPdo;
use Entity\Exception\EntityNotFoundException;
use PDO;

class Episode
{
    private ?int $id;
    private int $seasonId;
    private string $name;
    private string $overview;
    private int $episodeNumber;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSeasonId(): int
    {
        return $this->seasonId;
    }

    public function setSeasonId(int $seasonId): void
    {
        $this->seasonId = $seasonId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): void
    {
        $this->overview = $overview;
    }

    public function getEpisodeNumber(): int
    {
        return $this->episodeNumber;
    }

    public function setEpisodeNumber(int $episodeNumber): void
    {
        $this->episodeNumber = $episodeNumber;
    }

    /**
     * Retrieves an episode by its ID from the database.
     *
     * This static method executes a SQL query to select all columns from the `episode` table
     * for a given episode ID. It returns the result as an `Episode` object if found, otherwise
     * it throws an exception.
     *
     * @param int $episodeId The ID of the episode to retrieve.
     * @return Episode The Episode object corresponding to the specified ID.
     */
    public static function findById(int $episodeId): Episode
    {
        $request = MyPdo::getInstance()->prepare(
            <<<SQL
    SELECT id, seasonId, name, overview, episodeNumber
    FROM episode
    WHERE id = {$episodeId}
    SQL
        );
        $request->execute();
        $request->setFetchMode(PDO::FETCH_CLASS, Episode::class);
        $result = $request->fetch();
        if (empty($result)) {
            throw new EntityNotFoundException('Episode', $episodeId);
        }
        return $result;
    }

    /**
     * Deletes the current object from the Database (Episode)
     * and set its identifier to null.
     *
     * @return Episode Returns the current instance
     */
    public function delete(): Episode
    {
        $request = MyPdo::getInstance()->prepare(
            <<<SQL
    DELETE FROM episode
    WHERE id = :id
SQL
        );
        $request->execute(['id' => $this->id]);
        $this->setId(null);
        return $this;
    }

    /**
     * Updates the current object (Episode) in the "Episode" table.
     *
     * @return Episode Returns the current object
     */
    protected function update(): Episode
    {
        $request = MyPdo::getInstance()->prepare(
            <<<SQL
    UPDATE episode
    SET seasonId = :seasonId, name = :name, overview = :overview, episodeNumber = :episodeNumber
    WHERE id = :id
SQL
        );
        $request->execute([
            'seasonId' => $this->seasonId,
            'name' => $this->name,
            'overview' => $this->overview,
            'episodeNumber' => $this->episodeNumber,
            'id' => $this->id
        ]);
        return $this;
    }

    /**
     * Allows inserting a new Episode into the "Episode" table.
     * The new id is auto-incremented.
     *
     * @return Episode Returns the current instance
     */
    protected function insert(): Episode
    {
        $request = MyPdo::getInstance()->prepare(
            <<<SQL
    INSERT INTO episode (seasonId, name, overview, episodeNumber) 
    VALUES (:seasonId, :name, :overview, :episodeNumber)
SQL
        );
        $request->execute([
            'seasonId' => $this->seasonId,
            'name' => $this->name,
            'overview' => $this->overview,
            'episodeNumber' => $this->episodeNumber
        ]);
        $this->setId((int) MyPdo::getInstance()->lastInsertId());
        return $this;
    }

    /**
     * Allows either inserting a new Episode with the insert() method (id is null)
     * or updating the Episode if its identifier already exists in the "Episode" table.
     *
     * @return Episode Returns the current instance
     */
    public function save(): Episode
    {
        if ($this->id == null) {
            $this->insert();
        } else {
            $this->update();
        }
        return $this;
    }

    /**
     * Creates a new instance of Episode
     *
     * @param int $seasonId Season ID of the new Episode
     * @param string $name Name of the new Episode
     * @param string $overview Overview of the new Episode
     * @param int $episodeNumber Episode number of the new Episode
     * @param ?int $id Identifier of the new Episode
     *
     * @return Episode Returns the current object
     */
    public static function create(int $seasonId, string $name, string $overview, int $episodeNumber, ?int $id = null): Episode
    {
        $episode = new Episode();
        $episode->setSeasonId($seasonId);
        $episode->setName($name);
        $episode->setOverview($overview);
        $episode->setEpisodeNumber($episodeNumber);
        $episode->setId($id);
        return $episode;
    }

}