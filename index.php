<script src = 'https://code.jquery.com/jquery-3.1.1.min.js'></script>
<script type = 'text/javascript'>

var urls = Array();
var images = Array();
var hashTab = {};
var margin = 10;

var imageHeight = 200;
var avgWidth = 0;//Средняя длина элемента
window.onload = function(){
	$.ajax({
		url: 'backend.php',
		data: {func: 'init'},
		type: 'POST',
		success: function (data) {
			addImages(data);
		}
	});
	$(window).resize(function() {
	});
}


function sendFile() {
	var fd = new FormData;
	fd.append('uploadfile', $('#uploadfile').prop('files')[0]);
	$('#ajaxLoader').show();
	$('#forma').hide();
	$.ajax({
		url: 'backend.php',
		data: fd,
		type: 'POST',
		processData: false,
		contentType: false,
		success: function (data) {
			addImages(data);
			$('#ajaxLoader').hide();
			$('#forma').show();
		}
	});
function addImages(data){
	for (var i in data){
		if (!hashTab.hasOwnProperty(data[i].url)){
			row.obj = createDiv().append(createImage(data[i].url));
			row.width = row.origWidth * imageHeight / row.origHeight;
			row.height = imageHeight;
			$('body').append(row.obj);//Вначале прорисовываем, чтобы outerWidth схватился
			hashTab[data[i].url] = 0;
		}
	}
}

function calculateLines(){//Рассчитать сколько строк и сколько в них элементов
	var lines = Array();
	var lineWidth = 0;
	var totalWidth = 0;//Полная длина всех элементов
	$(urls).each(function(ind, url){//Вычислим строки
		if (lineWidth + (margin+url.width/2) > $('body').width()){//Элемент (его половина) не влезает в текущую строку - необходимо начать новую
			lines[ind-1] = lineWidth
			lineWidth = url.width+margin*2;
		}else{//Элемент влезает в текущую строку - продолжаем ее наращивать
			lineWidth += url.width+margin*2;
		}
		if (ind == urls.length - 1){//Последний элемент
			lines[ind] = lineWidth;
		}
		totalWidth += url.width+margin*2;
	});
	avgWidth = totalWidth/urls.length;//Средний размер элемента, понадобится для последнего элемента
	return {lines: lines, avgWidth: avgWidth};
}
function changeWidth(){//Пересчет размеров контейнеров и рисунков
	var start = 0;
	for (var i in data.lines){//Пройдем по каждой строке
		var bodyWidth = $('body').width();
		if (i*1+1 == data.lines.length){//Если последняя строка
		var imagesWidth = data.lines[i] - margin*2*i;//Размер рисунков данной строки с вычетом отступов
		var kImg = Math.max(1, (imagesWidth + space) / imagesWidth);//Во сколько раз надо увеличить рисунок, чтобы заполнить оставшееся пространство, уменьшать нельзя, потому что высота зафиксирована
		var kDiv = bodyWidth/data.lines[i]; //Во сколько раз модифицировать длину контейнера

		var lineWidth = 0;
			lineWidth += w+2*margin;//Вычислим все новые размеры контейнеров
			urls[i2].obj.children().height(urls[i2].height * kImg);//Назначаем размер рисунка
			if (i2 == i){//Правая сторона галереи
				var h = (urls[i2].height / urls[i2].width) * (bodyWidth - lineWidth);//Определяем на сколько надо поправить правые рисунки, чтобы прямо пиксель в пиксель
				urls[i2].obj.children().height(Math.max(imageHeight, urls[i2].height * kImg + Math.ceil(h)));//Назначим размер рисунка
			}
			urls[i2].obj.width(w);//Назначаем размер контейнера
	}
function createImage(src){
		src: src,
		height: imageHeight+'px'
	});
	obj.on('load', function(){
		hashTab[src] = 1;
		if (isLoaded()) changeWidth();
	return obj;
function isLoaded(){//Проверим все ли рисунки прорисовались
	for (var i in hashTab){
	}

	return true
function createDiv(/*width*/){
	return $('<div />', {
		class: 'onlyOneClass',
		height: imageHeight
	});



</script>
<style>
.onlyOneClass{
	display: inline-block;
	margin: 10px;
	overflow: hidden;
</style>
<div>
<h1>Галерея</h1>
<img src = 'ajax-loader.gif' id = 'ajaxLoader' style = 'display:none;' />
<form id = 'forma' enctype = 'multipart/form-data'>
<input type = 'file' id = 'uploadfile' />
<input type = 'button' onclick = 'sendFile()' value = ' Загрузить файл '/>

</form>

</div>