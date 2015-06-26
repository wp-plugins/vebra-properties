    <div id="propertyResults">
        <?php  echo vp_list_head();
        if ($properties=vp_theproperties()) {
            if (vp_get_view()=="list") {?>
            <div id="propertyListView">
	            <div id="propertyOrdering" class="clearfix">
		            <div id="orderBy">
			            <span id="vp_order_message">Order by:</span>
                        <?php vp_get_orderby(); ?>
                        <a id="viewMap" class="button">View Map</a>
                    </div>
	            </div>
                <div class="clearfix"> 
                    <?php foreach ($properties as $property) { ?>
                    <div class="property">
                        <div class="left_column">
                            <div class="property_tagline badge_<?php echo str_replace(" ","_",$property->web_status); ?>"></div>
                            <div class="property_image">
                                <a class="property_overview_thumb" href="<?php echo vp_propertyurl($property->vebraid); ?>" title="<?php echo $property->address_name; ?>">
                                    <?php echo vp_propertyimage($property->vebraid,0,"property_image"); ?>
                                </a>
                            </div>
                        </div>
                        <div class="right_column">
                            <ul class="property_summary">
                                <li class="property_title">
                                    <a href="<?php echo vp_propertyurl($property->vebraid); ?>"><?php echo $property->address_custom; ?><span><?php echo $property->property_type; ?></span></a>
                                </li>
                                <li class="property_address"><?php echo $property->address_display; ?></li>			  
				                <li class="property_price">&pound;<?php echo number_format($property->price,0,"",","); ?> <?php echo $property->price_postfix; ?></li>
                            </ul>
                            <div class="buttons clearfix">
                                <a href="<?php echo vp_propertyurl($property->vebraid); ?>" class="button">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="properties-paging">
                    <ul>
                        <?php if (vp_current_page() > 1) { ?>
                        <li><a href="#" data-id="<?php echo vp_current_page()-1; ?>" class="properties-page">&lt; Previous</a></li>
                        <?php } ?>
                        <li>Page <?php echo vp_current_page(); ?> of <?php echo vp_page_count(); ?></li>
                        <?php if (vp_current_page() < vp_page_count()) { ?>
                        <li><a href="#" data-id="<?php echo vp_current_page()+1; ?>" class="properties-page">&gt; Next</a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <?php } else { ?>
            <div id="propertyMapView">
                <div id="propertyOrdering" class="clearfix">
		            <div id="orderBy">
                        <a id="viewList" class="button">View List</a>
                    </div>
	            </div>

                <script type="text/javascript">
                    <?php echo vp_map_pins($properties); ?>

                    function ginitialise() {
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: myZoom,
                            center: myLatLng
                        });

                        var infowindow = new InfoBubble();

                        var marker, i;
                        for (i = 0; i < locations.length; i++) {
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                title: locations[i][0]
                            });

                            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                                return function () {
                                    infowindow.setContent(locations[i][3]);
                                    infowindow.open(map, marker);
                                }
                            })(marker, i));
                        }

                        if (showRadius) {
                            var cOptions = {
                                strokeColor: '#052e6b',
                                strokeOpacity: 0.6,
                                strokeWeight: 2,
                                fillColor: '#052e6b',
                                fillOpacity: 0.25,
                                map: map,
                                center: myLatLng,
                                radius: myRadius
                            };
                            // Add the circle for this city to the map.
                            cCircle = new google.maps.Circle(cOptions);
                        }
                    }

                    jQuery(document).ready(function () {
                        ginitialise();
                    });

                </script>
                <div id="map" style="width: 100%; height: 500px;"></div>
            </div>         
            <?php } 
        } else { ?>
            <p>No matching properties found.  Please try alternative search criteria.</p>
        <?php } ?>
        <?php echo vp_list_footer();?>
    </div>