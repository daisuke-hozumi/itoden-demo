/*---------------------------
 ※ SP時headerのみ追従状態のときに、PCにウィンドウリサイズすると、SPヘッダー追従分の
    margin-top分PCのpivotが下にズレて、カクつくので注意
 追従コンテンツがある場合、
 追従コンテンツのIDにfollow_contents、
  追従コンテンツの前のコンテンツにfollow_contents_beforeを指定。
 また、beforeの直後に追従コンテンツが無いと、開始位置が図れないため、
  beforeとの余白はmarginではなくpaddingを使用する。
---------------------------*/
$(function(){
	'use strict';
	
    var intBarHeight = 0;
    
    //---------------------------------------
	// スムーズスクロール処理
	//---------------------------------------
	$('a[href^="#"]').click(function() {
	// スクロールの速度
	var speed = 400; // ミリ秒
	// アンカーの値取得
	var href= $(this).attr("href");
	// 移動先を取得
	var target = $(href == "#" || href == "" ? 'html' : href);
	// 移動先を数値で取得
	var position = target.offset().top;
	//------------------
	// メニューバー分をマイナス
	//------------------
	// PC・スマホそれぞれ、追跡するバー分を引く
	position -= intBarHeight;
		
	// 指定位置までスムーズスクロール
	$('body,html').animate(	
								{
									scrollTop:position
								}, 
								{
									duration: speed ,
									easing : 'swing' ,
									complete: function() {
									}
								}
							);
      return false;
   });
});