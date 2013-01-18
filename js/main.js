var color1 = get_color();
var color2 = "#ffffff";
$(function(){
  $("a").css("color",color1);
  $("a").hover(
    function () {
      $(this).css("color",color2);
      $(this).css("background-color",color1);
    }, 
    function () {
      $(this).css("color",color1);
      $(this).css("background-color",color2);
    }
    );
})

function get_color(){
  var R = get_hex();
  var G = get_hex();
  var B = get_hex();
  var RGB = "#" + R + G + B;
  return RGB;
}

function get_hex(){
  var t = parseInt(Math.random() * 255);
  var s = t.toString(16);

  // Why: 十六进制不足两位数的前面补零，如"F"=>"0F"
  //      否则生成的颜色值不足六位时会出错
  // 情景：鼠标从链接上移除后，链接颜色错误，不再显示
  if(s.length <= 1){
    s = "0" + s;
  }
  return s;
}
