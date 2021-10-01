<style>

    .ms-choice{
        width: 100%;
    }

    .ms-drop{
        width: fit-content;
    }

    .ms-choice:hover{
        background: #E6E6E6;
    }

</style>

<?php

$images_path = "/wp-content/plugins/epfl-restauration/resources/images/";
//$images_path = "/resources/images";

$vars = parse_url( $params, $component = -1 );

parse_str($params, $params_array);
$lang = $params_array['lang'];
//echo '<h1>' . $lang . '</h1>';
//echo '<h1>' . $params_array['resto_id'] . '</h1>';

// Selection date of the menus
if(empty($_GET['date'])) {
    $selected_date = date('Y-m-d');
} else {
    $selected_date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
}

// Selection resto_id in URL's parameter
if(empty($params_array['resto_id'])) {
    $selected_resto_id = null;
} else {
    $selected_resto_id = $params_array['resto_id'];
}

// Analysis without sections
$ini_array = parse_ini_file("menus.ini");

// Connection data
$remote_url_menus = $ini_array['remote_url_menus'] . $selected_date;

// Language settings
if (empty($params_array['lang']) || $params_array['lang'] == 'fr') {
    $lang = 'fr';
//    setlocale(LC_ALL, ['fr', 'fra', 'fr_FR']);
    switch_to_locale('fr_FR');
    $menus_categories = 'french_menus_categories';

} else {
    $lang = 'en';
    $menus_categories = 'english_menus_categories';
}



// Create a stream

$cred = sprintf('Authorization: Basic %s',
    base64_encode($ini_array_sections['remote_url']['username_menus'] . ':' . $ini_array_sections['remote_url']['password_menus']));
$opts = array(
    'http'=>array(
        'method'=>"GET",
        'header' => $cred
    ),
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    )
);
$context = stream_context_create($opts);
$menus_file = file_get_contents($remote_url_menus, false, $context);

// Decodes JSON's file
$restaurants = json_decode($menus_file,true);
$restaurants = array_values(array_unique($restaurants, SORT_REGULAR));



?>

<!--<div class="container-full mx-3">-->
<div class="container">
    <ul class="nav nav-tabs" role="tablist">

        <?php
        // Return the current day and the six next days of the week
        for ($i = 0; $i < 7; $i++){
            if(current_date($i) == $selected_date) {
                // 'active' represents the active tab
                $active = 'active';
            }
            else {
                $active = '';
            }
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $active ?>" data-toggle="tab" href="" onclick="window.location.href='?date=<?= current_date($i) ?>'" role="tab" aria-controls="day" aria-selected="true"><?php echo week_days($i); ?></a>
            </li>
            <?php
        }
        ?>

    </ul>

    <div class="tab-content py-3">
        <div class="tab-pane fade show active" id="day1" role="tabpanel" aria-labelledby="day1-tab">
            <div class="row my-3">

                <div class="col-md-3">
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

                <div class="col-md-3">
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

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="formControlRange"><?php echo trad('minimum_nutrimenu_score', $lang) ?></label>
                        <label for="nutrimenu-score"></label>
                        <input type="range" id="nutrimenu-score" name="nutrimenu-score" class="form-control-range custom-range" min="0" max="13" step="1" value="0">
                        <output class="nutriscore-value" for="nutrimenu-score" aria-hidden="true"></output>
                    </div>

                </div>

                <div class="col-md-3">
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
            <div>
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

                    foreach ($restaurants as $restaurant) {
                        foreach ($restaurant['menuLines'] as $menuLine) {
                            $category = "";
                            $mealtype = "";
                            $nutri_score_value = null;

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
                                        $nutri_score_value = $meals['evaluation']['nutriScoreValue'];
                                    }

                                    echo '<tr class="menuPage' . $category . ' ' . $mealtype . '" data-mealtype="" data-restoid="'.$restaurant['id'].'" data-ns-score="' . $nutri_score_value . '">';

                                        echo '<td>';
                                        foreach ($meals['items'] as $item) {
                                            if(!empty($item['recipe']['name'])) {
                                                if ($item['menuSection'] == 'mainCourse') {
                                                    echo '<div class=""><b>' . $item['recipe']['name'] . '</b></div>';
                                                } else {
                                                    echo '<div class="">' . $item['recipe']['name'] . '</div>';
                                                }
                                            }

                                            if(!empty($item['recipe']['notesOrigin'])){
                                                echo '<div class=""><em>' . $item['recipe']['notesOrigin'] . '</em></div>';
                                            }

                                        }
                                        echo '</td>';

                                        echo '<td>';

                                        if (isset($meals['evaluation']['nutriScore'])) {
                                            echo '<div><img src="' . $images_path . 'nutriMenu_score_' . $meals['evaluation']['nutriScore'] . '.png' . '" alt="NutriScore" style="width: 75px; padding-top: 10px;"></div>';
                                        }

                                        echo '</td>';

                                        echo '<td>';
                                        echo '<div class="">' . $menuLine['name'] . '</div>';
                                        echo '</td>';


                                        echo '<td id="tr_id" class="' . $restaurant['name'] . '">';
                                        echo '<a href="' . $ini_array[$restaurant['id']] . '">' . $restaurant['name'];
                                        echo '</td>';

                                        echo '<td>';
                                        if (isset($meals['prices']) && count($meals['prices']) > 0) {
                                            foreach ($meals['prices'] as $price) {
                                                switch ($price['description']) {
                                                    case "PersonStudent":
                                                        echo '<div><abbr title="Prix étudiant" class="text-primary">E </abbr>' . $price['price'] . ' ' . $price['currency'] . '</div>';
                                                        break;

                                                    case "PersonEmployee":
                                                        echo '<div><abbr title="Prix campus" class="text-primary">C </abbr>' . $price['price'] . ' ' . $price['currency'] . '</div>';
                                                        break;
                                                    case "PersonOther":
                                                        echo '<div><abbr title="Prix visiteurs" class="text-primary">V </abbr>' . $price['price'] . ' ' . $price['currency'] . '</div>';
                                                        break;
                                                    default:
                                                        echo '<div>' . $price['price'] . ' ' . $price['currency'] . '</div>';
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



