<?php

function vp_get_qsareas() {
    global $wpdb;
    global $vp_qsearchvars;    
    
    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "SELECT DISTINCT area FROM $table_name";    
    return $wpdb->get_results($sql);
}

function vp_get_areas() {
    global $wpdb;
    global $vp_searchvars;    
    
    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "SELECT DISTINCT area FROM $table_name";    
    if ($result = $wpdb->get_results($sql)) {
        foreach ($result as $varea) {
            if ($vp_searchvars['area']==$varea->area)
                echo "<input type='radio' name='area' value='".$varea->area."' checked='checked'>".$varea->area."<br />";
            else
                echo "<input type='radio' name='area' value='".$varea->area."'>".$varea->area."<br />";
        }
    }       
}

function vp_get_branches($mybranches) {  
    global $wpdb;
    global $vp_searchvars;    
    echo "<select name='branchid' id='vp_branch_select'>";
    echo "<option value=''>Any</option>";
    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "SELECT DISTINCT branchid FROM $table_name ORDER BY branchid";
    if ($result = $wpdb->get_results($sql)) {
        foreach ($result as $vbranch) {
            $vbname = (array_key_exists($vbranch->branchid,$mybranches)) ? $mybranches[$vbranch->branchid] : $vbranch->branchid;
            if ($vbranch->branchid == $vp_searchvars['branchid'])
                echo "<option value='".$vbranch->branchid."' selected='selected' />".$vbname."</option>";
            else
                echo "<option value='".$vbranch->branchid."' />".$vbname."</option>";
        }
    }    
    echo "</select>";
}

function vp_get_bedrooms() {
    global $vp_searchvars;    
    
    echo "<select name='bedrooms' id='vp_bedrooms_select'>";
    echo "<option value=''>Any</option>";
    for ($i=1;$i<6;$i++) {
        if ($i==$vp_searchvars['bedrooms'])
            echo "<option selected='selected'>".$i."</option>";
        else
            echo "<option>".$i."</option>";
    }
    echo "</select>";
}

function vp_get_minprice() {
    global $vp_searchvars;    
    
    $pricebands = array(25000,50000,75000,100000,125000,150000,175000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,3000000,5000000);
    echo "<select name='minprice' id='vp_minprice_select'>";
    echo "<option value=''>No Minimum</option>";
    foreach ($pricebands as $thisband) {
        if ($thisband==$vp_searchvars['minprice'])
            echo "<option value='$thisband' selected='selected'>&pound;".number_format($thisband,0,"",",")."</option>";
        else
            echo "<option value='$thisband'>&pound;".number_format($thisband,0,"",",")."</option>";
    }
    echo "</select>";
}

function vp_get_maxprice() {
    global $vp_searchvars;    
    
    $pricebands = array(50000,75000,100000,125000,150000,175000,200000,300000,400000,500000,600000,700000,800000,900000,1000000,3000000,5000000,10000000);
    echo "<select name='maxprice' id='vp_maxprice_select'>";
    echo "<option value=''>No Maximum</option>";
    foreach ($pricebands as $thisband) {
        if ($thisband==$vp_searchvars['maxprice'])
            echo "<option value='$thisband' selected='selected'>&pound;".number_format($thisband,0,"",",")."</option>";
        else
            echo "<option value='$thisband'>&pound;".number_format($thisband,0,"",",")."</option>";
    }
    echo "</select>";
}

function vp_get_property_types($mytype = "checkbox") {
    global $wpdb;
    global $vp_searchvars;    
    
    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "SELECT DISTINCT property_type FROM $table_name WHERE 1=1";
    if ($vp_searchvars['branchid']!="") $sql.=" AND branchid=".$vp_searchvars['branchid'];
    if ($vp_searchvars['area']!="") $sql.=" AND area='".$vp_searchvars['area']."'";
    $sql.=" ORDER BY property_type";
    if ($mytype=="select") echo "<select name='type' id='vp_property_type'><option value=''>All</option>";
    
    if ($result = $wpdb->get_results($sql)) {
        foreach ($result as $vtype) {
            if (in_array($vtype->property_type,explode(",",$vp_searchvars['type']))) {
                if ($mytype=="select")
                        echo "<option value='".$vtype->property_type."' selected='selected' />".$vtype->property_type."</option>";               
                    else
                        echo "<input type='checkbox' name='type[]' value='".$vtype->property_type."' checked='checked' /><label>".$vtype->property_type."</label>";
            } else {
                if ($mytype=="select")
                    echo "<option value='".$vtype->property_type."' />".$vtype->property_type."</option>";               
                else
                    echo "<input type='checkbox' name='type[]' value='".$vtype->property_type."' /><label>".$vtype->property_type."</label>";
            }
        }
    }       
    if ($mytype=="select") echo "</select>";
}

function vp_get_location() {
    global $vp_searchvars;    
    echo $vp_searchvars['location'];    
}

function vp_get_radius() {
    global $vp_searchvars;    
    
    $radbands = array(1,3,5,10,15,20,30,40,50);
    echo "<select name='radius' id='vp_radius_select'>";
    foreach ($radbands as $thisband) {
        if ($thisband==$vp_searchvars['radius'])
            echo "<option value='$thisband' selected='selected'>within ".$thisband." miles</option>";
        else
            echo "<option value='$thisband'>within ".$thisband." miles</option>";
    }
    echo "</select>";
}

function vp_get_orderby() {
    global $vp_searchvars;    
    echo "<select name='sortby' id='vp_orderby_select'>";
    if ($vp_searchvars['orderby']=="price asc")
        echo "<option value='price asc' selected='selected'>Price Low to High</option>";
    else
        echo "<option value='price asc'>Price Low to High</option>";
    if ($vp_searchvars['orderby']=="price desc")
        echo "<option value='price desc' selected='selected'>Price High to Low</option>";
    else
        echo "<option value='price desc'>Price High to Low</option>";    
    echo "</select>";
}

function vp_get_view() {
    global $vp_searchvars;    
    return $vp_searchvars['view'];
}

function vp_get_search_link() {
    $options = get_option("vp_options");
    return get_permalink($options["searchpageid"]);
}

function vp_list_head() {
    global $vp_searchvars;
    $options = get_option("vp_options");
    $vp_out = "";
    
    //Setup the form for paging and postback
    $vp_out .= "<form id='property_form' action=".get_permalink($options["searchpageid"])." method=\"POST\">";
    foreach ($vp_searchvars as $key => $value) {  
        $vp_out .= "<input type=\"hidden\" name=\"vp_".$key."\" value=\"".$value."\" />";
    }
    return $vp_out;
}    

function vp_theproperties() {
    global $vp_searchvars;
    global $wpdb;
    //build the query
    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $table_name";
    $sqlwhere = " WHERE web_status NOT IN ('Let','Sold')";
    if ($vp_searchvars["branchid"]!="") $sqlwhere.=" AND branchid=" . $vp_searchvars["branchid"];
    if ($vp_searchvars["area"]!="") $sqlwhere.=" AND area='" . $vp_searchvars["area"] ."'";
    if ($vp_searchvars["featured"]!="") $sqlwhere.=" AND featured=" . ($vp_searchvars["featured"]=='yes') ? "1" : "0";
    if ($vp_searchvars["bedrooms"]!="") $sqlwhere.=" AND bedrooms>=" . $vp_searchvars["bedrooms"];
    if ($vp_searchvars["minprice"]!="") $sqlwhere.=" AND price>=" . $vp_searchvars["minprice"];
    if ($vp_searchvars["maxprice"]!="") $sqlwhere.=" AND price<=" . $vp_searchvars["maxprice"];
    if ($vp_searchvars["type"]!="") $sqlwhere.=" AND property_type in ('".str_replace(",","','",$vp_searchvars["type"])."')";
    if ($vp_searchvars["vebraid"]!="") {
        $sqlwhere=" WHERE vebraid in (".$vp_searchvars["vebraid"].")";
        $vp_searchvars["orderby"] = "FIELD(vebraid,".$vp_searchvars["vebraid"].")";
    }   
    if ($vp_searchvars["radius"]!="") {}
    //geo-locate : use form geo if supplied or use location is supplied and lat/lng are not
    if (($vp_searchvars["lng"]=="" || $vp_searchvars["lat"] =="") && $vp_searchvars["location"]!="") {      
        $ggeo = vp_position($vp_searchvars["location"]);
        if ($ggeo!="") {
            $vp_searchvars["lng"] = $ggeo[0]["geometry"]["location"]["lng"];
            $vp_searchvars["lat"] = $ggeo[0]["geometry"]["location"]["lat"];
        } 
    }
    if ($vp_searchvars["lng"]!="" && $vp_searchvars["lat"]!="") {
        $sqlwhere.=" AND (((acos(sin((".$vp_searchvars["lat"]."*pi()/180)) * 
            sin((latitude*pi()/180))+cos((".$vp_searchvars["lat"]."*pi()/180)) * 
            cos((latitude*pi()/180)) * cos(((".$vp_searchvars["lng"]."- longitude)* 
            pi()/180))))*180/pi())*60*1.1515
            ) <= ".$vp_searchvars["radius"];
    }
    //ordering
    $sql .= $sqlwhere. " ORDER BY ". $vp_searchvars["orderby"];
    //paging
    if ($vp_searchvars["view"]=="list") {
        $startfrom = ((intval($vp_searchvars["page"])-1) * intval($vp_searchvars["pagesize"]));
        $sql .= " LIMIT ".$startfrom.",".$vp_searchvars["pagesize"];
    }
    $returnrows = $wpdb->get_results($sql);
    $vp_searchvars["property_count"] = $wpdb->get_var("SELECT FOUND_ROWS()");
    return $returnrows;
}

function vp_property_count() {
    global $vp_searchvars;
    $vp_searchvars["property_count"];
}

function vp_page_count() {
    global $vp_searchvars;
    return floor((intval($vp_searchvars["property_count"])-1) / intval($vp_searchvars["pagesize"])) + 1;
}

function vp_current_page() {
    global $vp_searchvars;
    return $vp_searchvars["page"];
}

function vp_list_footer() {
    return "</form>";
}

function vp_theproperty() {
    global $vp_detailvars;
    global $wpdb;
    //see if the URL has the property ref in it
    $options = get_option('vp_options');
    $thisurl = vp_curURL();
    $baseurl = get_permalink($options['pageid']);  
    
    if (isset($_GET["vebraid"])) {
        $vp_detailvars['vebraid'] = $_GET["vebraid"];
    } else {
        if (strpos($thisurl,$baseurl) !== FALSE) 
            $vp_detailvars['vebraid'] = str_replace("/","",str_replace($baseurl,"",$thisurl));
    }
    //build the query

    $table_name = $wpdb->prefix."vebraproperties";
    $sql = "SELECT * FROM $table_name LIMIT 1";
    if ($vp_detailvars["vebraid"]!="")
        $sql = "SELECT * FROM $table_name WHERE vebraid=".$vp_detailvars["vebraid"];
    if ($vp_detailvars["agentref"]!="")
        $sql = "SELECT * FROM $table_name WHERE agentref='".$vp_detailvars["agentref"]."'";
    return $wpdb->get_results($sql);
}

function vp_propertyurl($verbaid) {
    $options = get_option('vp_options');
    $thislink = get_permalink($options['pageid']);
    if (strpos($thislink,"?") !== FALSE)
        return get_permalink($options['pageid'])."&vebraid=".$verbaid;
    else
        return get_permalink($options['pageid']).$verbaid;
}

function vp_propertyimage($vebraid, $sortorder, $pclass) {
    global $wpdb;
    $table_name = $wpdb->prefix."vebrafiles";   
    $pimage = $wpdb->get_results("SELECT * FROM $table_name WHERE vebraid=$vebraid AND sortorder=$sortorder ORDER BY sortorder LIMIT 1");
    if (sizeof($pimage) > 0) {
        return "<img src=\"".$pimage[0]->url."\" alt=\"".$pimage[0]->name."\" class=\"$pclass\" />";
    } else {
        return "";
    }
    return get_permalink($options['pageid'])."/".$agentref;
}

function vp_propertyimages($vebraid) {
    global $wpdb;
    $table_name = $wpdb->prefix."vebrafiles";   
    return $wpdb->get_results("SELECT * FROM $table_name WHERE vebraid=$vebraid AND (url like '%.jpg' OR url like '%.png' OR url like '%.gif') ORDER BY sortorder");
}

function vp_propertypdfs($vebraid) {
    global $wpdb;
    $table_name = $wpdb->prefix."vebrafiles";   
    return $wpdb->get_results("SELECT * FROM $table_name WHERE vebraid=$vebraid AND (url like '%.pdf') ORDER BY sortorder");
}

function vp_propertytext($vebraid) {
    global $wpdb;
    $table_name = $wpdb->prefix."vebraparagraphs";   
    return $wpdb->get_results("SELECT * FROM $table_name WHERE vebraid=$vebraid ORDER BY sortorder");
}

function vp_map_pins($tproperties) {
    global $vp_searchvars;
    $pstr = "var locations = [";
    $first = true;
    foreach($tproperties as $property) {
        if ($first)
            $first = false;
        else
            $pstr .= ",";
        $pstr .= "[";
        $pstr .= "'".$property->address_custom."',";
        $pstr .= "'".$property->latitude."',";
        $pstr .= "'".$property->longitude."',";
        $pstr .= "'".str_replace("'","\'","<div class=\"vp_marker_content\">".vp_propertyimage($property->vebraid,0,"vp_pin_image")."<br /><em>".$property->address_custom."</em><br />".$property->property_type."</em><br /><span>&pound;".number_format($property->price,0,"",",")." ".$property->price_postfix."</span><br /><a href=\"".vp_propertyurl($property->vebraid)."\">View details</a></div>")."'";
        $pstr .= "]\r\n";
    }
    $pstr .= "];\r\n";
    if (array_key_exists("lat",$vp_searchvars) && array_key_exists("lng",$vp_searchvars)) {
        $pstr .= "var myLatLng = new google.maps.LatLng(".$vp_searchvars["lat"].",".$vp_searchvars["lng"].");\r\n";
    } else {
        $options = get_option("vp_options");
        $pstr .= "var myLatLng = new google.maps.LatLng(".$options["lat"].",".$options["lng"].");\r\n";        
    }   
    $pstr.= "var myRadius = 1609.344 * ".$vp_searchvars["radius"]."\r\n";
    if ($vp_searchvars["radius"] < 3) $pstr.= "var myZoom=13;\r\n";
    if ($vp_searchvars["radius"] >= 3 && $vp_searchvars["radius"] < 5) $pstr.= "var myZoom=12;\r\n";
    if ($vp_searchvars["radius"] >= 5 && $vp_searchvars["radius"] < 10) $pstr.= "var myZoom=11;\r\n";
    if ($vp_searchvars["radius"] >= 10 && $vp_searchvars["radius"] < 20) $pstr.= "var myZoom=10;\r\n";
    if ($vp_searchvars["radius"] >= 20 && $vp_searchvars["radius"] < 40) $pstr.= "var myZoom=9;\r\n";
    if ($vp_searchvars["radius"] >= 40) $pstr.= "var myZoom=8;\r\n";
    if ($vp_searchvars["location"]=="")
        $pstr.= "var showRadius=false;\r\n";
    else
        $pstr.= "var showRadius=true;\r\n";
    return $pstr;
}

function vp_curURL() {
    $pageURL = 'http';
    //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function vp_position($address) {
    $url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $json = curl_exec($ch);
    curl_close($ch); 	

    echo "<!--" . $json . "-->";
    $data = json_decode($json, TRUE);
    if($data['status']=="OK"){
       	return $data['results'];
    } else {
        return "";
    }
}

?>