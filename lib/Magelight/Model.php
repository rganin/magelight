<?php


namespace Magelight;

abstract class Model
{

    use \Magelight\Forgery;

	/**
	 * Table name for model
	 *
	 * @var string
	 */
	protected static $tableName = null;

	/**
	 * id field in table for model
	 *
	 * @var string
	 */
	protected static $idField = 'id';

	/**
	 * ORM object
	 *
	 * @var \Magelight\ORM
	 */
	protected $orm = null;

	/**
	 * Default values for model
	 *
	 * @var mixed
	 */
	protected static $defaultValues = array();

    /**
     * Constructor
     *
     * @param array $data
     * @param bool $forceNew
     */
    public function __construct($data = array(), $forceNew = false)
	{
		$this->setOrm();
		if (!empty($data) && is_array($data)) {
			$this->orm->create($data, $forceNew);
		}
	}

	/**
	 * Find model instance by id
	 *
	 * @param mixed $id
	 *
	 * @return \Magelight\Model
	 */
	public static function find($id)
	{
		$modelName = get_called_class();
		$orm = new ORM(static::$tableName, static::$idField, $modelName);
		return new static($orm->whereEq(static::$idField, $id)->fetchRow(true));
	}

	/**
	 * Set orm object for model
	 *
	 * @param \DBAL\ORM $orm
	 */
	protected function setOrm($orm = null)
	{
		if ($orm instanceof ORM) {
			$this->orm = $orm;
		} else {
			$modelName = get_called_class();
			$this->orm = new ORM(static::$tableName, static::$idField, $modelName);
		}
	}

	/**
	 * Model abstract factory
	 *
	 * @param array $data - data for model
	 * @param bool $forceNew - force model as new
	 *
	 * @return \Magelight\Model
	 */
	public static function forge($data = array(), $forceNew = false)
	{
        $object = parent::forge();
		return new static($data, $forceNew);
	}

	/**
	 * Get ORM instance for model class
	 *
	 * @return \DBAL\ORM
	 */
	public static function orm()
	{
		$modelName = get_called_class();
		return new ORM(static::getTableName(), static::$idField, $modelName);
	}

	/**
	 * Magic setter
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$this->orm->setValue($name, $value);
	}

	/**
	 * Magic getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->orm->getValue($name);
	}

	/**
	 * Get model`s table name
	 * If the table name is not set in the child model class method will return
	 * child class name in plural by addin 's' to the end
	 *
	 * @return string
	 */
	final protected static function getTableName()
	{
		if (!empty(static::$tableName)) {
			return static::$tableName;
		}
		return strtolower(get_called_class() . 's');
	}

	/**
	 * Delete model by id
	 *
	 * @param mixed $id
	 *
	 * @return int|null
	 */
	public static function deleteById($id)
	{
		return static::orm()->delete($id);
	}

	/**
	 * Save model data to database and mark all fields as not dirty
	 *
	 * @param bool $safeMode - call DESCRIBE TABLE before updating and omit non-intersecting fields
	 *
	 * @return int
	 */
	public function save($safeMode = false, $ignore = false, $onDuplicateKeyUpdate = false)
	{
		if ($this->orm->isNew()) {
			$this->orm->mergeData(static::$defaultValues);
		}
		return $this->orm->save($safeMode, $ignore, $onDuplicateKeyUpdate);
	}

	/**
	 * Delete model instance by internal or given id
	 *
	 * @param mixed $id
	 *
	 * @return int|null
	 */
	public function delete($id = null)
	{
		return $this->orm->delete($id);
	}

	/**
	 * Get model data as array
	 *
	 * @param array|string $fields
	 *
	 * @return array
	 */
	public function asArray($fields = array())
	{
		$fields = !is_array($fields) ? func_get_args() : $fields;
		return $this->orm->getData($fields);
	}

	/**
	 * Dummy for converting data into readable representation
	 *
	 * @param array  $data
	 * @return array
	 */
	public function asReadable($data)
	{
		return $data;
	}

	/**
	* Dummy for converting data into saveable representation
	*
	* @param array  $data
	* @return array
	*/
	public function asSaveable($data)
	{
		return $data;
	}
}