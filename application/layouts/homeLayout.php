<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Outirl</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="<?=Sally::get('static')?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=Sally::get('static')?>css/template.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=Sally::get('static')?>leaflet/leaflet.css"/>
    <script src="<?=Sally::get('static')?>leaflet/leaflet.js"></script>
  </head>
  <body>
    <div id="main-menu">
      Menu
    </div>
    <div id="wrapper">
      <div id="header">
        <a class="main-menu-action">hide menu</a> Header
      </div>
      <div id="content">
        <?=$content?>
      </div>
    </div>
    <script src="<?=Sally::get('static')?>js/jquery.js"></script>
    <script>
      $(document).ready(function() {
        $('.main-menu-action').click(function() {
          if ($('body').hasClass('show-menu')) {
            $('body').removeClass('show-menu');
          } else {
            $('body').addClass('show-menu');
            window.scrollTo(0, 0);
          }
        });

        if ($(window).width() > 768) {
          $('body').addClass('show-menu');
        }
      });
    </script>
  </body>
</html>