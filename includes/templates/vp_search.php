<div id="propertyFilter">
    <form action="<?php echo get_permalink()?>" method="POST">
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
