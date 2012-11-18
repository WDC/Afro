<?php 

    include "Afro.php";

    get('/', function($Afro) {
        echo "HELLO";
    });

    get('/countries(.*?)', function($Afro) {
        $testData = array();
        $Afro->format('json', function() use (&$testData) {
            $testData = array(
                "mx" => array(
                    'iso' => 'MX',
                    'fullName' => 'Mexico'
                ),
                "jm" => array(
                    'iso' => 'JM',
                    'fullName' => 'Jamaica'
                )
            );
        });

        $lookingFor = strtolower(basename($Afro->params[1], '.json'));
        if(isset($testData[$lookingFor])) {
            // return json_encode($testData[$lookingFor]);
            echo json_encode($testData[$lookingFor]);
        }else{
            echo json_encode($testData);
        }

        $Afro->format('csv', function() {
            return "iso,fullName\nMX,Mexico\n,JM,Jamaica";
        });

        if(!$Afro->format) echo "Countries are only available as a JSON format.";
    });

    get('/hello/(.*?)', function($Afro) {
        $Afro->format('json', function($Afro) {
            echo json_encode(array('name', $Afro->param(2)));
        });

        if(!$Afro->format)
            echo 'Hello '. $Afro->param(2) . ', it\'s a good day today!';
    });

?>