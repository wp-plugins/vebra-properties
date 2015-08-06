<div id="vp_quicksearch" class="clearfix vp_search">
    <form action="<?php echo vp_get_search_link()?>" method="POST">
    <input type="hidden" name="radius" value="3" />

        <div id="vp_qsearch_checkboxes">
            <?php 
            if ($result = vp_get_qsareas()) {
                foreach ($result as $varea) { ?>
                    <div class="search_checkbox">
                    <?php if ($vp_qsearchvars['area']==$varea->area)
                        echo "<input type='radio' name='area' value='".$varea->area."' checked='checked' /> <label>".$varea->area."</label>";
                    else
                        echo "<input type='radio' name='area' value='".$varea->area."'  /> <label>".$varea->area."</label>";
                    ?>
                    </div>                   
                <?php
                }
            } ?>      
        </div>

        <div id="vp_qsearch_address">
            <input class="vp_qsearch_input_address" id="vp_location" name="location" type="text" value="">
            <input class="vp_search_button" type="submit" value="Search">
        </div>
    </form>
</div>
