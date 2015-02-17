var vpautocomplete;

jQuery(document).ready(function () {

    // The slider being synced must be initialized first
    jQuery('#carousel').flexslider({
        animation: "slide",
        controlNav: false,
        slideshow: false,
        itemWidth: 160,
        itemMargin: 5,
        asNavFor: '#slider'
    });

    jQuery('#slider').flexslider({
        animation: "fade",
        controlNav: false,
        animationLoop: false,
        slideshow: true,
        sync: "#carousel"
    });

    //Google autofill for locations
    vpautocomplete = new google.maps.places.Autocomplete(
        (document.getElementById('vp_location')),
        { types: ['geocode'] });

    jQuery("#vp_location").blur(function () {
        setLngLat();
    });

    if (jQuery("#vp_location").length > 0) setLngLat();

    jQuery('#vp_orderby_select').change(function () {
        jQuery("input:hidden[name=vp_orderby]").val(jQuery('#vp_orderby_select').val());
        jQuery("#property_form").submit();
    });

    //paging
    jQuery(".properties-page").click(function () {
        jQuery("input:hidden[name=vp_page]").val(jQuery(this).attr("data-id"));
        jQuery("#property_form").submit();
        return false;
    });

    jQuery("#propertyFilter input:radio[name=area]").change(function () {
        jQuery(this).parents("form").submit();
    });

    jQuery('#viewList').click(function () {
        jQuery("input:hidden[name=vp_view]").val("list");
        jQuery("#property_form").submit();
    });

    jQuery('#viewMap').click(function () {
        jQuery("input:hidden[name=vp_view]").val("map");
        jQuery("#property_form").submit();
    });

    if (jQuery("#propertyFilter input:radio[name=area]").val() == 'To Rent') {
        jQuery(".vp_price").hide();
        jQuery(".vp_rent").show();
    } else {
        jQuery(".vp_price").show();
        jQuery(".vp_rent").hide();
    }

});

function setLngLat() {
    var address = document.getElementById('vp_location').value;
    if (address != "") {
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'address': address }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {

                if (jQuery("input[name='lat']").length > 0)
                    jQuery("input[name='lat']").val(results[0].geometry.location.lat());
                else
                    jQuery("#vp_location").after("<input type='hidden' name='lat' value='" + results[0].geometry.location.lat() + "' />");

                if (jQuery("input[name='lng']").length > 0)
                    jQuery("input[name='lng']").val(results[0].geometry.location.lng());
                else
                    jQuery("#vp_location").after("<input type='hidden' name='lng' value='" + results[0].geometry.location.lng() + "' />");
            }
        });
    }
}