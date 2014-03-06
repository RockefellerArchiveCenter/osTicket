    </div>
<?php
if(is_object($thisstaff) && $thisstaff->isStaff()) { ?>
    <div style="display:none">
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
        <img src="autocron.php" alt="" width="1" height="1" border="0" />
        <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
    </div>
<?php
} ?>
</div>
<div id="overlay"></div>
<div id="loading">
    <h4>Please Wait!</h4>
    <p>Please wait... it will take a second!</p>
</div>
<div class="dialog" style="display:none;width:650px;" id="popup">
    <div class="body"></div>
</div>
</body>
</html>
