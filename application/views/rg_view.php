<? if($_GET["rgnsk"] == "clown") { ?>

<div class="d-flex" style="background-image: url('/images/clown.png'); height: 1500px;">
<div class="col"></div>
<h1> <? echo base64_decode("Um9tYW4gR3VyZXYgMjAyNA=="); ?> </h1>
<div class="col"></div>
</div>

<? } else { 
    include "error_view.php";
 } ?>