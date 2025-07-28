
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{{ __('lang.certificate') }}</title>
  </head>
  <style type="text/css">
    .lefttext{
      font-size: 18px;
      font-weight: bold;
      width: 20%;  
    }
    .righttext{
      font-size: 14px;
      font-weight: bold;
      width: 80%; 
    }
  </style>
    <body>
    {!! html_entity_decode($certificate->content) !!}
  </body>
</html>