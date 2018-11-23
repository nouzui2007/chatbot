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
        // $db->close();

        return $res;
    } catch (Exception $e) {
        return false;
    }
}

function get_data($text, $is_geo)
{
    $json = array(
        'features'  => array()
    );

    if ($is_geo) {
        $result = read_db("select *, AsGeoJSON(geom) AS geojson from place where geom is not null and category like '%" . $text . "%'");;
        $json['type'] = 'FeatureCollection';         
    } else {
        $result = read_db("select * from place where category like '%" . $text . "%'");
        $json['type'] = 'Collection';
    }
    
    $cnt = 0;
    while ($row = $result->fetchArray()) {
        if (is_geo) {
            $properties = array(
                "id" => $row["id"],
                "category" => $row["category"],
                "name" => $row["name"]
            );
    
            $feature = array(
                'type' => 'Feature',
                'geometry' => json_decode($row['geojson'], true),
                'properties' => $properties
            );
        } else {
            $feature = array(
                "id" => $row["id"],
                "category" => $row["category"],
                "name" => $row["name"]
            );         
        }
        array_push($json['features'], $feature);
        $cnt++;
    }    
    if ($cnt == 0) {
        $json['type'] = 'Word';
        array_push($json['features'], "該当データがありません");
    }

    return $json;
}
