<div id="vp_properties">
    <div id="propertyFilter">
    <form action="<?php echo get_permalink()?>" method="POST">
    <input type="hidden" name="vp_view" value="<?php echo vp_get_view(); ?>" /> 
    <div class="property_refine">Refine your search:</div>

    <div class="property_type">
        <?php vp_get_areas(); ?>       
    </div>
    <div class="property_search_group">
        <p>Minimum number of bedrooms</p>
        <?php vp_get_bedrooms(); ?>       
    </div>
    <div class="property_search_group">
        <p>Minimum price of property</p>
        <?php vp_get_minprice(); ?>               

    </div>
    <div class="property_search_group">
        <p>Maximum price of property</p>
        <?php vp_get_maxprice(); ?>       
    </div>
    <div class="property_search_group">
        <p>Type of property</p>
        <?php vp_get_property_types(); ?>
    </div>    

    <div class="property_search_group">
        <p>Location</p>
        <input type="text" id="vp_location" name="location" placeholder="Enter postcode or place name" value="<?php vp_get_location(); ?>" />
    </div>
        
    <div class="property_search_group">
        <p>Search Radius</p>
        <?php vp_get_radius(); ?>
    </div>
            
    <input class="submit" type="submit" value="Search" />
    </form>
 </div>

    <div id="propertyResults">
        <?php  echo vp_list_head();
        if ($properties=vp_theproperties()) { ?>
            <div id="propertyListView">
	            <div id="propertyOrdering" class="clearfix">
		            <div id="orderBy">
			            <span id="vp_order_message">Order by:</span>
                        <?php vp_get_orderby(); ?>
                    </div>
	            </div>
                <div class="clearfix"> 
                    <?php foreach ($properties as $property) { ?>
                    <div class="property">
                        <div class="left_column">
                            <div class="property_tagline badge_<?php echo str_replace(" ","_",$property->web_status); ?>"></div>
                            <div class="property_image">
                                <a class="property_overview_thumb" href="<?php echo vp_propertyurl($property->agentref); ?>" title="<?php echo $property->address_name; ?>">
                                    <?php echo vp_propertyimage($property->vebraid,0,"property_image"); ?>
                                </a>
                            </div>
                        </div>
                        <div class="right_column">
                            <ul class="property_summary">
                                <li class="property_title">
                                    <a href="<?php echo vp_propertyurl($property->agentref); ?>"><?php echo $property->address_custom; ?><span><?php echo $property->property_type; ?></span></a>
                                </li>
                                <li class="property_address"><?php echo $property->address_display; ?></li>			  
				                <li class="property_price">&pound;<?php echo number_format($property->price,0,"",","); ?> <?php echo $property->price_postfix; ?></li>
                            </ul>
                            <div class="buttons clearfix">
                                <a href="<?php echo vp_propertyurl($property->agentref); ?>" class="button">View Details</a>
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
            <p>No matching properties found.  Please try alternative search criteria.</p>
        <?php } ?>
        <?php echo vp_list_footer();?>
    </div>
</div>