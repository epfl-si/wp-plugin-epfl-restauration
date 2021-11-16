$(document).ready(function () {

// Filter the menu page according to the restaurants list, menu types list, nutriscore and day offers
// Hide all the options that are not in the filter
function filterMenus(){

    var restosList = $("select option:selected.restos");
    var menusTypeList = $("select option:selected.menus");
    var offerType = $('input[name=offer-type]:checked').val();
    var currentNutriScore = $("#nutrimenu-score").val();

    const nutriScoresArray = [
        {"txt": "-", "val": 0},
        {"txt": "E", "val": 1},
        {"txt": "D-", "val": 2},
        {"txt": "D", "val": 3},
        {"txt": "D+", "val": 4},
        {"txt": "C-", "val": 5},
        {"txt": "C", "val": 6},
        {"txt": "C+", "val": 7},
        {"txt": "B-", "val": 8},
        {"txt": "B", "val": 9},
        {"txt": "B+", "val": 10},
        {"txt": "A-", "val": 11},
        {"txt": "A", "val": 12},
        {"txt": "A+", "val": 13}
    ]

    // Browse the menus list (<tr> -> class="menuPage), show the selected options and hide the unselected ones
    $("#menuTable tr.menuPage").each(function (){

        var menuLine = $(this);

        // By default, the menu line is shown
        menuLine.show();

        // Filter by restaurant
        if(restosList.length > 0) {

            let found = false;
            restosList.each(function () {

                // Test if menu page contains restaurants IDs => test if menuLine is shown
                //if (menuLine.hasClass($(this).val())) {
                if (menuLine.attr('data-restoid') === $(this).val()){
                    found = true;
                    return false;
                }
            });
            // If a restaurant is not selected, it is not shown in the menu page
            if (!found) {
                menuLine.hide();
            }
        }

        // Filter by menu type
        if(menusTypeList.length > 0) {

            let found = false;
            menusTypeList.each(function () {

                // Test if menu page contains menu types => test if menuLine is shown
                if (menuLine.hasClass($(this).val())) {
                    found = true;
                }

            });
            // If a type of menu is not selected, it is not shown in the menu page
            if (!found) {
                menuLine.hide();
            }
        }

        // Hide if not lunch or dinner
        if(!menuLine.hasClass(offerType)){
            menuLine.hide();
        }

        // Hide if lower than selected nutriScore
        let nsValue = Number(menuLine.attr('data-ns-score'));

        if (nsValue < nutriScoresArray[currentNutriScore].val) {
            menuLine.hide();
        }

    });

    $(".nutriscore-value").text(nutriScoresArray[currentNutriScore].txt);

}


// Add and remove tags, according to selected/unselected options. Update the menu page
function updateTags(){

    // Add a tag to the selected restaurants options
    $( "select option:selected.restos" ).each(function() {

        let restaurant = $(this).text();
        let restaurantID = $(this).val();

        if(!$("#tag-"+restaurantID).length){
            $(".filterTags").append('<a href="javascript:void(0)" class="tag tag-primary restoTag '+ restaurant +' " id="tag-' + restaurantID + '" >' + restaurant + '<span class="remove removeTag" tabindex="-1" title="Remove">×</span></a>');
        }

    });

    // Add a tag to the selected menu types options
    $( "select option:selected.menus" ).each(function() {

        let menu = $(this).text();
        let menuID = $(this).val();

        if(!$("#tag-"+menuID).length){
            $(".filterTags").append('<a href="javascript:void(0)" class="tag tag-primary menuTag '+ menu +' " id="tag-' + menuID + '" >' + menu + '<span class="remove removeTag" tabindex="-1" title="Remove">×</span></a>');
        }

    });

    // Array containing the selected restaurants options
    let selectedRestosArray = $('select.select-resto').multipleSelect('getSelects');

    // Remove restaurants tags
    $(".restoTag").each(function (){

        // Delete "tag-" before the ID in the way to have only the ID
        let idToRemove = $(this).attr('id').replace("tag-","");

        // If the restaurant is not selected, then it will be removed
        if($.inArray(idToRemove, selectedRestosArray) === -1){
            $(this).remove();
        }
    });

    // Array containing the selected menu types options
    let selectedMenusArray = $('select.select-menu').multipleSelect('getSelects');

    // Remove menu types tags
    $(".menuTag").each(function (){

        // Delete "tag-" before the ID in the way to have only the ID
        let idToRemove = $(this).attr('id').replace("tag-","");

        // If the menu type is not selected, then it will be removed
        if($.inArray(idToRemove ,selectedMenusArray) === -1){
            $(this).remove();
        }
    });

}

// Remove tag when clicking on the remove button
$(".filterTags").on('click',".removeTag", function (){

    let tagID = $(this).parent().attr('id');
    let iD = tagID.replace('tag-','');

    $("#"+tagID).remove();

    // RESTAURANTS
    // Array of selected restaurants
    let selectedResto = $('select.select-resto');
    let selectedRestosArray = selectedResto.multipleSelect('getSelects');

    // Array of selected restaurants after removing a tag
    selectedRestosArray = $.grep(selectedRestosArray, function(value) {
        return value !== iD;
    });

    // Update the selected restaurants options after removing the restaurant tag
    selectedResto.multipleSelect('setSelects', selectedRestosArray);

    // MENU TYPES
    // Array of selected menu types
    let selectedMenus = $('select.select-menu');
    let selectedMenusArray = selectedMenus.multipleSelect('getSelects');

    // Array of selected menu types after removing a tag
    selectedMenusArray = $.grep(selectedMenusArray, function(value) {
        return value !== iD;
    });

    // Update the selected menu types options after removing the menu type tag
    selectedMenus.multipleSelect('setSelects', selectedMenusArray);

});

// Filter when selecting a restaurant
$("select.select-resto").change(function (){
    filterMenus();
    updateTags();
});

// Filter the menu on the initial display
$(".select-multiple .select-resto").ready(function (){
    filterMenus();
    updateTags();
});

// Filter when selecting a menu type
$("select.select-menu").change(function () {
    filterMenus();
    updateTags();
});

// Filter menu page when NutriMenu score change
$("#nutrimenu-score").change(function () {
    filterMenus();
    updateTags();
});

// Filter menu page when offer types change
$('input[name=offer-type]').change(function () {
    filterMenus();
    updateTags();
});

// Remove all tags
$(".removeAll").click(function () {
    $('#offer-lunch').prop('checked', true);
    $("#nutrimenu-score").val(0);
    $(".nutriscore-value").text('-');
    $('select.select-resto').multipleSelect('uncheckAll');
    $('select.select-menu').multipleSelect('uncheckAll');
});

});
