<?php

/**
 * PHP data caching plugin. Provides methods that enable faster delivery of json content pulled from an external API.
 * 
 * @version 1.0
 */

class DataCacher 
{

    /**
    * Check the cache for a specific file.
    *
    * @param   string  $fileName -  Local json filename of cached data
    * @param   int     $maxTime  -  Maximum amount of time that can pass before the file cache is refreshed
    * @param   int     $maxUsers -  Maximum amount of users that can view the file before cache is refreshed
    *
    * @return  bool    True/False indicates if cache should be refreshed for this given file
    */

    private function check_cache($fileName, $maxTime, $maxUsers) {

        if(file_exists($fileName)) {
            
            // Check if we have exceeded the max allowed time the cached file can sit without being refreshed.

            $lastTime = filemtime(__DIR__ . '/' . $fileName);
            $now = time();
            $timeHasExpired = $lastTime + $maxTime < $now;

            // If the time has expired then reset the user counter and set our refreshCache flag to true.

            if($timeHasExpired){
                $userCounter = $maxUsers;
                $refreshCache = true;
            } 

            // Else time hasn't expired but we want to check how many users have been hitting the server.
            // Debouncing the users ensures we only indicate cache needs to be refreshed after a certain amount of users have hit the server in the max allowed time period.
            
            else {

                $userCounter = file_get_contents(__DIR__ . '/user_counter.json');

                // If user counter is 0 then reset counter and indicate that we need to refresh the cache.

                if($userCounter == 0) {
                    $userCounter = $maxUsers;
                    $refreshCache = true;
                } 

                // Else not enough users have hit the server then debounce users.

                else {
                    $userCounter --;
                    $refreshCache = false;
                }

            }

            // Save updated user count

            $fp = fopen(__DIR__ . '/user_counter.json', 'w');
            fwrite($fp, $userCounter);
            fclose($fp);
        }

        // Else file doesn't exist so we have to refresh it.

        else {
            $refreshCache = true;
        }

        // Return whether we should or should not refresh cache.

        return $refreshCache;

    }

    /**
     * Save JSON data. Updates a local json file with new data after we have queried it via a REST API call.
     *
     * @param   string  $fileName    - Local json filename of cached data
     * @param   string  $queryString - REST http/https query string to make the API call. Should already include query parameters on URL. REST endpoint must return JSON.
     * @param   int     $maxTime  -  Maximum amount of time(seconds) that can pass before the file cache is refreshed
     * @param   int     $maxUsers -  Maximum amount of users that can view the file before cache is refreshed
     * 
     * @return  string  $jsonData    - Returns json from our query in case the user wants to use the data immediately.
     * 
     */

    public function save_json($fileName, $queryString, $maxTime, $maxUsers) {

        // Check if we need to refresh the cached data

        $refreshCache = $this->check_cache($fileName, $maxTime, $maxUsers);

        if($refreshCache) {

            $queriedData = file_get_contents($queryString);

            $fp = fopen($fileName, 'w');

            fwrite($fp, $queriedData);

            fclose($fp);

        }

        // Return the cached data

        $jsonData = file_get_contents(__DIR__ . '/' . $fileName);

        return $jsonData;
        
    }
    
}

?>