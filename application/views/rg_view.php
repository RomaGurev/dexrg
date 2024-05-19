<? if ($_GET["rgnsk"] == "clown") { ?>
    <main class="mt-4 mb-4">
        <div class="container">
            <div class="d-flex">
                <div class="p-4 rounded-3 border shadow me-2 col">
                    <h1> <? echo base64_decode("Um9tYW4gR3VyZXY="); ?> </h1>
                    <img src="/images/clown.png" class="d-block" width="700px"></img>
                    <h3> <? echo base64_decode("NzAl"); ?> </h3>
                    <p> <? echo base64_decode("LSDQv9GA0L7QtdC60YLQuNGA0L7QstCw0L3QuNC1INGB0LjRgdGC0LXQvNGLPGJyPi0g0YDQsNC30YDQsNCx0L7RgtC60LAg0LTQstC40LbQutCwPGJyPi0g0YDQsNC30YDQsNCx0L7RgtC60LAg0YjQsNCx0LvQvtC90LjQt9Cw0YLQvtGA0LAg0Lgg0YHQuNGB0YLQtdC80Ysg0L7RgtC+0LHRgNCw0LbQtdC90LjRjzxicj4tINC00LjQt9Cw0LnQvQo="); ?></p>
                </div>
                <div class="p-4 rounded-3 border shadow me-2 col">
                    <h1> <? echo base64_decode("QW50b24gRHVka28="); ?> </h1>
                    <img src="/images/clown.png" class="d-block" width="200px"></img>
                    <h3> <? echo base64_decode("MjAl"); ?> </h3>
                    <p><? echo base64_decode("LSDRgdCx0L7RgCDQuNC90YTQvtGA0LzQsNGG0LjQuDxicj4tINGA0LDQsdC+0YLQsCDQuCDQvtCx0YPRh9C10L3QuNC1INGB0L7RgtGA0YPQtNC90LjQutC+0LI8YnI+LSDRgtC10YHRgtC40YDQvtCy0LDQvdC40LUK"); ?></p>
                </div>
                <div class="p-4 rounded-3 border shadow col">
                    <h1> <? echo base64_decode("RWdvciBFcGlmYW50c2V2"); ?> </h1>
                    <img src="/images/clown.png" class="d-block" width="100px"></img>
                    <h3> <? echo base64_decode("MTAl"); ?> </h3>
                    <p><? echo base64_decode("LSDQvdCw0YHRgtGA0L7QudC60LAg0YHRgtCw0YLQuNGB0YLQuNC60Lg8YnI+LSDQsNC70LPQvtGA0LjRgtC8INC/0LXRgNC10L3QvtGB0LAg0YHRgtCw0YDRi9GFINCx0LDQtzxicj4tINC+0YLQu9Cw0LTQutCw"); ?></p>
                </div>
            </div>
        </div>
    </main>
    <style>
        h1, h3 {
            text-align: center;
        }
        img {
            margin: 0 auto;
        }
    </style>

    <? } else {
    include "error_view.php";
} ?>