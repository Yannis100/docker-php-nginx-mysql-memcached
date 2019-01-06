<?php
    # Connect to memcache:
    global $memc;
    $memc = new Memcached();
	
	$memc->addServer('memcached', 11211);

    # Gets key / value pair into memcache ... called by mysql_query_cache()
    function getCache($key) {
        global $memc;
        return ($memc) ? $memc->get($key) : false;
    }

    # Puts key / value pair into memcache ... called by mysql_query_cache()
    function setCache($key,$object,$timeout = 60) {
        global $memc;
        return ($memc) ? $memc->set($key,$object,MEMCACHE_COMPRESSED,$timeout) : false;
    }

    # Caching version of mysql_query()
    function mysql_query_cache($sql,$linkIdentifier = false,$timeout = 60) {
        if (($cache = getCache(md5("mysql_query" . $sql))) !== false) {
            $cache = false;
            $r = ($linkIdentifier !== false) ? mysql_query($sql,$linkIdentifier) : mysql_query($sql);
            if (is_resource($r) && (($rows = mysql_num_rows($r)) !== 0)) {
                for ($i=0;$i<$rows;$i++) {
                    $fields = mysql_num_fields($r);
                    $row = mysql_fetch_array($r);
                    for ($j=0;$j<$fields;$j++) {
                        if ($i === 0) {
                            $columns[$j] = mysql_field_name($r,$j);
                        }
                        $cache[$i][$columns[$j]] = $row[$j];
                    }
                }
                if (!setCache(md5("mysql_query" . $sql),$cache,$timeout)) {
                    # If we get here, there isn't a memcache daemon running or responding
                }
            }
        }
        return $cache;
    }
?>




<?php
    $sql = "
        SELECT `dataID`, `dataTitle`
        FROM `tbldata`
        WHERE `dataTypeID` BETWEEN 2 AND 2093
        AND `dataStatusID` IN (1,2,3,4)
        AND `dataTitle` LIKE '%something%'
        ORDER BY `dataDate` DESC
        LIMIT 10
    ";

    # Before: [without memcache]
    $rSlowQuery = mysql_query($sql);
    # $rSlowQuery is a MySQL resource
    $rows = mysql_num_rows($rSlowQuery);
    for ($i=0;$i<$rows;$i++) {
        $dataID = intval(mysql_result($rSlowQuery,$i,"dataID"));
        $dataTitle = mysql_result($rSlowQuery,$i,"dataTitle");

        echo "<a href=\"/somewhere/$dataID\">$dataTitle</a><br />\n";
    }


    # After: [with memcache]
    $rSlowQuery = mysql_query_cache($sql);
    # $rSlowQuery is an array
    $rows = count($rSlowQuery);
    for ($i=0;$i<$rows;$i++) {
        $dataID = intval($rSlowQuery[$i]["dataID"]);
        $dataTitle = $rSlowQuery[$i]["dataTitle"];

        echo "<a href=\"/somewhere/$dataID\">$dataTitle</a><br />\n";
    }
	print_r($rSlowQuery);

?>