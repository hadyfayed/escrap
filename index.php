<?php

require 'vendor/autoload.php';

use EcomScrap\Client;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = "nothing found";
    if (isset($_POST['url']) && !empty($_POST['url'])){
        $client = new Client($_POST['url']);
        $data = $client->getProductData();
    }
    print json_encode($data);
}
else{
?>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Scraper</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.17/css/uikit.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/black/pace-theme-flash.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script data-pace-options='{ "ajax": true }' src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.17/js/uikit.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.17/js/uikit-icons.min.js"></script>
    </head>
    <body>
    <div class="uk-section uk-section-primary uk-preserve-color">
        <div class="uk-container">
            <form class="uk-form-stacked" id="product" action="">
                <div class="uk-margin">
                    <div class="uk-form-controls">
                        <input type="url" class="uk-input uk-form-large" placeholder="Product Url" id="url" name="url"/>
                    </div>
                </div>
                <div class="uk-margin">
                    <div class="uk-form-controls">
                        <button class="uk-button uk-button-default" type="submit">Submit</button>
                    </div>
                </div>
            </form>
            <div class="uk-card uk-card-default uk-grid-collapse uk-child-width-1-2@s uk-margin" uk-grid>
                <div class="uk-card-media-left uk-cover-container">
                    <img class="product-image " alt="" uk-cover>
                </div>
                <div>
                    <div class="uk-card-body">
                        <h3 class="uk-card-title product-name"></h3>
                        <div>
                            <var class="uk-text-muted">
                                <span class="product-price"></span>
                            </var>
                        </div>
                        <div>
                            <var class="uk-text-danger uk-text-large uk-text-bold">
                                <span class="product-sale-price"></span>
                            </var>
                        </div>
                        <dl class="uk-description-list">
                            <dt>Description</dt>
                            <dd><p class="product-description"></p></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        $(document).ready(function ($) {
            $('#product').on('submit',function (e) {
                e.preventDefault();
                $.ajax({
                    url: this.action,
                    method: "POST",
                    data:{url: $("#url").val()}
                }).done(function(res) {
                    var data = $.parseJSON(res);
                    $('.product-name').html(data.title);
                    $('.product-price').html(data.originalPrice);
                    $('.product-sale-price').html(data.salePrice);
                    $('.product-image').attr('src',data.mainImage);
                    $('.product-description').html(data.description);
                    console.log(data);
                });
            });
        });
    </script>
    </body>
    </html>
<?php
}
