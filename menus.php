<style>

    /* Change color when mouse on selection list */
    .ms-choice:hover{
        background: #E6E6E6;
    }

    /* Hide red list point */
    .ms-drop ul li:before{
        background: none;
    }

    /* Hide [Select all] option (checkboxes) */
    .ms-select-all {
        display:none !important;
    }

    span.price {
        style="white-space: nowrap";
    }
</style>

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
                <table class="table table-restauration tablesaw tablesaw-stack" data-tablesaw-mode="stack" id="menuTable">
                    <thead class="sr-only">
                    <th></th>
                    </thead>
                    <tbody>
                    <?php

                    $nutri_score_array = ["-","E","D-","D","D+","C-","C","C+","B-","B","B+","A-","A","A+"];
                    $eco_score_array = ["-","A","B","C","D","E"];

                    foreach ($restaurants as $restaurant) {
                        foreach ($restaurant['menuLines'] as $menuLine) {
                            $category = "";
                            $mealtype = "";
                            $nutri_score_value = null;
                            $nutri_score = null;

                            if(is_array($menuLine['meals']) && count($menuLine['meals']) > 0){

                                foreach ($menuLine['meals'] as $meals){
                                    if(is_array($meals['items']) && count($meals['items']) > 0){
                                        foreach ($meals['items'] as $item){
                                            if($item['menuSection'] == 'mainCourse' && !empty($item['recipe']['category']) && $item['recipe']['category']!="unclassified"){
                                                $category = $category . ' ' . $item['recipe']['category'];
                                            }
                                            if($item['menuSection'] == 'mainCourse' && !empty($item['recipe']['cuisine']) && $item['recipe']['cuisine']!="unclassified"){
                                                $category = $category . ' ' . $item['recipe']['cuisine'];
                                            }
                                        }
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

                                    echo '<div class="row">';
                                        echo '<tr class="menuPage' . $category . ' ' . $mealtype . '" data-mealtype="" data-restoid="'.$restaurant['id'].'" data-ns-score="' . $nutri_score_value . '" data-ns-score-txt="' . $nutri_score . '">';

                                            echo '<div class="col-5">';
                                                echo '<td class="menu">';
                                                    echo '<div class="menu-content">';
                                                            echo '<div class="descr">';
                                                            foreach ($meals['items'] as $item) {
                                                                if(!empty($item['recipe']['name'])) {
                                                                    if ($item['menuSection'] == 'mainCourse') {
                                                                        echo '<b>' . $item['recipe']['name'] . '</b><br>';
                                                                    } else {
                                                                        echo $item['recipe']['name'] . '<br>';
                                                                    }
                                                                }

                                                                if(!empty($item['recipe']['notesOrigin'])){
                                                                    echo '<em>' . $item['recipe']['notesOrigin'] . '</em><br>';
                                                                }

                                                            }
                                                            echo '</div>';
                                                echo '</td>';
                                            echo '</div>';

                                            echo '<div class="col-3">';
                                                echo '<td>';
                                                    echo '<div class="nutrimenu">';
                                                        if (isset($meals['evaluation']['nutriScore'])) {

                                                                echo '<img src="' . $images_path . 'nutriMenu_score_' . $nutri_score . '.svg' . '" alt="NutriScore" height="60">';

                                                        }
                                                        if (isset($meals['evaluation']['ecoScore'])) {

                                                                echo '<img src="' . $images_path . 'ecoMenu_score_' . $eco_score . '.svg' . '" alt="EcoScore" height="60">';

                                                        }
                                                    echo '</div>';
                                                echo '</td>';
                                            echo '</div>';

                                            echo '<div class="col-2">';
                                                echo '<td class="restaurant">';
                                                    if(isset($ini_array_sections['url_restaurants'][$restaurant['id']])){
                                                        echo '<a href="' . $ini_array_sections['url_restaurants'][$restaurant['id']] . '">' . $restaurant['name'];
                                                    }
                                                    else{
                                                        echo $restaurant['name'];
                                                    }
                                                echo '</td>';
                                            echo '</div>';

                                            echo '<div class="col-2">';
                                                echo '<td class="prices">';
                                                if (isset($meals['prices']) && count($meals['prices']) > 0) {
                                                    foreach ($meals['prices'] as $price) {
                                                        switch ($price['description']) {
                                                            case "PersonStudent":
                                                                echo '<span class="price" style="white-space: nowrap"><abbr title="Prix étudiant" class="text-primary">E</abbr> ' . $price['price'] . ' ' . $price['currency'] . ' </span>';
                                                                break;
                                                            case "PersonEmployee":
                                                                echo '<span class="price" style="white-space: nowrap"><abbr title="Prix campus" class="text-primary">C</abbr> ' . $price['price'] . ' ' . $price['currency'] . ' </span>';
                                                                break;
                                                            case "PersonOther":
                                                                echo '<span class="price" style="white-space: nowrap"><abbr title="Prix visiteurs" class="text-primary">V</abbr> ' . $price['price'] . ' ' . $price['currency'] . ' </span>';
                                                                break;
                                                            case "PersonDoctorand":
                                                                echo '<span class="price" style="white-space: nowrap"><abbr title="Prix doctorants" class="text-primary">D</abbr> ' . $price['price'] . ' ' . $price['currency'] . ' </span>';
                                                                break;
                                                            default:
                                                                echo '<span class="price" style="white-space: nowrap">' . $price['price'] . ' ' . $price['currency'] . ' </span>';
                                                        }
                                                    }
                                                }
                                                echo '</td>';
                                            echo '</div>';

                                        echo '</tr>';
                                    echo '</div>';
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



