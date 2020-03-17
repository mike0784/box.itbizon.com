$(document).ready(function() {
    $(document).on('change', '.region-selector', function() {
        let parent =  $(this).closest('.travel-point-selector')
        let tree = parent.data('tree');
        let countrySelector = parent.find('.country-selector');
        let citySelector = parent.find('.city-selector');
        let regionId = $(this).val();

        countrySelector.empty();
        citySelector.empty();

        if(tree[regionId] !== undefined) {
            countrySelector.append('<option value=""></option>');
            $.each(tree[regionId].CHILD, function(i, e) {
                countrySelector.append('<option value="'+i+'">'+e.NAME+'</option>');
            });
        }
    });

    $(document).on('change', '.country-selector', function() {
        let parent =  $(this).closest('.travel-point-selector')
        let tree = parent.data('tree');

        let regionSelector  = parent.find('.region-selector');
        let countrySelector = $(this);
        let citySelector    = parent.find('.city-selector');

        let regionId  = regionSelector.val();
        let countryId = countrySelector.val();

        citySelector.empty();
        console.log(countryId);
        console.log(tree);
        if(tree[regionId] !== undefined) {
            if(tree[regionId].CHILD[countryId] !== undefined) {
                citySelector.append('<option value=""></option>');
                $.each(tree[regionId].CHILD[countryId].CHILD, function(i, e) {
                    citySelector.append('<option value="'+i+'">'+e.NAME+'</option>');
                });
            }
        }
    });
});