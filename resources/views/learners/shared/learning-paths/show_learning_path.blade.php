<div class="row">
  <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="form-group">
     <iframe id="iframe_show" name="iframe_show" height="200px" width="300px" title="Scorm Player"></iframe>
   </div>
   @if(count($scormItems) > 1)
     <button onclick="goPrev()">{{ __('lang.previous') }}</button>
     <button onClick="goNext()">{{ __('lang.next') }}</button>
   @endif
  </div>
</div>
<br>

<script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/init.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/constants.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/jsonFormatter.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/baseAPI.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scorm/scormAPI.js') }}"></script>
<script type="text/javascript">
    var resource = {{$resourceid}};
    var isMobile = {{$isMobile ?? 'false'}};
    var routeSlug = '{{$routeSlug}}';
    let items = @json($scormItems);
    var progress = @json($progress);
   

    let currentIndex = 0;
    let totalItems = {{count($scormItems)}};

    window.API.on("LMSSetValue", function(element, value) {
     
      window.trackProgress();
    });

    $(document).ready(function() {
        if(progress) {
          window.API.loadFromJSON(progress.cmi_data);
        } else {
          window.API.cmi.core.student_id = {{ Auth::user()->id }};
          window.API.cmi.core.student_name = '{{ Auth::user()->name }}';
        }
        setIframeURL(0);
    });

    function setIframeURL(index) {
      currentIndex = index;
      var path = "{{$scormPackage->package_saved_path}}";
      let basePath = "{{asset('/storage')}}" + path;      
      $("#iframe_show").attr('src', basePath + '/' + items[index].href + (items[index].parameters ? items[index].parameters : ''));
    }

    function goPrev() {
      if(currentIndex > 0) {
        setIframeURL(currentIndex - 1);
      }
    }

    function goNext() {
      if(currentIndex < totalItems) {
        setIframeURL(currentIndex + 1);
      }
    }

    function trackProgress() {
      const url = isMobile ? `/api/scorm/${routeSlug}/progress/track/${resource}` :  `/${routeSlug}/progress/track/${resource}`;
      let llt = new Date().getTime();
      if(isMobile) {
        var urlParams = new URLSearchParams(window.location.search);
        llt = urlParams.get('llt')
      }
      $.ajax({
        type: 'post',
        url: url,
        beforeSend: function(request) {
          request.setRequestHeader("llt", llt);
        },
        data: { cmi : { suspend_data: window.API.cmi.suspend_data, core : window.API.cmi.core.toJSON(), interactions:  window.API.cmi.interactions.toJSON() }, _token: "{{ csrf_token() }}" },
        success: function (data) {
         
        },
        error: function (data) {
        }
      });
    }

    window.onunload = function(){
      window.opener.location.reload();
    };

</script>

<style>

   iframe {
     width: 100%;
     height: 100vh;
   }

</style>

