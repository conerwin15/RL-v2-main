<iframe id="iframe_show" name="iframe_show" height="100vh" width="100%" title="Scorm Player" src="{{$link}}"></iframe>

<style>

   iframe {
     width: 100%;
     height: 100vh;
   }

</style>

<script>

window.onunload = function(){
  window.opener.location.reload();
};

</script>

