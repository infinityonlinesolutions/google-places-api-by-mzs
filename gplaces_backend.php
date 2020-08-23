<?php
    require dirname(__FILE__).'/vendor/autoload.php';
    use Da\Google\Places\Client\SearchClient;
    use Da\Google\Places\Client\PlaceClient;

    include('dbase.php');
    $conn = new Dbase('db12996039-1', 'localhost', 'dbu12996039', 'Polelove2017!');

    $search = new SearchClient('AIzaSyDS_YS3Y1NUVn_PgXhMJwMkGDgmBcY4Z2c');
    $place = new PlaceClient('AIzaSyDS_YS3Y1NUVn_PgXhMJwMkGDgmBcY4Z2c');
    $data = array();
    $cities = explode("\n", file_get_contents(dirname(__FILE__)."/cities"));
    foreach ($cities as $city) {
        $data = array();
        $res = $search->text('poledance studios in '.$city.' Germany');
        if(property_exists($res, 'next_page_token')){
            $pageToken = $res->next_page_token;
        }else{
            $pageToken = false;                
        }
        if($res->status ==='OK'){
            $results = $res->results;
            foreach ($results as $r) {
                $p = $place->details($r->place_id);
                if($p->status === 'OK'){
                    $p = $p->result;
                    $data[] = array(
                        'place_id' => $r->place_id,
                        'name' => property_exists($p, 'name') ? $p->name : '',
                        'address' => property_exists($p, 'formatted_address') ? $p->formatted_address : '',
                        'phone_no' => property_exists($p, 'formatted_phone_number') ? $p->formatted_phone_number : '',
                        'international_phone_number' => property_exists($p, 'international_phone_number') ? $p->international_phone_number : '',
                        'rating' => property_exists($p, 'rating') ? $p->rating : '',
                        'website' => property_exists($p, 'website') ? $p->website : '',
                        'map_url' => property_exists($p, 'url') ? $p->url : '',
                        'email' => property_exists($p, 'email') ? $p->email : '',
                        'email' => property_exists($p, 'email_address') ? $p->email_address : '',
                        'types' => property_exists($p, 'types') ? json_encode($p->types) : '',
                        'opening_hours' => property_exists($p, 'opening_hours') ? json_encode($p->opening_hours->weekday_text) : '',
                        'date_added' => date('Y-m-d H;i:s')
                    );
                    $id = $conn->selectSRow(array('id'), "wordpresswp_gplaces_information", "place_id = '$r->place_id'");
                    if(count($id) > 0){
                        $conn->updateCondition("wordpresswp_gplaces_information", "id = ".$id['id'], end($data));
                    }else{
                        $conn->insert(end($data), "wordpresswp_gplaces_information");
                    }
                }else{
                    $data[] = array(
                        'place_id' => $r->place_id,
                        'name' => property_exists($r, 'name') ? $r->name : '',
                        'address' => property_exists($r, 'formatted_address') ? $r->formatted_address : '',
                        'rating' => property_exists($r, 'rating') ? $r->rating : '',
                        'types' => property_exists($r, 'types') ? json_encode($r->types) : '',
                        'date_added' => date('Y-m-d H;i:s')
    
                    );
                    $id = $conn->selectSRow(array('id'), "wordpresswp_gplaces_information", "place_id = '$r->place_id'");
                    if(count($id) > 0){
                        $conn->updateCondition("wordpresswp_gplaces_information", "id = ".$id['id'], end($data));
                    }else{
                        $conn->insert(end($data), "wordpresswp_gplaces_information");
                    }
                sleep(2);
                }
            }
            if($pageToken !== false){
              
            }
        }
        while ($pageToken !== false) {
            $res = $search->getNextPage($pageToken);
            if(property_exists($res, 'next_page_token')){
                $pageToken = $res->next_page_token;
            }else{
                $pageToken = false;                
            }
            if($res->status ==='OK'){
                $results = $res->results;
                foreach ($results as $r) {
                    $p = $place->details($r->place_id);
                    if($p->status === 'OK'){
                        $p = $p->result;
                        $data[] = array(
                            'place_id' => $r->place_id,
                            'name' => property_exists($p, 'name') ? $p->name : '',
                            'address' => property_exists($p, 'formatted_address') ? $p->formatted_address : '',
                            'phone_no' => property_exists($p, 'formatted_phone_number') ? $p->formatted_phone_number : '',
                            'international_phone_number' => property_exists($p, 'international_phone_number') ? $p->international_phone_number : '',
                            'rating' => property_exists($p, 'rating') ? $p->rating : '',
                            'website' => property_exists($p, 'website') ? $p->website : '',
                            'map_url' => property_exists($p, 'url') ? $p->url : '',
                            'email' => property_exists($p, 'email') ? $p->email : '',
                            'email' => property_exists($p, 'email_address') ? $p->email_address : '',
                            'types' => property_exists($p, 'types') ? json_encode($p->types) : '',
                            'opening_hours' => property_exists($p, 'opening_hours') ? json_encode($p->opening_hours->weekday_text) : '',
                            'date_added' => date('Y-m-d H;i:s')
                        );
                        $id = $conn->selectSRow(array('id'), "wordpresswp_gplaces_information", "place_id = '$r->place_id'");
                        if(count($id) > 0){
                            $conn->updateCondition("wordpresswp_gplaces_information", "id = ".$id['id'], end($data));
                        }else{
                            $conn->insert(end($data), "wordpresswp_gplaces_information");
                        }
                    }else{
                        $data[] = array(
                            'place_id' => $r->place_id,
                            'name' => property_exists($r, 'name') ? $r->name : '',
                            'address' => property_exists($r, 'formatted_address') ? $r->formatted_address : '',
                            'rating' => property_exists($r, 'rating') ? $r->rating : '',
                            'types' => property_exists($r, 'types') ? json_encode($r->types) : '',
                            'date_added' => date('Y-m-d H;i:s')
                        );
                        $id = $conn->selectSRow(array('id'), "wordpresswp_gplaces_information", "place_id = '$r->place_id'");
                        if(count($id) > 0){
                            $conn->updateCondition("wordpresswp_gplaces_information", "id = ".$id['id'], end($data));
                        }else{
                            $conn->insert(end($data), "wordpresswp_gplaces_information");
                        }
                    }

                    sleep(2);
                }
            }
        }
        $my_file = dirname(__FILE__).'/data/'.$city.'.json';
        $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
        fwrite($handle, json_encode($data, JSON_PRETTY_PRINT));
        fclose($handle);
    }

?>