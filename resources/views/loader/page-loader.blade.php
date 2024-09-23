<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
#page-loader {
  display: none;
  position: fixed;
  z-index: 9999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.8);
}

#page-loader img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

</style>

<body>
<div id="page-loader">
            <img src="{{ asset('public/Loader.gif') }}" alt="Loading...">
        </div>
    
</body>
<script>
   $(window).on('load', function() {
                $('#page-loader').hide();
                });
                  $(document).ready(function(){
                $('#page-loader').show();
                  });
</script>
</html>
