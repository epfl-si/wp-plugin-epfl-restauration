<!--<div class="container-full mx-3">-->
<div class="container">
    <ul class="nav nav-tabs" role="tablist">

        <?php
        // Return the current day and the six next days of the week
        for ($i = 0; $i < 7; $i++){
            $current_date = date('Y-m-d', strtotime( " +" . $i . "days"));
            // 'active' represents the active tab
            $active = ($current_date == $selected_date)?"active":"";
            $item_class = ($i>5)?"d-none d-sm-inline":"";
            ?>
            <li class="nav-item <?= $item_class ?>">
                <a class="nav-link <?= $active ?>" data-toggle="tab" href="" onclick="window.location.href='?date=<?= $current_date ?>'" role="tab" aria-controls="day" aria-selected="true">
                    <?php
                    // add X days to the current date
                    echo date_i18n('D',  strtotime( " +" . $i . "days")); ?>
                </a>
            </li>
            <?php
        }
        ?>

    </ul>

    <div class="tab-content py-3">
        <div class="tab-pane fade show active" id="day1" role="tabpanel" aria-labelledby="day1-tab">
            <div class="row my-3">

                <div class="col-md-4 col-xl-3">
                    <div class="form-group">
                        <label for="mySelectID"><?php echo trad('restaurants_selection', $lang) ?></label>
                        <select id="mySelectID" class="select-multiple select-resto" multiple="multiple" data-placeholder="<?php echo trad('restaurants_list', $lang) ?>">
                            <?php
                            foreach ($restaurants as $restaurant) {
                                // If there is a 'resto_id' given as URL's parameter, we want to show only the menu lines associated.
                                // In this case, the option is selected.
                                $selected = '';
                                if ($selected_resto_id == $restaurant['id']) {
                                    $selected = 'selected';
                                }
                                echo '<option class="restos ' . $restaurant['id'] . '" value="' . $restaurant['id'] . '" ' . $selected . '>' . $restaurant['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="form-group">
                        <label for="types-menus"><?php echo trad('menus_selection', $lang) ?></label>
                        <select id="types-menus" class="select-multiple select-menu" multiple="multiple" data-placeholder="<?php echo trad('menus_types', $lang) ?>">
                            <?php
                            // Sort alpha
                            asort($ini_array_sections['cat_'.$lang], SORT_REGULAR );
                            foreach ($ini_array_sections['cat_'.$lang] as $key => $value){
                                echo '<option class="menus ' . $key . '" value="' . $key . '">' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="form-group">
                        <label for="formControlRange"><?php echo trad('minimum_nutrimenu_score', $lang) ?></label>
                        <label for="nutrimenu-score"></label>
                        <input type="range" id="nutrimenu-score" name="nutrimenu-score" class="form-control-range custom-range" min="0" max="13" step="1" value="0">
                        <output class="nutriscore-value" for="nutrimenu-score" aria-hidden="true"></output>
                    </div>

                </div>

                <div class="col-md-12 col-xl-3">
                    <div class="custom-controls-inline">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="offer-lunch" name="offer-type" value="lunch" class="custom-control-input" checked>
                            <label class="custom-control-label" for="offer-lunch"><?php echo trad('noon_offers', $lang) ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="offer-dinner" name="offer-type" value="dinner" class="custom-control-input">
                            <label class="custom-control-label" for="offer-dinner"><?php echo trad('evening_offers', $lang) ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="tag-group mb-3">
                        <label for=""><?php echo trad('search_filters', $lang) ?></label>
                        <div class="filterTags">
                            <a href="javascript:void(0)" class="tag tag-plain"><?php echo trad('reset_all', $lang) ?><span class="remove removeAll" tabindex="-1" title="Remove">×</span></a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="tablesaw-container">
                <table class="table table-restauration table-sortable" data-tablesaw-sortable data-tablesaw-mode="stack" id="menuTable">
                    <thead>
                        <th data-tablesaw-sortable-col><?php echo trad('offer', $lang) ?></th>
                        <th><?php echo trad('score', $lang) ?></th>
                        <th data-tablesaw-sortable-col><?php echo trad('restaurant', $lang) ?></th>
                        <th data-tablesaw-sortable-col data-tablesaw-sortable-numeric><?php echo trad('price', $lang) ?></th>
                    </thead>
                    <tbody>
                    <?php

                    $nutri_score_array = ["-","E","D-","D","D+","C-","C","C+","B-","B","B+","A-","A","A+"];
                    $eco_score_array = ["-","A","B","C","D","E"];

                    // Check if any menu are available
                    if(empty($restaurants) || empty($restaurants[0]['menuLines'])){
                        echo error_msg("error_nomenu", $lang);
                        $restaurants = Array();
                    }

                    $images_path = plugin_dir_url(__FILE__) . "images/";

                    // Set a defined price order display
                    $prices_levels_order = array(
                        "PersonStudent",
                        "PersonEmployee",
                        "PersonDoctorand",
                        "PersonOther",
                        "PricePer100gr",
                        "SizeHalf",
                        "ComboMainStarter",
                        "ComboMainStarterDesert"
                    );


                    foreach ($restaurants as $restaurant) {
                        foreach ($restaurant['menuLines'] as $menuLine) {
                            if(!empty($menuLine['meals']) && is_array($menuLine['meals']) && count($menuLine['meals']) > 0){
                                foreach ($menuLine['meals'] as $meals){
                                    $category = "";
                                    $mealtype = "";
                                    $nutri_score_value = $nutri_score = null;
                                    $eco_score = $eco_score_value = null;

                                    if(is_array($meals['items']) && count($meals['items']) > 0){
                                        foreach ($meals['items'] as $item){
                                            if($item['menuSection'] == 'mainCourse' && !empty($item['recipe']['category']) && $item['recipe']['category']!="unclassified"){
                                                $category = $category . ' ' . $item['recipe']['category'];
                                            }
                                            if($item['menuSection'] == 'mainCourse' && !empty($item['recipe']['cuisine']) && $item['recipe']['cuisine']!="unclassified"){
                                                $category = $category . ' ' . $item['recipe']['cuisine'];
                                            }
                                        }
                                    }else{
                                        // No menu 'items' to display, continue to next 'meals'
                                        continue;
                                    }

                                    $mealtype = $meals['mealType'];

                                    if(!empty($meals['evaluation']['nutriScore'])) {
                                        $nutri_score = $meals['evaluation']['nutriScore'];
                                        $nutri_score_value = array_search($nutri_score,$nutri_score_array);
                                    }
                                    if(!empty($meals['evaluation']['ecoScore'])) {
                                        $eco_score = $meals['evaluation']['ecoScore'];
                                        $eco_score_value = array_search($eco_score,$eco_score_array);
                                    }

                                    echo '<tr class="menuPage' . $category . ' ' . $mealtype . '" data-restoid="'.$restaurant['id'].'" data-ns-score="' . $nutri_score_value . '" data-ns-score-txt="' . $nutri_score . '">';

                                        echo '<td class="menu">';
                                            echo '<div class="menu-content">';
                                                    echo '<div class="descr">';
                                                    foreach ($meals['items'] as $item) {
                                                        if(!empty($item['recipe']['name'])) {
                                                            if ($item['menuSection'] == 'mainCourse') {
                                                                if($lang == 'en' && !empty($item['recipe']['name_en'])){
                                                                    echo '<b>' . $item['recipe']['name_en'] . '</b><br>';
                                                                } else {
                                                                    echo '<b>' . $item['recipe']['name'] . '</b><br>';
                                                                }
                                                            } else {
                                                                if($lang == 'en' && !empty($item['recipe']['name_en'])){
                                                                    echo $item['recipe']['name_en'] . '<br>';
                                                                } else {
                                                                    echo $item['recipe']['name'] . '<br>';
                                                                }

                                                            }
                                                        }

                                                        if(!empty($item['recipe']['notesOrigin'])){
                                                            echo '<em>' . trad('origin', $lang) . ': ' . $item['recipe']['notesOrigin'] . '</em><br>';
                                                        }
                                                        if(!empty($item['recipe']['category']) && $item['recipe']['category'] == 'vegetarian' || $item['recipe']['category'] == 'vegan'){
                                                            echo '<img src="' . $images_path . 'vegetarian.svg' . '" alt="Vegetarian" height="20"> <em>' . trad($item['recipe']['category'], $lang) . '</em><br>';
                                                        }

                                                    }
                                                    echo '</div>';
                                            echo '</div>';
                                        echo '</td>';

                                        echo '<td>';
                                            echo '<div class="nutrimenu text-nowrap">';
                                                if (isset($nutri_score)) {
                                                        echo '<img src="' . $images_path . 'nutriMenu_score_' . strtolower($nutri_score) . '.svg' . '" alt="NutriScore" height="55">';
                                                }
                                                if (isset($eco_score)) {
                                                        echo '<img src="' . $images_path . 'ecoMenu_score_' . strtolower($eco_score) . '.svg' . '" alt="EcoScore" height="55">';
                                                }
                                            echo '</div>';
                                        echo '</td>';

                                        echo '<td class="restaurant">';
                                            if(isset($ini_array_sections['url_restaurants'][$restaurant['id']])) {
                                                echo '<a href="' . $ini_array_sections['url_restaurants'][$restaurant['id']] . '">' . $restaurant['name'];
                                            } else{
                                                echo $restaurant['name'];
                                            }
                                        echo '</td>';

                                        echo '<td class="prices">';
                                            if (isset($meals['prices']) && count($meals['prices']) > 0) {
                                                // Sort the table price from a custom order $prices_levels_order
                                                usort($meals['prices'], function ($a, $b) use ($prices_levels_order) {
                                                    $pos_a = array_search($a['description'], $prices_levels_order);
                                                    $pos_b = array_search($b['description'], $prices_levels_order);
                                                    return $pos_a - $pos_b;
                                                });

                                                foreach ($meals['prices'] as $price) {
                                                    if(!empty($price['description'])){
                                                        echo '<span class="price" style="white-space: nowrap">';
                                                        switch ($price['description']) {
                                                            case "PersonStudent":
                                                                echo '<abbr title="' . trad('student_price', $lang) . '" class="text-primary">E</abbr> ' . $price['price'] . ' '. $price['currency'];
                                                                break;
                                                            case "PersonEmployee":
                                                                echo '<abbr title="' . trad('campus_price', $lang) . '" class="text-primary">C</abbr> ' . $price['price'] . ' ' . $price['currency'];
                                                                break;
                                                            case "PersonOther":
                                                                echo '<abbr title="' . trad('visitor_price', $lang) . '" class="text-primary">V</abbr> ' . $price['price'] . ' ' . $price['currency'];
                                                                break;
                                                            case "PersonDoctorand":
                                                                echo '<abbr title="' . trad('phd_student_price', $lang) . '" class="text-primary">D</abbr> ' . $price['price'] . ' ' . $price['currency'];
                                                                break;
                                                            case "SizeHalf":
                                                                echo '<abbr title="' . trad('half_portion', $lang) . '" class="text-primary">&#189;</abbr> ' . $price['price'] . ' ' . $price['currency'];
                                                                break;
                                                            case "PricePer100gr":
                                                                echo $price['price'] . ' ' . $price['currency'] . '/100g';
                                                                break;
                                                            case "ComboMainStarter":
                                                                echo '<abbr class="text-primary">' . trad('starter_maincourse', $lang) . '</abbr><br>' . $price['price'] . ' ' . $price['currency'];
                                                                break;
                                                            case "ComboMainStarterDesert":
                                                                echo '<abbr class="text-primary">' . trad('starter_maincourse_dessert', $lang) . '</abbr><br>' . $price['price'] . ' ' . $price['currency'];
                                                                break;
                                                            default:
                                                                echo $price['price'] . ' ' . $price['currency'];
                                                        }
                                                        echo '</span>';
                                                    }
                                                }
                                            }
                                        echo '</td>';

                                    echo '</tr>';

                                }
                            }
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

