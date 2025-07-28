<script src="/assets/js/headbreaker.js"></script>
<div id="my-canvas">
</div>
<img id="image-canvas-overlay" src="https://cdn.britannica.com/86/170586-050-AB7FEFAE/Taj-Mahal-Agra-India.jpg" class="">
<script>
  // ...and a script with the following code:
  let dali = new Image();
  let canvas;
  dali.src = 'https://cdn.britannica.com/86/170586-050-AB7FEFAE/Taj-Mahal-Agra-India.jpg';
  dali.onload = () => {
	var w = window.innerWidth;
	var h = window.innerHeight;
	
	['resize', 'DOMContentLoaded'].forEach((event) => {
	  window.addEventListener(event, () => {
		var container = document.getElementById('my-canvas');
		canvas.resize(container.offsetWidth, container.scrollHeight);
		canvas.scale(container.offsetWidth / initialWidth);
		canvas.redraw();
    });
	
    const canvas = new headbreaker.Canvas('my-canvas', {
		width: w, height: h,
    	pieceSize: 200, proximity: 40,
    	borderFill: 20, strokeWidth: 1,
    	lineSoftness: 0.36, image: dali,
		preventOffstageDrag: true
    });
    canvas.adjustImagesToPuzzleHeight();
	canvas.autogenerate({
		horizontalPiecesCount: 3,
		verticalPiecesCount: 3
	});
	canvas.shuffle(0.7);
    canvas.draw();
	canvas.autoconnect();
	canvas.attachSolvedValidator();
	canvas.onValid(() => {
		setTimeout(() => {
		  document.getElementById('image-canvas-overlay').setAttribute("class", "active");
		}, 1500);
	})
	
	  
	});
	
  }
 
</script>

<style>

#image-canvas-overlay {
	position: absolute;
    left: 0;
    top: 0;
    margin: 0;
    padding: 0;
    opacity: 0;
    pointer-events: none;
    transition: opacity 1s ease-in-out;
}

#image-canvas-overlay.active {
	opacity: 1;
}

</style>