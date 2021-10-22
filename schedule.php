<?php

//$days = [
//    0 => 'Sunday',
//    1 => 'Monday',
//    2 => 'Tuesday',
//    3 => 'Wednesday',
//    4 => 'Thursday',
//    5 => 'Friday',
//    6 => 'Saturday'
//];
//
//$current_day = date("w");
//echo $current_day;

$hours_array = array();
for($i = 0; $i < 25; $i++){
    $zero = "0";
    if($i >= 10) $zero = "";
    array_push($hours_array, $zero.$i.":00");
}
//print_r($hours_array);


foreach ($restaurants as $restaurant){

    $hour = "";
//    for($i = 0; $i < 24; $i++){
//        $hour = $hour.$i;
//    }
    $hour10 = "";
    $hour12 = "";
    $hour14 = "";
    $hour18 = "";
    $hour9 = "";

    $newHourLine = true;
    $counter = 0;

    foreach ($restaurant['openingHours'] as $opening_hours){

//        $array_diff = array_diff($hours_array, $opening_hours);
//        print_r($array_diff);



        for($i = 1; $i < 24; $i++){
            if($opening_hours['open'] == $hours_array[10]){     // Changer 10 par une variable testant toutes les heures possibles
                $hour10 = $hours_array[10] . '<br>';
            }
            elseif ($opening_hours['open'] == $hours_array[12]){    // Idem
                $hour12 = $hours_array[12] . '<br>';
            }
            elseif ($opening_hours['open'] == $hours_array[9]){    // Idem
                $hour9 = $hours_array[9] . '<br>';
            }
            // etc... créer boucle prenant les valeurs du tableau d'heures
        }

        if($opening_hours['close'] == $hours_array[18]){     // Changer 10 par une variable testant toutes les heures possibles
            $hour18 = $hours_array[18] . '<br>';
        }
        elseif ($opening_hours['close'] == $hours_array[14]){    // Idem
            $hour14 = $hours_array[14] . '<br>';
        }





//        $open_hour = substr($opening_hours['open'], 0, 2);
//        $open_hour = intval($open_hour);
//
//        $close_hour = substr($opening_hours['close'], 0, 2);
//        $close_hour = intval($close_hour);

        if($opening_hours['dayOfWeek'] != 'Saturday' && $opening_hours['dayOfWeek'] != 'Sunday'){
            if($counter == 5){
//                echo $opening_hours['open'] . ' - ' . $opening_hours['close'] . '<br>';
            }
//            else {
//                echo $counter . '<br>';
//            }



        }

//        echo 'counter : ' . $counter . '<br>';



    }

    echo $hour10;
    echo $hour12;
    echo $hour14;
    echo $hour18;
    echo $hour9;

}




?>

<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th><?php echo trad('restaurant', $lang); ?></th>
            <th><?php echo trad('opening_time', $lang); ?></th>
            <th><?php echo trad('closing', $lang); ?></th>
        </tr>
        </thead>
        <tbody>

            <?php
                foreach ($restaurants as $restaurant) {
                    echo '<tr>';
                        echo '<td>' . $restaurant['name'] . '</td>';

                        echo '<td>';
                            $day_of_week = "";
                            $newDayLine = true;
                            $sunday_array = array();


                            foreach ($restaurant['openingHours'] as $opening_hours){

                                echo '<div class="row">';
                                    echo '<div class="col-6">';

                                    if($day_of_week != $opening_hours['dayOfWeek']){

                                        $day_of_week = $opening_hours['dayOfWeek'];

                                        $newDayLine = true;

                                        if($day_of_week != 'Sunday'){
                                            echo trad($opening_hours['dayOfWeek'], $lang);
                                        }
                                        else{
                                            $sunday_array[] = $opening_hours['dayOfWeek'];
//                                            var_dump($sunday_array);

                                        }


                                    } else {
//                                        echo $days[0];
                                        $newDayLine = false;
                                    }



                                    echo '</div>';
                                    echo '<div class="col-6">';

                                    if($opening_hours['isClosed'] && $newDayLine && $opening_hours['dayOfWeek'] != 'Sunday'){
                                        echo trad('closed', $lang);
                                    }

                                    if(!empty($opening_hours['open']) && !empty($opening_hours['close'])){
                                        echo $opening_hours['open'] . ' - ' . $opening_hours['close'] . '<br>';
                                    } elseif (!empty($opening_hours['open']) || !empty($opening_hours['close'])){
                                        echo $opening_hours['open'] . '<br>';
                                        echo $opening_hours['close'] . '<br>';
                                    } elseif (!empty($opening_hours['open']) && !empty($opening_hours['close']) && $opening_hours['dayOfWeek'] != 'Sunday' && $opening_hours['dayOfWeek'] != 'Saturday' && (count($opening_hours['open']) == "10:00") == 5){
                                        echo 'ok';
                                    }


                                    echo '</div>';


                                echo '</div>';
                            }



                                echo '<div class="row">';
                                    echo '<div class="col-6">';

                                foreach ($restaurant['openingHours'] as $opening_hours){

                                    if($day_of_week != $opening_hours['dayOfWeek']) {

                                        $day_of_week = $opening_hours['dayOfWeek'];

                                        $newDayLine = true;

                                        if ($opening_hours['dayOfWeek'] == 'Sunday') {
                                            echo trad($opening_hours['dayOfWeek'], $lang);
                                        }


                                    echo '</div>';

                                    echo '<div class="col-6">';

                                        if($opening_hours['isClosed'] && $newDayLine && $opening_hours['dayOfWeek'] == 'Sunday'){
                                            echo trad('closed', $lang);
                                        }

                                    echo '</div>';
                                echo '</div>';
                                    }
                                }

                        echo '</td>';

                        echo '<td>';
                            foreach ($restaurant['vacations'] as $vacations){
                                echo '<p>' . $vacations['note'] . '</p>';

                                $start_date_timestamp = strtotime($vacations['dateStart']);
                                $start_date = date("d.m.Y", $start_date_timestamp);

                                $end_date_timestamp = strtotime($vacations['dateEnd']);
                                $end_date = date("d.m.Y", $end_date_timestamp);

                                echo '<p>' . trad('from', $lang) . $start_date . trad('to', $lang) . $end_date . '</p>';

                            }
                        echo '</td>';

                    echo '</tr>';

                }

            ?>
        </tbody>
    </table>
</div>

<div>
<?php


//foreach ($restaurants as $restaurant){
//    foreach ($restaurant['openingHours'] as $openingHours) {
//
//        echo '<p>' . $openingHours['dayOfWeek'] . '</p>';
//        echo '<p>' . $openingHours['isClosed'] . '</p>';
//        echo '<p>' . $openingHours['open'] . '</p>';
//        echo '<p>' . $openingHours['close'] . '</p>';
//
//    }
//}

?>
</div>
