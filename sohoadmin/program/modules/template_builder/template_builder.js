	
<!--
function SV2_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=SV2_findObj(n,d.layers[i].document); return x;
}
function SV2_showHideLayers() { //v3.0
  var i,p,v,obj,args=SV2_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=SV2_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
function SV2_popupMsg(msg) { //v1.0
  alert(msg);
}
function SV2_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function navto(where) {
	window.location = where+"?<?=SID?>";
}

SV2_showHideLayers('addCartMenu?header','','hide');
SV2_showHideLayers('blankLayer?header','','hide');
SV2_showHideLayers('linkLayer?header','','hide');
SV2_showHideLayers('newsletterLayer?header','','hide');
SV2_showHideLayers('cartMenu?header','','show');
SV2_showHideLayers('menuLayer?header','','hide');
SV2_showHideLayers('editCartMenu?header','','hide');

//-->
	
	
	var ie=document.all;
	var nn6=document.getElementById&&!document.all;

	function openInfoDialog(daTstuff) {
		Dialog.info(daTstuff, {windowParameters: {className: "alert_lite",width:380, height:320}, showProgress: false});
	}
	
	function noBorder(daCurrent) {
		var templateAreas = new Array("head", "col_left", "col_right", "footer", "content")
		var areasLen = templateAreas.length
		for ( b = 0; b < areasLen; b++ ) {
			if(templateAreas[b] != daCurrent){
				$(templateAreas[b]).style.border= '0px solid red'
			}
		}
	}
		
	
	function showOptionsHead() {
		noBorder('head')
		$('curArea').value = 'head'
		$('styleOps').style.display= 'block'
		$('head').style.border= '1px solid red'
		var curBakCol = $('head').style.backgroundColor
		var curBakImg = $('head').style.backgroundImage
		if(curBakCol != ''){
			$("pickerSwatch").style.backgroundColor = curBakCol;
			//alert(curBakCol)
		}
	}
	
	function showOptionsColLeft() {
		noBorder('col_left')
		$('curArea').value = 'col_left'
		$('styleOps').style.display= 'block'
		$('col_left').style.border= '1px solid red'
		var curBakCol = $('col_left').style.backgroundColor
		var curBakImg = $('col_left').style.backgroundImage
		if(curBakCol != ''){
			$("pickerSwatch").style.backgroundColor = curBakCol;
			//alert(curBakCol)
		}
	}
	
	function showOptionsColRight() {
		noBorder('col_right')
		$('curArea').value = 'col_right'
		$('styleOps').style.display= 'block'
		$('col_right').style.border= '1px solid red'
		var curBakCol = $('col_right').style.backgroundColor
		var curBakImg = $('col_right').style.backgroundImage
		if(curBakCol != ''){
			$("pickerSwatch").style.backgroundColor = curBakCol;
			//alert(curBakCol)
		}
	}
	
	function showOptionsFooter() {
		noBorder('footer')
		$('curArea').value = 'footer'
		$('styleOps').style.display= 'block'
		$('footer').style.border= '1px solid red'
		var curBakCol = $('footer').style.backgroundColor
		var curBak = $('footer').bgColor
		//alert(curBak)
		var curBakImg = $('footer').style.backgroundImage
		if(curBakCol != ''){
			$("pickerSwatch").style.backgroundColor = curBakCol;
			//alert(curBakCol)
		}
	}
	
	function showOptionsContent() {
		noBorder('content')
		$('curArea').value = 'content'
		$('styleOps').style.display= 'block'
		$('content').style.border= '1px solid red'
		var curBakCol = $('content').style.backgroundColor
		var curBak = $('content').bgColor
		//alert(curBak)
		var curBakImg = $('content').style.backgroundImage
		if(curBakCol != ''){
			$("pickerSwatch").style.backgroundColor = curBakCol;
			//alert(curBakCol)
		}
	}
	
	
	
	
	
	var hue;
	var picker;
	//var gLogger;
	var dd1, dd2;
	var r, g, b;

	function init() {
		if (typeof(ygLogger) != "undefined")
			ygLogger.init(document.getElementById("logDiv"));
		pickerInit();
		ddcolorposter.fillcolorbox("colorfield1", "colorbox1") //PREFILL "colorbox1" with hex value from "colorfield1"
		ddcolorposter.fillcolorbox("colorfield2", "colorbox2") //PREFILL "colorbox1" with hex value from "colorfield1"
    }

    // Picker ---------------------------------------------------------

    function pickerInit() {
		hue = YAHOO.widget.Slider.getVertSlider("hueBg", "hueThumb", 0, 180);
		hue.onChange = function(newVal) { hueUpdate(newVal); };

		picker = YAHOO.widget.Slider.getSliderRegion("pickerDiv", "selector",
				0, 180, 0, 180);
		picker.onChange = function(newX, newY) { pickerUpdate(newX, newY); };

		hueUpdate();

		dd1 = new YAHOO.util.DD("pickerPanel");
		dd1.setHandleElId("pickerHandle");
		dd1.endDrag = function(e) {
			// picker.thumb.resetConstraints();
			// hue.thumb.resetConstraints();
        };
	}

	executeonload(init);

	function pickerUpdate(newX, newY) {
		//alert(arguments.length)
		pickerSwatchUpdate();
	}


	function hueUpdate(newVal) {

		var h = (180 - hue.getValue()) / 180;
		if (h == 1) { h = 0; }

		var a = YAHOO.util.Color.hsv2rgb( h, 1, 1);

		document.getElementById("pickerDiv").style.backgroundColor =
			"rgb(" + a[0] + ", " + a[1] + ", " + a[2] + ")";

		pickerSwatchUpdate();
	}

	function pickerSwatchUpdate() {
		var h = (180 - hue.getValue());
		if (h == 180) { h = 0; }
		
		document.getElementById("pickerhval").value = (h*2);

		h = h / 180;

		var s = picker.getXValue() / 180;
		document.getElementById("pickersval").value = Math.round(s * 100);

		var v = (180 - picker.getYValue()) / 180;
		document.getElementById("pickervval").value = Math.round(v * 100);

		var a = YAHOO.util.Color.hsv2rgb( h, s, v );

		document.getElementById("pickerSwatch").style.backgroundColor =
			"rgb(" + a[0] + ", " + a[1] + ", " + a[2] + ")";
		//alert("rgb(" + a[0] + ", " + a[1] + ", " + a[2] + ")");

		document.getElementById("pickerrval").value = a[0];
		document.getElementById("pickergval").value = a[1];
		document.getElementById("pickerbval").value = a[2];
		var hexvalue = document.getElementById("pickerhexval").value =
			YAHOO.util.Color.rgb2hex(a[0], a[1], a[2]);
			ddcolorposter.initialize(a[0], a[1], a[2], hexvalue)
			//alert(hexvalue);
			if($('tab3-content').style.display == 'block'){
				var daCurArea = $('curArea').value
				updateDisplay(daCurArea, hexvalue)
				$('pickerInput').value=hexvalue
			}
	}
	
	