<?php

class Model {

    public static $_db;

    // fonctions DTO 

    public function hydrate ($data) {

        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key); //ucfirst() -> met en majuscule la première lettre pour correspondre au nom du setter
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getAttributes () {

        $attributes = get_object_vars($this);
        $filteredAttributes = array_filter(
            $attributes,
            fn ($attributeKey) => !str_starts_with($attributeKey, '_'),
            ARRAY_FILTER_USE_KEY
        );

        return $filteredAttributes;
    }

    // fonctions DAO

    public function add ($query, $data) {

        return static::$_db->execQuery(
            $query,
            $data
        );
    }

    public function update ($query, $data) {

        return static::$_db->execQuery(
            $query,
            $data
        );
    }

    public function delete ($id) {

        static::$_db->execQuery(
            'DELETE FROM '. static::class .' WHERE id = :id',
            array( 'id' => $id )
        );
    }

    // fonctions statiques
    
    public static function getAll ($orderBy = NULL) {

        $className = static::class;
    
        $q = static::$_db->execQuery(
            'SELECT * FROM '. $className . ($orderBy ? ' ORDER BY :orderBy' : ''),
            array( 'orderBy' => $orderBy )
        );
    
        $results = $q->fetchAll();
    
        return array_map(
            fn($result) => new $className($result),
            $results
        );
    }
    
    public static function getById ($id) {

        $className = static::class;
    
        $q = static::$_db->execQuery(
            'SELECT * FROM '. $className .' WHERE id = :id',
            array( 'id' => $id )
        );

        $result = $q->fetch();

        if ($result) return new $className($result);
        else return false;
    } 


}