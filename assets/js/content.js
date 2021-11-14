var wishbtn = document.getElementById("wish_btn");
wishbtn.addEventListener("click", function() {
var post_id =  wishbtn.value;
ajaxRequest('wish','<div class="progress"><div class="indeterminate"></div></div>','wish','id',post_id,'yes');
});
var broken = document.getElementById("broken_report");
broken.addEventListener("click", function() {
var post_id =  wishbtn.value;
ajaxRequest('broken','<div class="progress"><div class="indeterminate"></div></div>','broken','id',post_id);
});
window.addEventListener('load', function(){
    if(document.getElementById("stream")) {
    var stream = document.getElementById("stream");
    var src = stream.getAttribute('data-src');
    stream.setAttribute('src', src);
    document.getElementById("loader").innerHTML = '';
    }
 }, false);