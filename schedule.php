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

?>

<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th><?php echo trad('restaurant', $lang) ?></th>
            <th><?php echo trad('opening_time', $lang) ?></th>
            <th><?php echo trad('closing', $lang) ?></th>
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
                            $counter = 0;
                            $sunday_array = array();


                            foreach ($restaurant['openingHours'] as $opening_hours){

                                echo '<div class="row">';
                                    echo '<div class="col-6">';

                                    if($day_of_week != $opening_hours['dayOfWeek']){

//                                        $counter++;

                                        $day_of_week = $opening_hours['dayOfWeek'];

                                        $newDayLine = true;

//                                        if($counter != 0){
                                        if($day_of_week != "Sunday"){
                                            echo $day_of_week;
                                        }
                                        else{
                                            $sunday_array[] = $opening_hours['dayOfWeek'];
//                                            var_dump($sunday_array);

                                        }

//                                        }

                                    } else {
//                                        echo $days[0];
                                        $newDayLine = false;
                                    }



                                    echo '</div>';
                                    echo '<div class="col-6">';

                                    if($opening_hours['isClosed'] && $newDayLine && $opening_hours['dayOfWeek'] != "Sunday"){
                                        echo trad('closed', $lang);
                                    }

                                    if(!empty($opening_hours['open']) && !empty($opening_hours['close'])){
                                        echo $opening_hours['open'] . ' - ' . $opening_hours['close'] . '<br>';
                                    } elseif (!empty($opening_hours['open']) || !empty($opening_hours['close'])){
                                        echo $opening_hours['open'] . '<br>';
                                        echo $opening_hours['close'] . '<br>';
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


                                        if ($opening_hours['dayOfWeek'] == "Sunday") {
                                            echo $opening_hours['dayOfWeek'];
                                        }




                                    echo '</div>';

                                    echo '<div class="col-6">';

                                        if($opening_hours['isClosed'] && $newDayLine && $opening_hours['dayOfWeek'] == "Sunday"){
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
