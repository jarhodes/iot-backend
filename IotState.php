<?php

/**
 * Class IotState
 * Handles interactions with the database
 * @author Jonathan Rhodes
 */
class IotState
{

    /**
     * @var mysqli $d Database connection
     */
    protected $d;

    /**
     * @var int $id ID of the database row
     */
    protected $id;

    /**
     * @var string $state The IoT device state
     */
    protected $state;

    /**
     * @var DateTime $stamp Timestamp
     */
    protected $stamp;

    /**
     * @var string[] $statesAllowed The permitted values
     */
    protected $statesAllowed = ["up", "down", "stopped"];

    /**
     * IotState constructor.
     * @param mysqli $d
     */
    public function __construct(mysqli $d) {
        $this->d = $d;
    }

    /**
     * Fetches the most recent state
     * @return $this
     * @throws Exception if one is thrown by the DateTime constructor or $this->setState()
     */
    public function fetchLatest() {
        $q = $this->d->prepare("SELECT `id`, `state`, `stamp` FROM `IotState` ORDER BY `stamp` DESC LIMIT 1");
        $q->execute();
        $q->store_result();
        if ($q->num_rows == 0) {
            $this->setState("stopped");
            $this->setStamp(new DateTime());
        }
        else {
            $q->bind_result($id, $state, $stamp);
            $q->fetch();
            $this->setId($id);
            try {
                $this->setState($state);
                $this->setStamp(new DateTime($stamp));
            }
            catch (Exception $e) {
                throw $e;
            }
        }
        $q->close();
        return $this;
    }

    /**
     * Fetches a specific row from the database
     * @return $this
     * @throws Exception if $this->id is not set or if an exception is thrown by the DateTime constructor or $this->setState()
     */
    public function fetchById() {
        if (empty($this->id)) {
            throw new Exception("FetchById called but no ID set");
        }
        $q = $this->d->preapre("SELECT `state`, `stamp` FROM `IotState` WHERE `id` = ? LIMIT 1");
        $q->bind_param("i", $this->id);
        $q->execute();
        $q->store_result();
        if ($q->num_rows == 0) {
            $q->close();
            throw new Exception("FetchById: no such row");
        }
        else {
            $q->bind_result($state, $stamp);
            $q->fetch();
            try {
                $this->setState($state);
                $this->setStamp(new DateTime($stamp));
            }
            catch (Exception $e) {
                throw $e;
            }
        }
        $q->close();
        return $this;
    }

    /**
     * Saves the state
     * @return $this
     * @throws Exception if one is thrown by $this->insert() or $this->update()
     */
    public function save() {
        if (empty($this->id)) {
            return $this->insert();
        }
        else {
            return $this->update();
        }
    }

    /**
     * Inserts a new row into the database
     * @return $this
     * @throws Exception if $this->id is not empty, because in that case, we should be updating not inserting
     */
    protected function insert() {
        if (!empty($this->id)) {
            throw new Exception("insert() called but ID set");
        }
        $q = $this->d->prepare("INSERT INTO `IotState` (`state`) VALUES ?");
        $q->bind_param("s", $this->state);
        $q->execute();
        $this->id = $q->insert_id;
        $q->close();
        return $this->fetchById();
    }

    /**
     * Updates a row in the database
     * @return $this
     * @throws Exception if $this->id is empty, because in that case, we should be inserting not updating
     */
    protected function update() {
        if (empty($this->id)) {
            throw new Exception("update() called but no ID set");
        }
        $q = $this->d->prepare("UPDATE `IotState` SET `state` = ? WHERE `id` = ? LIMIT 1");
        $q->bind_param("s", $this->state);
        $q->execute();
        $q->close();
        return $this->fetchById();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return IotState
     */
    public function setId(int $id): IotState
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return IotState
     * @throws Exception if $state is not in the whitelisted commands in $this->statesAllowed
     */
    public function setState(string $state): IotState
    {
        if (in_array($state, $this->statesAllowed)) {
            $this->state = $state;
            return $this;
        }
        else {
            throw new Exception("Attempt to set a disallowed state");
        }
    }

    /**
     * @return DateTime
     */
    public function getStamp(): DateTime
    {
        return $this->stamp;
    }

    /**
     * @param DateTime $stamp
     * @return IotState
     */
    public function setStamp(DateTime $stamp): IotState
    {
        $this->stamp = $stamp;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStatesAllowed(): array
    {
        return $this->statesAllowed;
    }

}