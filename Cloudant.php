<?php

/**
 * class Cloudant
 * @see http://cloudant.com/for-developers/crud/
 * 
 * @author nirshneor@gmail.com
 */

class Cloudant {  
    
    function __construct($server,$db,$username,$password){
      $this->server = "https://".$username.":".$password.'@'.$username.$server;
      $this->db = $db;
      $this->header_short = "Content-Type: application/json";
      $this->header = $this->header_short."\r\nContent-Length: ";
    }
 
    function call($path, $http = array(), $db_level = true){

      if ($path) {
          $path = "/".$path;
      }
      if ($db_level && $this->db) {
          return json_decode(file_get_contents($this->server . $this->db . $path, NULL, stream_context_create(array('http' => $http))));
      } else {
          return json_decode(file_get_contents($this->server . $path, NULL, stream_context_create(array('http' => $http))));
      }
    }

  //curl -X PUT {USERNAME}:{PASSWORD}@{USERNAME}.cloudant.com/{DB}{/ID} -d ....
  function upsert_doc( $id, $data ){ // to update - include _rev in document body
     return $this->call($id, array(
        'method' => 'POST', 
        'header' => $this->header . strlen($data) . "\r\n",
        'content' => $data));
  }
  
  //curl -X GET {USERNAME}:{PASSWORD}@{USERNAME}.cloudant.com/{DB}/{ID} 
  function get( $id ){
     return $this->call($id, array(
        'method' => 'GET', 
        'header' => $this->header_short
     ));
  }

  //curl -X DELETE {USERNAME}:{PASSWORD}@{USERNAME}.cloudant.com/{DB}/{ID}?rev={rev}
  function delete_doc( $id, $rev ){
     $path = $id."?rev=".$rev;
     return $this->call($path, array(
        'method' => 'DELETE',
     ));
  }
  
  //curl -X GET {USERNAME}:{PASSWORD}@{USERNAME}.cloudant.com/{DB}/_design/{DESIGN_DOC}/_{TYPE}/{INDEX}?{params} 
  function view( $doc , $index, $params, $type = 'view' ){
    // inner arrays to json before transforming to get params
    foreach($params as $key => $param) {
        if (is_array($param)) {
            $params[$key] = json_encode($param);
        }
    }
    // transforming to get params
    $params = "?".http_build_query($params);
    // creating the api call + params
    $path = "_design/".$doc."/_".$type."/".$index.$params;
    return $this->call($path, array(
       'method' => 'GET', 
       'header' => $this->header_short,
    ));
  }
  
}

?>