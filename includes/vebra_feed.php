<?php

//Function to authenticate self to API and return/store the Token
function vp_getToken($url) {
    if (get_option('vp_options')) {
        $options = get_option('vp_options');
        $username = $options['username'];
        $password = $options['password'];
	    //Start curl session
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	    curl_setopt($ch, CURLOPT_HEADER, 1); 
	    curl_setopt($ch, CURLOPT_NOBODY, 1); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($ch);
        update_option( "vp_apistatus", $result);

	    curl_close($ch); 
	    foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $headerLine)
        {
            if (strpos($headerLine,':') !== false) {
		        $line = explode(':', $headerLine);
    		    $header = $line[0];
	    	    $value = trim($line[1]);

                if($header == "Token") {               
				    //save the token in a settings
                    update_option( "vp_token", base64_encode($value));
                    update_option( "vp_apistatus", "Success");
			    }
            }
	    }
	
	    //If we have been given a token request XML from the API authenticating using the token
	    if (get_option('vp_token')!="") {
		    return vp_connect($url);
        } else {
            return false;
        }
    }
}

function vp_connect($url) {
	if (get_option('vp_token')) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.get_option('vp_token')));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
        
		//Check if we have been authorised or not
		if($info['http_code'] == '401') {
			return vp_getToken($url);
		} else	
            return $result;		
	} else {
		return vp_getToken($url);	
	}
}

function vp_populate($getall) {
    global $wpdb;
    if (get_option('vp_options')) {
        $options = get_option('vp_options');
        $datafeedid = $options['feedid'];
        $xml = false;
        $updated = 0; $deleted = 0;
            
        if ($getall) {     
            //remove all data from the system
            $table_name = $wpdb->prefix."vebraproperties";
            $wpdb->query("TRUNCATE TABLE $table_name");
            $table_name = $wpdb->prefix."vebrafiles";
            $wpdb->query("TRUNCATE TABLE $table_name");
            $table_name = $wpdb->prefix."vebraparagraphs";
            $wpdb->query("TRUNCATE TABLE $table_name");
                
            //Get list of branches
            $xml = vp_connect("http://webservices.vebra.com/export/$datafeedid/v8/branch");
            if ($xml !== false && $xml != "") {
                $branches = simplexml_load_string($xml);
                foreach ($branches->branch as $thisbranch) {
                    //populate the branch details
                    vp_updatebranch($thisbranch->url);
                    
                    //get the list of properties   
                    $url = $thisbranch->url."/property";
                   
                    //get the properties
                    $properties = simplexml_load_string(vp_connect($url));
                    foreach ($properties->property as $thisproperty) {
                        vp_updateproperty($thisproperty->url);
                        $updated++;
                    }
                }
            }
        } 
        else 
        {
            $url = "http://webservices.vebra.com/export/$datafeedid/v8/property/".get_option("vp_lastupdated");
            $xml = vp_connect($url);
            if ($xml !== false && $xml != "") {
                $properties = simplexml_load_string($xml);
                foreach ($properties->property as $thisproperty) {
                    //check the property action if it exists
                    if ($thisproperty->action=='deleted') {
                        $vebraid = $thisproperty->propid;
                        $table_name = $wpdb->prefix."vebraproperties";
                        $wpdb->query("DELETE FROM ".$table_name." WHERE vebraid=".$vebraid);
                        $table_name = $wpdb->prefix."vebrafiles";
                        $wpdb->query("DELETE FROM ".$table_name." WHERE vebraid=".$vebraid);
                        $table_name = $wpdb->prefix."vebraparagraphs";
                        $wpdb->query("DELETE FROM ".$table_name." WHERE vebraid=".$vebraid);
                        $deleted++;
                    } else {
                        vp_updateproperty($thisproperty->url);
                        $updated++;
                    }
                }
            }
        }
            
        if ($xml !== false) {
            $date = new DateTime();
            update_option("vp_lastupdated", $date->format('Y/m/d/H/i/s'));
        }
        $table_name = $wpdb->prefix."vebraproperties";
        $property_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $wpdb->insert( $wpdb->prefix."vebralog", array('updated' => $updated, 'deleted' => $deleted, 'totalproperties' => $property_count),array('%d','%d','%d'));
        update_option("vp_propertycount", $property_count);
        //remove all but the last 10 logs from vebra logs
        $wpdb->query("DELETE FROM ".$wpdb->prefix."vebralog WHERE id NOT IN (SELECT id FROM (SELECT id FROM ".$wpdb->prefix."vebralog ORDER BY id DESC LIMIT 10) x)");
    }        
}

function vp_updatebranch($url) {
    global $wpdb;
    $wpdb->show_errors();
    $bi = array();
    libxml_use_internal_errors(true);
    
    //populate the branch details
    $branch = new DOMDocument();
    if ($branch->loadXML(vp_connect($url))) {
        $thisbranch = $branch->firstChild;
        $bi["branchid"] = vp_getNode($thisbranch,"BranchID");
        $bi["firmid"] = vp_getNode($thisbranch,"FirmID");
        $bi["name"] = vp_getNode($thisbranch,"name");
        $bi["street"] = vp_getNode($thisbranch,"street");
        $bi["town"] = vp_getNode($thisbranch,"town");
        $bi["county"] = vp_getNode($thisbranch,"county");
        $bi["postcode"] = vp_getNode($thisbranch,"postcode");
        $bi["phone"] = vp_getNode($thisbranch,"phone");
        $bi["email"] = vp_getNode($thisbranch,"email");
        //update the data in the DB - replace is a clever update or insert
        $table_name = $wpdb->prefix."vebrabranches";
        $wpdb->replace($table_name, $bi, 
            array('%d','%d','%s','%s','%s','%s','%s','%s','%s') 
        );
    } else {
        libxml_clear_errors();        
    }   
}
    
function vp_updateproperty($url) {
    global $wpdb;
    $wpdb->show_errors();
    //Lookups
    $a_database = array("1" => "To Buy", "2" => "To Rent", "5" => "Commercial", "6" => "Commercial", "7" => "Commercial", "15" => "Developments", "41" => "To Rent", "118" => "Commercial" );
    //Solex instances will tend to use brand for the database
    //$a_branch = array(1 => "To Buy", 2 => "To Buy", 3 => "To Buy", 11 => "To Let", 12 => "To Let", 13 => "To Let", 21 => "Rural", 31 => "Developments", 41 => "Commercial", 42 => "Commercial", 43 => "Commercial" );
    $a_sales_status = array("0" => "For Sale", "1" => "Under Offer", "2" => "Sold", "3" => "SSTC", "4" => "For Sale By Auction", "5" => "Reserved", "6" => "New Instruction", "7" => "Just on the Market", "8" => "Price Reduction", "9" => "Keen to Sell", "10" => "No Chain", "11" => "Vendor will pay stamp duty", "12" => "Offers in the region of", "13" => "Guide Price");
    $a_let_status = array("0" => "To Let", "1" => "Let", "2" => "Under Offer", "3" => "Reserved", "4" => "Let Agreed");
    $a_file_type = array(0 => "Image", 1 => "Map", 2 => "Floorplan", 3 => "360 Tour", 4 => "EHouse", 5 => "Ipix", 7 => "PDF", 8 => "Url", 9 => "Energy Performance Certificate");
    $a_furnished = array("0" => "Furnished", "1" => "Part Furnished", "2" => "Un-Furnished", "3" => "Not Specified", "4" => "Furnished / Un-Furnished");
    $a_lettype = array("0" => "Not Specified", "1" => "Long Term", "2" => "Short Term", "3" => "Student", "4" => "Commercial");
    $a_rmqualifier = array("1" => "Price on Application", "2" => "Guide Price", "3" => "Fixed Price", "4" => "Offers in Excess of", "5" =>  "Offers in the Region of", "6" => "Sale by Tender", "7" => "From", "9" => "Shared Ownership", "10" => "Offers Over", "11" => "Part Buy Part Rent", "12" => "Shared Equity");
   
    $insert = array();
    
    //add or update the property details
    libxml_use_internal_errors(true);
    $iproperty = new DOMDocument();
    if ($iproperty->loadXML(vp_connect($url))) {
        $oproperty = $iproperty->firstChild;
    
        //map values to text
        $insert["vebraid"] = $oproperty->getAttribute("id");
        $insert["branchid"] =  $oproperty->getAttribute("branchid");
        $insert["databaseid"] = $oproperty->getAttribute("database");
        $insert["area"] = "";
        
        $tdatabase = $oproperty->getAttribute("database");
        if (array_key_exists($tdatabase, $a_database)) 
            $insert["area"] =$a_database[$tdatabase];
        
        //OVER-RIDE FOR SOLEX
        //$tbranch = $oproperty->getAttribute("branchid");
        //if (array_key_exists($tbranch, $a_branch)) 
        //    $insert["area"] =$a_branch[$tbranch];
    
        $insert["featured"] = $oproperty->getAttribute("featured"); 
        $insert["uploaded"] = vp_formatdate(vp_getNode($oproperty,"uploaded"));
        $insert["agentref"] = vp_getNode($oproperty,"agents");
        $taddress = $oproperty->getElementsByTagName("address")->item(0);
        $insert["address_name"] = vp_getNode($taddress,"name");
        $insert["address_street"] = vp_getNode($taddress,"street");
        $insert["address_locality"] = vp_getNode($taddress,"locality");
        $insert["address_town"] = vp_getNode($taddress,"town");
        $insert["address_county"] = vp_getNode($taddress,"county");
        $insert["address_postcode"] = vp_getNode($taddress,"postcode");
        $insert["address_custom"] = vp_getNode($taddress,"custom_location");
        $insert["address_display"] = vp_getNode($taddress,"display");
    
        $tprice = $oproperty->getElementsByTagName("price")->item(0);
        $insert["price_postfix"] = $tprice->getAttribute("rent");
        $insert["price_qualifier"] = $tprice->getAttribute("qualifier");
        $insert["price_currency"] = $tprice->getAttribute("currency");
        $insert["price_display"] = ($tprice->getAttribute("display")=="yes") ? 1 : 0;
        $insert["price"] = $tprice->nodeValue;
    
        $insert["furnished"] = "";
        $tfurnished = vp_getNode($oproperty,"furnished");
        if (array_key_exists($tfurnished, $a_furnished))
            $insert["furnished"] = $a_furnished[$tfurnished];
    
        $insert["let_type"] = "";
        $tlettype = vp_getNode($oproperty,"rm_let_type_id");
        if (array_key_exists($tlettype, $a_lettype)) 
            $insert["let_type"] = $a_lettype[$tlettype]; 
    
        $insert["longitude"] = vp_getNode($oproperty,"longitude");
        $insert["latitude"] = vp_getNode($oproperty,"latitude");
    
        $insert["web_status"] = "";
        $tstatus = vp_getNode($oproperty,"web_status");
        if ($tdatabase==1 & array_key_exists($tstatus, $a_sales_status))
            $insert["web_status"] = $a_sales_status[$tstatus];
        if ($tdatabase==2 & array_key_exists($tstatus, $a_let_status)) 
            $insert["web_status"] = $a_let_status[$tstatus];

        $insert["property_type"] = "";
        foreach ($oproperty->getElementsByTagName("type") as $thistype) {
            if ($thistype->nodeValue != "") {
                $insert["property_type"] = $thistype->nodeValue;
                break;
            }
        }      
        
        $insert["bedrooms"] = vp_getNode($oproperty,"bedrooms");
        $insert["receptions"] = vp_getNode($oproperty,"receptions");
        $insert["bathrooms"] = vp_getNode($oproperty,"bathrooms");
        $insert["excerpt"] = vp_getNode($oproperty,"description");

        $insert["rm_qualifier"] = "";
        $trmqualifier = vp_getNode($oproperty,"rm_qualifier");
        if (array_key_exists($trmqualifier, $a_rmqualifier))
            $insert["rm_qualifier"] = $a_rmqualifier[$trmqualifier];
    
        //merge bullets into one
        $bullets = "<ul>";
        foreach ($oproperty->getElementsByTagName("bullet") as $thisbullet) {
            $bullets .= "<li>".$thisbullet->nodeValue."</li>";
        }
        $bullets .= "</ul>";
        $insert["bullets"] = $bullets;
       
        //now handle the images
        //Remove the images first
        $table_name = $wpdb->prefix."vebrafiles";
        $wpdb->delete($table_name, array('vebraid' => $insert["vebraid"]), array('%d'));
    
        //add the the images again
        $tfiles = $oproperty->getElementsByTagName("files")->item(0);
        foreach ($tfiles->getElementsByTagName("file") as $thisfile) {
            $vebraid = $insert["vebraid"];
            $sortorder = $thisfile->getAttribute("id");
            $filetype = $thisfile->getAttribute("type");
            $fname = vp_getNode($thisfile,"name");
            $furl = vp_getNode($thisfile,"url");
            $wpdb->insert($table_name, array('vebraid' => $vebraid, 'sortorder' => $sortorder, 'name' => $fname, 'url' => $furl, 'filetype' => $filetype),array('%d','%d','%s','%s','%d'));
        }
    
        //Remove existing paragraphs first
        $table_name = $wpdb->prefix."vebraparagraphs";
        $wpdb->delete($table_name, array('vebraid' => $insert["vebraid"]), array('%d'));
    
        $fulltext = "";
        foreach ($oproperty->getElementsByTagName("paragraph") as $thisparagraph) {
            if ($thisparagraph->getAttribute("type")=="0") {
                $vebraid = $insert["vebraid"];
                $thisdimensions = "";
                $thissortorder = $thisparagraph->getAttribute("id");
                $thisname =  vp_getNode($thisparagraph,"name");
                if ($thisparagraph->getElementsByTagName("metric")->length > 0) $thisdimensions = $thisparagraph->getElementsByTagName("metric")->item(0)->nodeValue;
                if ($thisparagraph->getElementsByTagName("imperial")->length > 0) $thisdimensions = $thisparagraph->getElementsByTagName("imperial")->item(0)->nodeValue;
                if ($thisparagraph->getElementsByTagName("mixed")->length > 0) $thisdimensions = $thisparagraph->getElementsByTagName("mixed")->item(0)->nodeValue;
                $thistext =  vp_getNode($thisparagraph,"text");
                $filesortorder = "0";
                $pfile = $thisparagraph->getElementsByTagName("file")->item(0);
                if ($pfile->hasAttribute("ref")) $filesortorder=$pfile->getAttribute("ref");
                $wpdb->insert($table_name, array('vebraid' => $vebraid, 'sortorder' => $thissortorder, 'filesortorder' => $filesortorder, 'name' => $thisname, 'dimensions' => $thisdimensions, 'description' => $thistext),array('%d','%d','%d','%s','%s','%s'));           
            }                     
        }

        //update the data in the DB - replace is a clever update or insert
        $table_name = $wpdb->prefix."vebraproperties";
        $wpdb->replace($table_name, $insert, 
            array('%d','%d','%d','%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%f','%s','%s','%f','%f','%s','%s','%d','%d','%d','%s','%s','%s') 
        );
    } else {
        libxml_clear_errors();        
    }
}


function vp_getNode($domroot, $tagname) {
    if ($domroot->getElementsByTagName($tagname)->length > 0) 
        return $domroot->getElementsByTagName($tagname)->item(0)->nodeValue;
    else
        return "";
}

function vp_formatdate($somedate) {
    if (($timestamp = strtotime($somedate)) !== false) {
        $parts = explode('/',$somedate);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        // return DateTime::createFromFormat('d/m/Y', $somedate)->format('Y-m-d');
    }
    else
        return date('Y-m-d');
}
?>