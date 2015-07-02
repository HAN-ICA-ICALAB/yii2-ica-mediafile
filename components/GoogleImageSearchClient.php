<?php

namespace icalab\mediafile;

/**
 * This component wraps the Google Image Search API. It takes a search query 
 * as a parameter and returns an array of image data.
 *
 * IMPORTANT: The Google Image Search API is DEPRECATED. It will be terminated 
 * in 2014.
 */
class GoogleImageSearchClient
{

    protected $url = 'http://ajax.googleapis.com/ajax/services/search/images';

    protected $query = null;
    protected $size = null; // small, medium or large

    // Very sneaky. If you do a lot of requests, Google notices and blocks 
    // you (you're not actually allowed to scrape Google like this).
    // What helps, however, is if you supply the IP of a user on whose 
    // behalf you're doing this. A 192.168. address is fine. It 'd probably be 
    // very obvious if we'd change the IP address on every request. So what we 
    // do is set it in a static variable. This way 
    protected static $userIp = null;
    
    public function __construct($query = null)
    {
        if(static::$userIp === null)
        {
           $hour = date('H');
           $lastOctet = round(($hour * $hour) / 3) + rand(0, 3);
           if($lastOctet > 200)
           {
               $lastOctet = 190 + rand(0, 2);
           }
           static::$userIp = '192.168.1.' . $lastOctet;
        }

        if($query)
        {
            $this->setQuery($query);
            return $this->search();
        }
    }

    /**
     * The function that performs the search.
     */
    public function search()
    {
        $query = $this->getQuery();
        if($query === null)
        {
            throw new CException("Attempt to execute search without a query.");
        }

        $params = array('q' => $query );
        $params['v'] = '1.0';
        $params['userip'] = static::$userIp;

        $size = $this->getSize();
        if($size)
        {
            $params['imgsz'] = $size;
        }


        $paramElements = array();
        foreach($params as $key => $value)
        {
            $paramElements[] = urlencode($key) . '=' . urlencode($value);
        }

        $requestUrl = $this->url . '?' . implode('&', $paramElements);

        $rawResponse = file_get_contents($requestUrl);

        $data = null;
        try {
            $data = json_decode($rawResponse);
        }
        catch(Exception $e)
        {
            throw new CException("Unable to parse result as JSON.");
        }

        if(! property_exists($data, 'responseData'))
        {
            throw new CException("JSON data has unexpected structure");
        }

        if(! is_object($data->responseData))
        {
            return array();
        }


        if(! property_exists($data->responseData, 'results'))
        {
            return array();
        }

        $results = array();
        foreach($data->responseData->results as $result)
        {
            $results[] = $result->unescapedUrl;
        }

        return $results;

    }

    /**
     * This function performs a search as well but it tries to find the 
     * largest possible images.
     */
    public function searchLargestPossible()
    {
        $query = $this->getQuery();
        if($query === null)
        {
            throw new CException(
                "Attempt to execute largest possible search without a query.");
        }

        foreach(explode(' ', 'xxlarge large medium small') as $size)
        {
            $this->setSize($size);
            $result = $this->search();
            if(count($result))
            {
                return $result;
            }
        }
        return array();

    }


    

    /**
     * Getters and setters.
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setSize($size)
    {
        $cleanSize = trim(strtolower($size));
        if($cleanSize != 'small' 
            && $cleanSize != 'medium'
            && $cleanSize != 'large'
            && $cleanSize != 'xxlarge'
        )
        {
            throw new CException(
                "Invalid size '$size' specified. Allowed sizes are " 
                . "'small', 'medium', 'large' and 'xxlarge'."); 
        }
        $this->size = $cleanSize;
    }

    public function getSize()
    {
        return $this->size;
    }

}
