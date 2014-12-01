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
            <p>No matching properties found.  Please try alternative search criteria.</p>
        <?php } ?>
        <?php echo vp_list_footer();?>
    </div>