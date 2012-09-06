<?php

/**
 * Copyright (C) 2012  Nicolas Oelgart
 * 
 * @author Nicolas Oelgart
 * @license GPL 3 http://www.gnu.org/copyleft/gpl.html
 *
 * Handles HTTP requests using cURL.
 *
**/

namespace PutIO\Engines\HTTP;

use PutIO\ClassEngine;
use PutIO\Interfaces\HTTP\HTTPEngine;
use PutIO\Helpers\HTTP\HTTPHelper;


class CurlEngine extends HTTPHelper implements HTTPEngine
{
    
    /**
     * Makes an HTTP request to put.io's API and returns the response.
     *
     * @param string $method    HTTP request method. Only POST and GET are supported.
     * @param string $url       Remote path to API module.
     * @param array  $params    OPTIONAL - Variables to be sent.
     * @param string $outFile   OPTIONAL - If $outFile is set, the response will be written to this file instead of StdOut.
     * @param array  $arrayKey  OPTIONAL - Will return all data on a specific array key of the response.
     * @return mixed
     * @throws PutIOLocalStorageException
     *
    **/
    public function request($method, $url, array $params = array(), $outFile = '', $returnBool = false, $arrayKey = '')
    {
        $options = array();
        
        if ($method === 'POST')
        {
            $options[CURLOPT_POSTFIELDS] = $params;
        }
        else
        {
            $url .= '?' . http_build_query($params, '', '&');
        }
        
        if ($outFile === '')
        {
            $options[CURLOPT_RETURNTRANSFER] = true;
        }
        else
        {
            if (($fp = @fopen($outFile, 'w+')) === false)
            {
                throw new LocalStorageException('Unable to create local file');
            }

            $options[CURLOPT_FILE] = $fp;
        }
        
        $options[CURLOPT_URL]            = $url;
        $options[CURLOPT_USERAGENT]      = 'nicoswd-putio/2.0';
        $options[CURLOPT_CONNECTTIMEOUT] = 10;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_FOLLOWLOCATION] = true;
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        
        if ((int) curl_getinfo($ch, CURLINFO_HTTP_CODE) === 404)
        {
            return false;
        }
        
        return $this->getResponse($response, $returnBool, $arrayKey);
    }
}

?>