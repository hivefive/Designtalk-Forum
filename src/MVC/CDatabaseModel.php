<?php
namespace Anax\MVC;
/**
 * Model for Users.
 *
 */
class CDatabaseModel implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
 

 /**
 * Get the table name.
 *
 * @return string with the table name.
 */
public function getSource()
{
    return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
}

/**
 * Find and return all.
 *
 * @return array
 */
public function findAll()
{
    $this->db->select()
             ->from($this->getSource());
 
    $this->db->execute();
    $this->db->setFetchModeClass(__CLASS__);
    return $this->db->fetchAll();
}

/**
 * Deletes all
 *
 */
public function deleteAll()
{
    $this->db->delete(
        $this->getSource());
    $this->db->execute();
    
}


/**
 * Get object properties.
 *
 * @return array with object properties.
 */
public function getProperties()
{
    $properties = get_object_vars($this);
    unset($properties['di']);
    unset($properties['db']);
 
    return $properties;
}

/**
 * Save current object/row.
 *
 * @param array $values key/values to save or empty to use object properties.
 *
 * @return boolean true or false if saving went okey.
 */
public function save($values = [])
{
    $this->setProperties($values);
    $values = $this->getProperties();
 
    if (isset($values['id'])) {
        return $this->update($values);
    } else {
        return $this->create($values);
    }
}

/**
 * Update row.
 *
 * @param array $values key/values to save.
 *
 * @return boolean true or false if saving went okey.
 */
public function update($values)
{
    $keys   = array_keys($values);
    $values = array_values($values);
 
    // Its update, remove id and use as where-clause
    unset($keys['id']);
    $values[] = $this->id;
 
    $this->db->update(
        $this->getSource(),
        $keys,
        "id = ?"
    );
 
    return $this->db->execute($values);
}

/**
 * Create new row.
 *
 * @param array $values key/values to save.
 *
 * @return boolean true or false if saving went okey.
 */
public function create($values)
{
    $keys   = array_keys($values);
    $values = array_values($values);
 
    $this->db->insert(
        $this->getSource(),
        $keys
    );
 
    $res = $this->db->execute($values);
 
    $this->id = $this->db->lastInsertId();
 
    return $res;
}

/**
 * Find and return specific.
 *
 * @return this
 */
public function find($id)
{
    $this->db->select()
             ->from($this->getSource())
             ->where("id = ?");
 
    $this->db->execute([$id]);
    return $this->db->fetchInto($this);
}

/**
 * Set object properties.
 *
 * @param array $properties with properties to set.
 *
 * @return void
 */
public function setProperties($properties)
{
    // Update object with incoming values, if any
    if (!empty($properties)) {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }
    }
}



/**
 * Execute the query built.
 *
 * @param string $query custom query.
 *
 * @return $this
 */
public function execute($params = [])
{
    $this->db->execute($this->db->getSQL(), $params);
    $this->db->setFetchModeClass(__CLASS__);
 
    return $this->db->fetchAll();
}

/**
 * Build the where part.
 *
 * @param string $condition for building the where part of the query.
 *
 * @return $this
 */
public function andWhere($condition)
{
    $this->db->andWhere($condition);
 
    return $this;
}

/**
 * Build the where part.
 *
 * @param string $condition for building the where part of the query.
 *
 * @return $this
 */
public function where($condition)
{
    $this->db->where($condition);
 
    return $this;
}

/**
 * Build a select-query.
 *
 * @param string $columns which columns to select.
 *
 * @return $this
 */
public function query($columns = '*')
{
    $this->db->select($columns)
             ->from($this->getSource());
 
    return $this;
}

/**
 * Delete row.
 *
 * @param integer $id to delete.
 *
 * @return boolean true or false if deleting went okey.
 */
public function delete($id)
{
    $this->db->delete(
        $this->getSource(),
        'id = ?'
    );
 
    return $this->db->execute([$id]);
}

public function orderBy($condition)
    {
        $this->db->orderBy($condition);
     
        return $this;
    }

public function groupBy($column)
    {
        $this->db->groupBy($column);
     
        return $this;
    }

/**
 * Build the LIMIT by part.
 *
 * @param string $condition for building the LIMIT part of the query.
 *
 * @return $this
 */
public function limit($condition)
    {
        $this->db->limit($condition);

        return $this;
}


public function findAcronym($acronym)
	{
		$this->db->select()
			->from($this->getSource())
			->where("acronym = ?");
		$this->db->execute([$acronym]);
		
		return $this->db->fetchInto($this);
	}

public function login($acronym)
	{
		$this->db->select()
			->from($this->getSource())
			->where("acronym = ?");
			//->andWhere('password = ?');
			$this->db->execute([$acronym]);
			$res = $this->db->fetchInto($this);
		return $res;

	}
	
public function count_loggedin() 
	{
		$this->db->select()
			->orderBy("timesLoggedOn")
			->from($this->getSource())
			->limit(3);
			$this->db->execute();
			$res = $this->db->fetchInto($this);
		return $res;
	}
	
public function getUserQuestions($user){
		$this->db->select()
		->from('question')
		->where('user = ?')
		->execute([$id]);
		$res = $this->db->fetchInto($this);
		return $res;
	}
	
public function getUserAnswers($user){
		$this->db->select()
		->from('answer')
		->where('user = ?')
		->execute([$user]);
		$res = $this->db->fetchInto($this);
		return $res;
	}
	
public function getUserComments($user) {
		$this->db->select()
		->from('comments')
		->where('user = ?')
		->execute([$user]);
		$res = $this->db->fetchInto($this);
		return $res;
	}
	
}