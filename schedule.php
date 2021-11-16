<table class="table tablesaw tablesaw-stack <?php if(!empty($selected_resto_id)) echo 'single-schedule' ?>">
    <thead>
    <tr>
        <th <?php if(!empty($selected_resto_id)) echo 'class="d-none"' ?>><?php echo trad('restaurant', $lang); ?></th>
        <th><?php echo trad('opening_time', $lang); ?></th>
        <th><?php echo trad('closing', $lang); ?></th>
    </tr>
    </thead>
    <tbody>

        <?php
            foreach ($restaurants as $restaurant) {
                // If there is a 'resto_id' given as URL's parameter, we want to show only the menu lines associated
                if (!empty($selected_resto_id) && $selected_resto_id != $restaurant['id']) {
                    continue;

                }
                echo '<tr>';
                    // Restaurant name
                ?>

                    <td <?php if(!empty($selected_resto_id)) echo 'class="d-none"' ?>><?php echo $restaurant['name'] . '</td>';
                    // Opening hours
                    echo '<td>';
                        $day_of_week = "";
                        $newDayLine = true;
                        // Put Sunday hours at end of array (temp fix)
                        // $opening_hours = array_filter($restaurant['openingHours'],function($item){return $item['dayOfWeek']!="Sunday";})+$restaurant['openingHours'];

                        foreach ($restaurant["openingHours"] as $opening_hour){

                            echo '<div class="row">';
                                echo '<div class="col-5">';

                                if($day_of_week != $opening_hour['dayOfWeek']){
                                    $day_of_week = $opening_hour['dayOfWeek'];
                                    $newDayLine = true;

                                    echo trad($opening_hour['dayOfWeek'], $lang);

                                } else {
                                    $newDayLine = false;
                                }

                                echo '</div>';
                                echo '<div class="col-7">';

                                if($opening_hour['isClosed']){
                                    if($newDayLine) echo trad('closed', $lang);
                                }else if(!empty($opening_hour['open']) && !empty($opening_hour['close'])){
                                    echo $opening_hour['open'] . ' - ' . $opening_hour['close'];
                                }

                                echo '</div>';
                            echo '</div>';
                        }

                    echo '</td>';
                    // Holidays
                    echo '<td>';
                        foreach ($restaurant['vacations'] as $vacations){
                            if(!empty($vacations['note'])){
                                echo '<p>' . $vacations['note'] . '</p>';
                            }

                            if(!empty($vacations['dateStart']) && !empty($vacations['dateEnd'])){
                                $start_date_timestamp = strtotime($vacations['dateStart']);
                                $start_date = date("d.m.Y", $start_date_timestamp);

                                $end_date_timestamp = strtotime($vacations['dateEnd']);
                                $end_date = date("d.m.Y", $end_date_timestamp);

                                echo '<p class="text-nowrap">' . trad('from', $lang) . $start_date . trad('to', $lang) . $end_date . '</p>';
                            }

                        }
                    echo '</td>';
                echo '</tr>';
            }
        ?>
    </tbody>
</table>
