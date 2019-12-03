<?php


namespace TaskForce\classes\models;

use TaskForce\exceptions\ModelException;

/**
 * Class Model
 * @package TaskForce\classes\models
 */
abstract class Model
{
    protected $id = null;

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . ucfirst($name))) {
            throw new ModelException('Getting write-only property: ' . get_class($this) . '::'. $name);
        } else {
            throw new ModelException('Getting unknown property: ' . get_class($this) . '::'. $name);
        }
    }

    /**
     * @param string $name
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value);

        } elseif (method_exists($this, 'get' . ucfirst($name))) {
            throw new ModelException('Setting read-only property: ' . get_class($this) . '::'. $name);
        } else {
            throw new ModelException('Setting unknown property: ' . get_class($this) . '::'. $name);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
