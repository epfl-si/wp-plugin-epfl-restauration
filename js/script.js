$(document).ready(function () {

// Filter the menu page according to the restaurants list, menus types list, nutriscore and day offers
// Hide all the options that are not in the filter
function filterMenus(){

    var restosList = $("select option:selected.restos");
    var menusTypeList = $("select option:selected.menus");
    var offerType = $('input[name=offer-type]:checked').val();
    var currentNutriScore = $("#nutrimenu-score").val();
    // const nutriScoresArray = [
    //     {"txt": "-", "val": 0},
    //     {"txt": "E", "val": 0.5},
    //     {"txt": "D-", "val": 0.59999},
    //     {"txt": "D", "val": 0.62999},
    //     {"txt": "D+", "val": 0.66999},
    //     {"txt": "C-", "val": 0.69999},
    //     {"txt": "C", "val": 0.72999},
    //     {"txt": "C+", "val": 0.76999},
    //     {"txt": "B-", "val": 0.79999},
    //     {"txt": "B", "val": 0.82999},
    //     {"txt": "B+", "val": 0.86999},
    //     {"txt": "A-", "val": 0.89999},
    //     {"txt": "A", "val": 0.92999},
    //     {"txt": "A+", "val": 0.96999}
    // ]

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

    console.log("NutriScore sélectionné : " + nutriScoresArray[currentNutriScore].val + " text " + nutriScoresArray[currentNutriScore].txt);


    // Browse the menus list (<tr> -> class="menuPage), show the selected options and hide the unselected ones
    $("#menuTable tr.menuPage").each(function (){

        var menuLine = $(this);
        // console.log(menuLine);
        // By default, the menu line is shown
        menuLine.show();

        // Filter by restaurant
        if(restosList.length > 0) {

            let found = false;
            restosList.each(function () {

                // Show in console the selected restaurant
                // console.log('Selected restaurant : ');
                // console.log($(this).val() + ' : ' + $(this).text());

                // Test if menu page contains restaurants IDs => test if menuLine is shown
                //if (menuLine.hasClass($(this).val())) {
                if (menuLine.attr('data-restoid') === $(this).val()){
                    found = true;
                    console.log("Menu line is restaurant : " + $(this).val());
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

                // Show in console the menu type
                // console.log('Selected menu : ');
                // console.log($(this).val());

                // Test if menu page contains menus types => test if menuLine is shown
                if (menuLine.hasClass($(this).val())) {
                    found = true;
                    console.log("Menu line is type : " + $(this).val());
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

        // console.log('NS du menu (auto) : ' + nsValue + ', NS sélectionné -> ' + nutriScoresArray[currentNutriScore].txt + ' : ' + nutriScoresArray[currentNutriScore].val);

    });

    $(".nutriscore-value").text(nutriScoresArray[currentNutriScore].txt);

}


// Add and remove tags, according to selected/unselected options. Actualise the menu page
function updateTags(){

    // Add a tag to the selected restaurants options
    $( "select option:selected.restos" ).each(function() {

        let restaurant = $(this).text();
        let restaurantID = $(this).val();

        if(!$("#tag-"+restaurantID).length){
            $(".filterTags").append('<a href="javascript:void(0)" class="tag tag-primary restoTag '+ restaurant +' " id="tag-' + restaurantID + '" >' + restaurant + '<span class="remove removeTag" tabindex="-1" title="Remove">×</span></a>');
        }

    });

    // Add a tag to the selected menus types options
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

    // Array containing the selected menus types options
    let selectedMenusArray = $('select.select-menu').multipleSelect('getSelects');

    // Remove menus types tags
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

    // Show in console the tag to remove (ID of the restaurant or name of the menu)
    console.log('Tag to be removed : ' + iD);

    $("#"+tagID).remove();

    // RESTAURANTS
    // Array of selected restaurants
    let selectedResto = $('select.select-resto');
    let selectedRestosArray = selectedResto.multipleSelect('getSelects');

    // console.log('Selected restaurants tags (before removing the tag) : ');
    // console.log(selectedRestosArray);

    // Array of selected restaurants after removing a tag
    selectedRestosArray = $.grep(selectedRestosArray, function(value) {
        return value !== iD;
    });

    // console.log('Selected restaurants tags (after removing the tag) : ');
    // console.log(selectedRestosArray);

    // Actualise the selected restaurants options after removing the restaurant tag
    selectedResto.multipleSelect('setSelects', selectedRestosArray);

    // MENUS TYPES
    // Array of selected menus types
    let selectedMenus = $('select.select-menu');
    let selectedMenusArray = selectedMenus.multipleSelect('getSelects');

    // console.log('Selected menus types tags (before removing the tag) : ');
    // console.log(selectedMenusArray);

    // Array of selected menus types after removing a tag
    selectedMenusArray = $.grep(selectedMenusArray, function(value) {
        return value !== iD;
    });

    // console.log('Selected menus types tags (after removing the tag) : ');
    // console.log(selectedMenusArray);

    // Actualise the selected menus types options after removing the menu type tag
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