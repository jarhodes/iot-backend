<?php


class IotCommand
{

    /**
     * @var mysqli database connection
     */
    protected $d;

    /**
     * @var int id
     */
    protected $id;

    /**
     * @var int number of degrees of rotation
     */
    protected $degrees;

    /**
     * @var DateTime timestamp
     */
    protected $stamp;

    /**
     * IotCommand constructor.
     * @param mysqli $d
     */
    public function __construct(mysqli $d, $id = 0) {
        $this->d = $d;
        if (!empty($id)) {
            $this->id = (int) $id;
            $this->fetchById();
        }
        else {
            $this->fetchLatest();
        }
    }

    /**
     * Gets the most recent entry in the database
     * @return $this
     * @throws Exception
     */
    public function fetchLatest() {
        $q = $this->d->prepare("SELECT `id`, `degrees`, `stamp` FROM `IotCommand` ORDER BY `stamp` DESC LIMIT 1");
        $q->execute();
        $q->bind_result($this->id, $this->degrees, $stamp);
        $q->fetch();
        $this->stamp = new DateTime($stamp);
        $q->close();
        return $this;
    }

    /**
     * Saves the current number of degrees
     * @return $this
     */
    public function save() {
        $q = $this->d->prepare("INSERT INTO `IotCommand` (`degrees`) VALUES (?)");
        $q->bind_param("i", $this->degrees);
        $q->execute();
        $this->id = $q->insert_id;
        $q->close();
        $this->fetchById();
        return $this;
    }

    /**
     * Fetches a record by ID
     * @param int $id
     * @return $this
     * @throws Exception
     */
    public function fetchById($id = 0) {
        if (!empty($id)) {
            $this->id = (int) $id;
        }
        if (empty($this->id)) {
            throw new Exception("fetchById: no ID set");
        }
        $q = $this->d->prepare("SELECT `degrees`, `stamp` FROM `IotCommand` WHERE `id` = ? LIMIT 1");
        $q->bind_param("i", $this->id);
        $q->execute();
        $q->bind_result($this->degrees, $stamp);
        $q->fetch();
        $this->stamp = new DateTime($stamp);
        $q->close();
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDegrees()
    {
        return $this->degrees;
    }

    /**
     * @param int $degrees
     */
    public function setDegrees(int $degrees)
    {
        $this->degrees = $degrees;
    }

    /**
     * @return DateTime
     */
    public function getStamp()
    {
        return $this->stamp;
    }

}