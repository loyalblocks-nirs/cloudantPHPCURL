<?php

require_once 'Cloudant.php';

abstract class cloudantcouchdb {
    
    protected static $instance;
    
    protected static $cdb_obj = null;
    
    /**
     * Initialize this class after construction
     * Can throw Exception or simply write to error log
     * @return Cloudant | null
     */
    abstract protected function initialize();
    
    /**
     * Return the CloudantCouchDB object held by the singleton
     * @return Cloudant
     */
    abstract public function getCloudantCouchDB();

    /**
     * return single value from view
     * @param string $doc design document name
     * @param string $view view name
     * @param array $params
     */
    public function singleViewValue( $doc , $view, $params ) {
        $cdb = $this->getCloudantCouchDB();

        $r =  $cdb->view( $doc , $view, $params );
        if( isset( $r->rows ) && isset($r->rows[0]) ) {
            return $r->rows[0]->value;
        } else {
            return 0;
        }
    }
    
    /**
     * return values from Cloudant view
     * @param string $doc design document name
     * @param string $view view name
     * @param array $params
     */
    public function view( $doc , $view, $params ) {
        $cdb = $this->getCloudantCouchDB();
        
        return  $cdb->view( $doc , $view, $params );
    }
    
}

/**
 * Use this for each DB to connect 
 */
class cloudant extends cloudantcouchdb {
    
    protected function initialize(){
        try {
            self::$cdb_obj = new Cloudant(
                    "server",
                    "database",
                    "username",
                    "password"
            );
        } catch (Exception $ex) {
            error_log("Cloudant connection Problem! ".$ex->getMessage()." ***Details: ".print_r($ex,1));
        }
    }
    
    /**
     * Return the singleton instance, constructing and
     * and initializing it if it doesn't already exist
     *
     * @return instance
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
            self::$instance->initialize();
        }
        return self::$instance;
    }
    
    public function getCloudantCouchDB() {
         return(self::$cdb_obj);
    }
}


?>
