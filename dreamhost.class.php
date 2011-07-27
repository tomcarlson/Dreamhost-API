<?php

/**
 * Class for interacting with Dreamhost API
 * 
 * Dreamhost is a hosting company in Los Angelas.  They released a small API
 * for users of their private servers to interact with their accounts.
 * This API is described at http://wiki.dreamhost.com/Api
 * 
 * This class provides an easy way to quickly add Dreamhost API functionality
 * to your existing dashboard or management console.
 *
 * @version 0.1  05/31/2010
 * @author Tom Carlson (http://elance.com/s/tomcarlson/resume)
 * @link http://tomcarlson.com
 * 
 */
 
class Dreamhost {	
	/* available for status and debug */
  public $http_code;  /* Contains the last HTTP status code returned. */  
  public $http_info;  /* Contains the last HTTP headers returned.     */  
  public $url;        /* Contains the last API call.                  */  
   
  /* May be set externally with setter functions */ 
  var $key;              /* Dreamhost API Key */  
  var $format = 'xml';   /* Respons format. */     
  var $account;          /* The account number to perform operations under. */ 
    
  var $api_url = 'https://api.dreamhost.com/'; /* Dreamhost API base url */ 
  var $timeout = 30;                           /* Set timeout default.   */  
  var $connecttimeout = 30;                    /* Set connect timeout.   */ 
  var $ssl_verifypeer = FALSE;                 /* Verify SSL Cert.       */  
  var $useragent = 'DreamhostAPI_THC v0.1';    /* Set the useragent.     */


  function __construct($dreamhost_api_key) {
    $this->key = $dreamhost_api_key;
  }

  public function format($newformat) {
    $this->format = $newformat;
  }
  
  public function account($newaccount) {
    $this->account = $newaccount;
  }

  // Make an HTTP request and return API results
  function http($url, $method, $parameters = NULL) {
    $this->http_info = array();
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    curl_setopt($ci, CURLOPT_HEADER, FALSE);

    switch ($method) {
      case 'GET':
        $pcount = 0;        
        foreach ($parameters as $key=>$value)
        {
        	if (!$pcount)
        	  $url .= '?'.$key.'='.urlencode($value);
        	else
        	  $url .= '&'.$key.'='.urlencode($value);
        	$pcount++;
        }
        break;    	
      case 'POST':
        $postfields = $parameters;
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        $postfields = $parameters;
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }

    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
    $this->url = $url;
    curl_close ($ci);
    return $response;
  }


  // Get header info to store.  Used by CURLOPT_HEADERFUNCTION
  function getHeader($ch, $header) {
    $i = strpos($header, ':');
    if (!empty($i)) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->http_header[$key] = $value;
    }
    return strlen($header);
  }
  
  
  public function api($command,$extraparams = NULL)
  {
  	$baseparams = array('key'=>$this->key,'unique_id'=>uniqid(),'cmd'=>trim($command),'format'=>$this->format);
  	if ($this->account) $baseparams = array_merge($baseparams,array('account'=>$this->account));
  	  
  	if (is_array($extraparams))
  	  $parameters = array_merge($baseparams,$extraparams);
  	else
  	  $parameters = $baseparams;
  	return $this->http($this->api_url,'GET',$parameters);
  }
}


// this is not part of the class, it's just a quick and dirty way to display 
// Dreamhost API data, so it's included here for convenience.
// Takes an XML object and generates an HTML table
// ... assuming xml object is structured like Dreamhost API
function XMLtoTable($xml)
{ 
	if ($xml->result!='success')
    echo '<b>'. ucwords($xml->result).':</b> '.$xml->data;
  else
  {  	  
	  if (count($xml->data)>0)
	  {
		  echo '<table border="1"><tr>';
	    foreach($xml->data[0] as $key=>$value) 
	      echo '<th>'.$key.'</th>';
	    echo '</tr>';
	    
      foreach($xml->data as $data)
      {
    	  echo '<tr>';
        foreach($data as $key=>$value)
          echo '<td>'.$value.'</td>';
        echo '</tr>';
      }
      echo '</table>';
    }
  }
}

?>