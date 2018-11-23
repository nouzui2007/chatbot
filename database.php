<?php

/**
 * read from SQLite
 */
function read_db($sql)
{
    $db = new SQLite3('testdb.sqlite3');
    $db->loadExtension('libspatialite.so.5');
    $db->exec("SELECT InitSpatialMetadata()");

    try {
        $res = $db->query($sql);        
        $db->close();

        return $res;
    } catch (Exception $e) {
        write_log($e->getMessage());
        return false;
    }
}
