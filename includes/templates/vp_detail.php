<div id="vp_detail">
<?php 
    if ($properties=vp_theproperty()) {
        foreach ($properties as $property) {
            $thisbranch=vp_thebranch($property->branchid);?>
        
        <div class="backContainer clearfix">
			<button class="backButton" onclick="history.go(-1);">Back </button>
        </div>
        <div class="vp_title_wrapper clearfix">
        	<div class="left">
                <h1 class="vp-title entry-title"><?php echo $property->address_custom; ?><span> <?php echo $property->property_type; ?></span></h1>
                <div class="addressBox"><?php echo $property->address_display; ?><br />Branch: <?php echo $thisbranch->name.". ".$thisbranch->street.", ".$thisbranch->town.", ".$thisbranch->county.". ".$thisbranch->postcode.". ".$thisbranch->phone;?></div>
            </div>
            
            <div class="right">
                <h2><?php echo $property->web_status; ?></h2>
                <div class="vp_price_wrapper clearfix">
                    <div><?php echo $property->price_qualifier; ?> <span class="vp-price">&pound;<?php echo number_format($property->price,0,"",","); ?></span> <?php echo $property->price_postfix; ?></div>
                </div>
            </div>
            
        </div>

        <!--GALLERY-->
        <?php 
        if ($vpimages=vp_propertyimages($property->vebraid)) { ?>
            <div id="slider" class="flexslider">
                <ul class="slides">
                <?php
                foreach ($vpimages as $vpimage) { ?>
                    <li><div class="vp_slider_image" style="max-width:100%; max-height: 100%; background-image: url(<?php echo $vpimage->url; ?>)" ></div></li>
                <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <?php 
        if ($vpimages=vp_propertyimages($property->vebraid)) { ?>
            <div id="carousel" class="flexslider">
                <ul class="slides">
                <?php foreach ($vpimages as $vpimage) { ?>
                    <li><div class="thumbImage" style="background-image:url(<?php echo $vpimage->url; ?>);"></div></li>
                <?php } ?>
                </ul>
            </div>
        <?php } ?>
        
        <!--CONTENT-->
        <div class="vp_content_wrapper">
            <div class="vp_content_section">
                <?php echo $property->excerpt; ?>
            </div>
            <div class="vp_content_section">
                <?php echo $property->bullets; ?>
            </div>
            <div class="vp_content_section clearfix">
            <?php
            if ($paragraphs=vp_propertytext($property->vebraid)) {
                foreach ($paragraphs as $paragraph) { ?>
                    <h2><?php echo $paragraph->name; ?></h2>
                    <?php if ($paragraph->dimensions != "") { ?>
                        <div class="vp_dimensions"><?php echo $paragraph->dimensions; ?></div>               
                    <?php } ?>
                    <?php if ($paragraph->filesortorder != "0") { ?>
                        <?php echo vp_propertyimage($property->vebraid,$paragraph->filesortorder,"vp_large_image"); ?>               
                    <?php } ?>
                    <?php if ($paragraph->description != "") { ?>
                        <div class="vp_description clearfix"><?php echo $paragraph->description; ?></div>               
                    <?php } ?>
                <?php 
                }
            }
            ?>
            </div>
        </div>

        <div id="property_map" class="" style="width: 100%; height: 450px; position: relative;">
            <script type="text/javascript">
                function ginitialize() {
                    var myLatlng = new google.maps.LatLng(<?php echo $property->latitude; ?>, <?php echo $property->longitude; ?>);
                    var mapOptions = {
                        zoom: 12,
                        center: myLatlng
                    }
                    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
                    var contentString = '<div class="vp_marker_content"><?php echo vp_propertyimage($property->vebraid,0,"vp_pin_image"); ?><br /><em><?php echo $property->address_custom; ?></em><br /><?php echo $property->property_type; ?></em><br /><span>&pound;<?php echo number_format($property->price,0,"",","); ?> <?php echo $property->price_postfix; ?></span><br /><a href="<?php echo vp_propertyurl($property->vebraid); ?>">View details</a></div>';
                    var infowindow = new google.maps.InfoWindow({
                        content: contentString
                    });
                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map,
                        title: '<?php echo $property->address_custom; ?> <?php echo $property->property_type; ?>'
                    });
                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map,marker);
                    });
                }
                jQuery(document).ready(function () {
                    ginitialize();
                });
            </script>
            <div id="map-canvas" style="width: 100%; height: 100%; margin: 0px; padding: 0px"></div>
        </div>

        <?php
        }
    }
    ?>

</div>




