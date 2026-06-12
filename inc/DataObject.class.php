<?php

namespace ImmanentChecker;

/**
  * a DataObject is a generic object
  * to store data under given keys.
  * 
  * it allows getting, adding, updating and deleting
  * data.
  * 
  * a validation callback can be provided
  * to ensure, that the stored data
  * validate correctly.
  *
  **/
class DataObject {

  /**
    * list of stored data 
    **/
  private $arrData = array();
  
  /**
    * callback to validate all stored data
    **/
  private $cloValidator = null;
  
  /**
    * @param $cloValidator - optional validator callback to validate stored data
    * @param $arrData      - optional list of data to store in object
    *
    * initiates the data-object and stores the validator callback. if no
    * callback is given, a default callback is stored, which validates against
    * everything.
    * 
    * if optional data are given, they are passed to DataObject::add(), to be stored
    * immediately
    *
    **/
  public function __construct(?callable $cloValidator = null, array $arrData = array()) {
    
    $this->cloValidator = (!is_null($cloValidator)) ? $cloValidator : function() {return true; };
    
    if(!empty($arrData))
      $this->add($arrData);
  
  }
  
  /**
    * @param $arrData - list of data to store in object
    * 
    * @throws \Exception - if a key is already in use
    * @throws \Exception - if the final data-set do not validate
    *
    * add the list of data to the existing ones. the given data
    * must have the following format:
    * array([key] => data
    *       [key] => data, [..])
    *
    * if a key in the given data is already used in stored
    * data the addition is aborted and an exception is
    * thrown.
    *
    * the stored data are only modified on success
    *
    **/
  public function add(array $arrData): void {
  
    $arrMergedData = array_merge($this->arrData, $arrData);
    
    foreach($arrData AS $strKey => $mixData)
      if(array_key_exists($strKey, $this->arrData))
        throw new \Exception("could not store data, key is already in use: $strKey");
    
    $cloValidator = $this->cloValidator;
    
    if(!$cloValidator($arrMergedData))
      throw new \Exception("given data do not validate");
    
    $this->arrData = $arrMergedData;
  
  }
  
  /**
    * @param $arrData - list of data to update
    * 
    * @throws \Exception - if the final data-set do not validate
    * 
    * update the stored data with the given data-set. for
    * the structure of $arrData have a look at DataObject:add()
    *
    * existing keys are overwritten with new values. new
    * keys are added to the data-set.
    * 
    * if validation of final set fails, an exception is
    * thrown. no data are modified on failure.
    *
    **/
  public function update(array $arrData): void {
  
    $arrMergedData = array_merge($this->arrData, $arrData);
    
    $cloValidator = $this->cloValidator;
    
    if(!$cloValidator($arrMergedData))
      throw new \Exception("given data do not validate");
    
    $this->arrData = $arrMergedData;
  
  }
  
  /**
    * @param $strKey - the key of the value to delete
    * 
    * delete the value stored under the given key.
    * if given key does not exist, nothing is done!
    *
    **/
  public function delete(string $strKey): void {
    
    if($this->exists($strKey))
      unset($this->arrData[$strKey]);
  
  }
  
  /**
    * @param $strKey - the key to check
    *
    * @returns (boolean) true, if key exists
    * @returns (boolean) false, if key does not exist
    *
    * check if the given key exists
    *
    **/
  public function exists(string $strKey): bool {
    
    if(!array_key_exists($strKey, $this->arrData))
      return false;
    
    return true;
  
  }
  
  /**
    * @param $strKey - the key to get the value from
    * 
    * @throws \Exception - if given key is unknown
    *
    * @returns (mixed) the value stored under the given key
    *
    * return the value stored under the given key
    *
    **/
  public function get(string $strKey) {
    
    if(!$this->exists($strKey))
      throw new \Exception("unknown key: $strKey");
    
    return $this->arrData[$strKey];
  
  }
  
  /**
    * return all stored data in the structure of:
    * array([key] => data
    *       [key] => data, [..])
    *
    **/
  public function getAll(): array {
    
    return $this->arrData;
    
  }
  
}
