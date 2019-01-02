$(function() {

  // // //左侧收缩菜单 ///////////////
  $(".item-a,.proimg").click(function(){
	 
	  var t1=$(this).parent('li').find(".tree-1");
	   //如果是图标的话
	  if($(this).attr('class')=='proimg'){
		 
		  var ji=$(this);
		  
	  }else{
		  
		  var ji= $(this).prev();
	  }
	  ////////////////////////
	  if(t1.css('display')=='none'){
		  
		 t1.fadeIn("fast");
		 
		 ji.attr('src','./theme/default/images/down.png');
		 
	  }else{
 
			  ji.attr('src','./theme/default/images/arrow_right.png');
			  
			  t1.fadeOut("fast"); 


	  
	  }
 
  });
  
  $(".item-a-1,.proimg-1").click(function(){
	  
	  
	   //如果是图标的话
	  if($(this).attr('class')=='proimg-1'){
		 
		  var a1=$(this).next().next('ul');
		  
	  }else{
		  
		  var a1=$(this).next('ul');
	  }
	  
	    
	  
	  if(a1.css('display')=='none'){
		  
		  	a1.fadeIn("fast");
		  	a1.prev().prev().attr('src','./theme/default/images/down.png');
		  }else{
			  
			  a1.fadeOut("fast");
			  a1.prev().prev().attr('src','./theme/default/images/arrow_right.png');
		  }
	  
  });
  //更换小三角和展开
  $(".proimg").click(function(){
	  
	  
	  
  });
  ///////////组织//////////////////
  
  
  $(".item-bg-a , .zuimg").click(function(){
	  
	 // var u=$(this).children('ul');
	  
	  if($(this).attr('class')=='zuimg'){
		  
		  var zui=$(this);
		  var u=$(this).next().next('ul');
	  }else{
		  
		  var u=$(this).next('ul');
		  var zui=$(this).prev();
		  //alert("");
	  }

	  
	  if(u.css('display')=='none'){
		  
		  	u.fadeIn("fast");
		  	 zui.attr('src','./theme/default/images/down.png');
		  }else{
			  
			  u.fadeOut("fast");
			  zui.attr('src','./theme/default/images/arrow_right.png');

		  }
	  //

  });
  
  /////////////////////////////////////////////

	$("#popClose").click(function(){
		  $('#pop').hide();
		});
  
  //////////左侧店铺列表点击显示具体的店铺////////////

$("body").on("click","#store_list li",function(){

		var xy=$(this).attr("xyid");
		var xyid=xy.split(",");  //分割字符串为数组
		var point = new BMap.Point(xyid[0],xyid[1]);
		var  marker=new BMap.Marker(point);
		map.clearOverlays();    //清除地图上所有覆盖物  
		map.addOverlay(marker);    //添加标注
		//marker.setAnimation(BMAP_ANIMATION_DROP); //跳动的动画 
		 map.centerAndZoom(point, 15);  //缩放等级
			
		 
});
/*
	$("#fan2").click(function(){
			  
		 $("#detail").hide();
		 $("#store_list").show();
		 $(this).hide();
		map.clearOverlays();    //清除地图上所有覆盖物  
		map.centerAndZoom("西安",5);  
		//显示所有的门店
		setTimeout(function(){
			showAll(all_store);
		}, 1000)
	});
*/	
//搜索框 默认值

	var search_default=$("#suggestId").val();
$("#suggestId").focus(function(){
	if($(this).val()==search_default){
		$(this).val("");
	}
	
});   
$("#suggestId").blur(function(){
	if($(this).val()==""){
		$(this).val(search_default);
	}
	
}); 
	
//so搜索
	$("#search_store").click(function(){
		
		var value=$("#suggestId").val();
		//alert(value);
		if(value==search_default){return false;}
		  $.ajax({
			  
		 		 type: "get",  //
		  		 url : "index.php", 
		  		 dataType:'json',// 
		  		 data:  'act=search_store&s='+encodeURI(value),   
		  		 success: function(data){
		  			 console.log(data);
		  			 var res=data.data;
		  			 if(res){
				  			var shtm='<ul>';
		  				 for(var i=0;i<res.length;i++){
		  			  		 if(res[i].image){var img=res[i].image;	}
		  			  		 else{var img="./images/img_none.png"; }	
		  					 
		 		  			shtm+='<li style="cursor: pointer;" bid="'+res[i].id+'" xyid="'+res[i].baidu_coordinate+'">';
				  			shtm+='<div style="width:60%;float:left;">';
				  			shtm+='<div class="sltal" style="font-weight:bold;">编号:<span >'+res[i].store_id+'</span></div>';
				  			shtm+='<div class="sltal">店长名:<span >'+res[i].name+'</span>     </div>';
				  			shtm+='<div class="sltal">电话:<span >'+res[i].phone+'</span></div>';
				  			shtm+='</div>';
				  			shtm+='<div class="srtal" style="float:right;width:40%">';
				  			shtm+='<img src="'+img+'"  style="width:100%"></img>';
				  			shtm+='</div>';
				
				  			shtm+='<p class="strop">';
				  			shtm+='地址:<span>'+res[i].address+'</span>';
				  			shtm+='</p></li>';
		  					 
		  					 
		  				 }
				  			shtm+='</ul>';
		  			 }
		  			 

		  			 	$(".menu_tab").hide();
		  			 	
		  			 	var ch='<div id="spage"  style="float:left"><p>总计 <span id="search_count">'+data.count+'</span> 个记录</p></div>';

		  			 	
		  			 	$("#tap4").find(".main-div").html(shtm+ch);
		  			 	$("#tap4").show();
		  			 	$("#pop").hide();
		  			 	$("#spage").show();
		  			
		  			 
		  		 }
		  
		        });
		
		
	});
	
	//绑定搜索结果 点击事件
	
	$("body").on("click","#tap4 li",function(){
		
		var bid=$(this).attr('bid');
		mkdetail(bid,'search');
		
		var xy=$(this).attr("xyid");
		var xyid=xy.split(",");  //分割字符串为数组
		var point = new BMap.Point(xyid[0],xyid[1]);
		var  marker=new BMap.Marker(point);
		map.addOverlay(marker);    //添加标注
		//marker.setAnimation(BMAP_ANIMATION_DROP); //跳动的动画 
		 map.centerAndZoom(point, 15);  //缩放等级
	});
	
	
	
	
$("#china").click(function(){
	map.clearOverlays();    //清除地图上所有覆盖物  
	map.centerAndZoom("西安",5);  
	//显示所有的门店
	setTimeout(function(){
		showAll(all_store);
	}, 1000)	
	
	$("#pop").show();  //显示左下角数据
	$('#z_store').html(count_store);
	$('#z_money').html(money_all);
	
	
});
	
});

/***
 * 返回事件
 */

function  fanhui(s){
	
	 if(typeof(s) !='undefined'){  //如果s存在 表示搜索
		$(".menu_tab").hide();
		 $("#tap4").show();
		 
	 }else{
		 
		 $("#store_list").show();
		 $("#detail").hide();
	 }

	 $(this).hide();
	map.clearOverlays();    //清除地图上所有覆盖物  
	map.centerAndZoom("西安",5);  
	//显示所有的门店
	setTimeout(function(){
		showAll(all_store);
	}, 1000);
	
}


/**
 * 根据id查询店铺详细信息
 * @param id
 */
function mkdetail(id,s){

	  $.ajax({
		  
	 		 type: "get",  //
	  		 url : "index.php", 
	  		 dataType:'json',// 
	  		 data:  'act=id_store&id='+id,   
	  		 success: function(res){
	  			 
	  			if(res){
	  				
	  				var data=res;
	  		 if(data.image){var img=data.image;	}
	  		 else{var img="./images/img_none.png"; }	
	  		 
			var detail='<div style="width:60%;float:left;">';
			detail+='<div class="dltal" style="font-weight:bold;">编号:<span id="store_id">'+data.store_id+'</span></div>';
			detail+='<div class="dltal">店长名:<span id="store_name">'+data.name+'</span>     </div>';
			detail+='<div class="dltal">电话:<span id="store_phone">'+data.phone+'</span></div>';
			detail+='</div><div class="drtal" style="float:right;width:40%">';
			detail+=' <img src="'+img+'" id="store_image" style="width:100%"></img> </div>';
  			detail+='<div  class="jiaoyi">	<p>';
  			detail+=' 地址:<span id="store_address">'+data.address+'</span></p>';
  			detail+=' <p style=""> <strong>所属信息</strong><br/>';
  			detail+=' 一级代理:<span id="level1_name">'+data.level1_name+'</span><br/>';
  			detail+=' 二级代理:<span id="level2_name">'+data.level2_name+'</span><br/>';
  			detail+=' 本部长:<span id="level3_name">'+data.level3_name+'</span><br/><br/>';
  			detail+=' <strong>销售信息</strong><br/>';
  			detail+=' 最初交易日: <span id="api_start_day">'+data.api_start_day+'</span><br/>';
  			detail+=' 最终交易日: <span id="api_end_day">'+data.api_end_day+'</span><br/>';
  			detail+=' 中交易次数:<span id="api_total_num">'+data.api_total_num+'</span> 次<br/>';
  			detail+='累计销售额: <span id="api_total_money">'+data.api_total_money+'</span></p>';
  			if(data.remark)detail+='<p><strong>备注信息</strong><br/><span id="remark">'+data.remark+'</span></p>';
  			detail+='</div>';
	  				
  				$("#detail").html(detail);
  				
  				$(".menu_tab").hide();
  				$("#tap1").show();
	  			}
	  			 
	  		 }
	  });
	  
	  
		 //左侧显示详细信息
		 $("#store_list").hide();
		 $("#detail").show();
		 $("#fan2").show();
		 
		 if(typeof(s) !='undefined'){   //如果s存在
			 
			 $("#fan2").attr("onclick","fanhui('s')");
		 }
	  
	
	 //return false;
	//console.log(data);


}
///门店翻页
function  toPage(n){
	

	//alert(page_count);
		var n=parseInt(n);
	//alert(n);
	//return false;
	  $.ajax({
		  
 		 type: "get",  //
  		 url : "index.php", 
  		 dataType:'json',// 
  		 data:  'act=store_page&page='+n,   
  		 success: function(res){
  			 var data=res;
  			 //console.log(res);
  			 
  			 var html='<ul style="width:100%">';
  			
  			 if(res){
  				 
  				var data_li=new Array();
  				
  				 for(var i=0;i<data.length;i++){
  					 
 
  	  				html+='<li style="cursor: pointer;" xyid="'+data[i].baidu_coordinate+'" onclick="mkdetail('+data[i].id+')" >';
  					html+='<div style="width:60%;float:left;">';
  				 	html+='<div class="sltal" style="font-weight:bold;">编号:<span>'+data[i].store_id+'</span></div>';
  					html+='<div class="sltal">店长名:<span>'+data[i].name+'</span>     </div>';
  					html+='<div class="sltal">电话:<span>'+data[i].phone+'</span></div>';
  					html+='</div>';
  					html+='<div class="srtal" style="float:right;width:40%">';
  					if(data[i].image){
  	  					html+=' <img src="'+data[i].image+'" style="width:100%">';

  					}else{
  	  					html+=' <img src="./images/img_none.png" style="width:100%">';

  					}
  					html+='</div>';
  				
  					html+='<p class="strop" style="">';
  					html+='地址:<span>'+data[i].address+'</span>';
  					html+='</p>	</li>';
  					 
  					
  				 }
  				  
					
				   if(n>=1 &&n<=page_count){
					   var t=n-1;
				if(n!=1){
					var p='<span><a href="javascript:void(0)" tid="toFirstPage" onclick="toPage(1);return false;">首页</a></span>';
					   
					p+='<span><a href="javascript:void(0)" onclick="toPage('+t+');return false;">'+t+'</a></span>';
				}else{
					
					var p="";
				}	  


					   	for(var k=0;k<3;k++){
					   		
					   		var q=n+k;
					   		var tim='';
					   		if(k==0){var tim=' class ="curPage" '} 
					  if(q<=page_count){ 		
					 p+='<span '+tim+' ><a href="javascript:void(0)" onclick="toPage('+q+');return false;">'+q+'</a></span>';
	
					  		}
					   	}
					   	
					 p+='<span><a href="javascript:void(0)" tid="toNextPage" onclick="toPage('+page_count+');return false;">末页</a></span>';   	
				   }
  				 
  				 $("#page").html(p);
  			 }
  			html+='</ul>';
  			$("#poi_store").html(html);
  			 
  		 }
	  
	  
	  });
	
	
}

/**
 * 点击组织列表， 显示对应的店铺信息 ，并且分页显示在详细信息页面
 */

function zuzhi_page(did,str,page){
	
	  if(!page){page=1;}
		  
	   $.ajax({
 
type: "get",  //
 url : "index.php", 
 dataType:'json',// 
 data:  'act=zuzhi_lable&did='+did+"&"+str+"&page="+page+"&fenye=1",   
 success: function(los){
	 
	 //分页结果集赋给详细列表内容
	 var mdata=los.data;
		 var html='<ul style="width:100%">';
		 //console.log(mdata);
		 for(var i=0;i<mdata.length;i++){
			// console.log(mdata[i].baidu_coordinate);
	
			html+='<li style="cursor: pointer;" xyid="'+mdata[i].baidu_coordinate+'" onclick="mkdetail('+mdata[i].id+')" >';
			html+='<div style="width:60%;float:left;">';
		 	html+='<div class="sltal" style="font-weight:bold;">编号:<span>'+mdata[i].store_id+'</span></div>';
			html+='<div class="sltal">店长名:<span>'+mdata[i].name+'</span>     </div>';
			html+='<div class="sltal">电话:<span>'+mdata[i].phone+'</span></div>';
			html+='</div>';
			html+='<div class="srtal" style="float:right;width:40%">';
			if(mdata[i].image){
				html+=' <img src="'+mdata[i].image+'" style="width:100%">';

			}else{
				html+=' <img src="./images/img_none.png" style="width:100%">';

			}
			html+='</div>';
		
			html+='<p class="strop" style="">';
			html+='地址:<span>'+mdata[i].address+'</span>';
			html+='</p>	</li>';
			 
			
		 }
		html+='</ul>';
		$("#poi_store").html(html);
		
		
		//分页页码显示
		//alert(page);
		var n=page;  //当前页码
		var zuzhi_pagecount=los.page_count;   //总页面数
		var zuzhi_count=los.record_count;     //总数
		
		 console.log(los);
	if (n >= 1 && n <= zuzhi_pagecount) {
   var t = n - 1;
   if (n != 1) {
       var p = '<span><a href="javascript:void(0)" tid="toFirstPage" onclick="zuzhi_page(1);return false;">首页</a></span>';

       p += '<span><a href="javascript:void(0)" onclick="zuzhi_page(' + t + ');return false;">' + t + '</a></span>';
   } else {

       var p = " ";
   }

   for (var k = 0; k < 3; k++) {

       var q = n + k;
       var tim = '';
       if (k == 0) {
           var tim = ' class ="curPage" '
       }
       if (q <= page_count) {
           p += '<span ' + tim + ' ><a href="javascript:void(0)" onclick="toPage(' + q + ');return false;">' + q + '</a></span>';

       }
   }

   p += '<span><a href="javascript:void(0)" tid="toNextPage" onclick="toPage(' + page_count + ');return false;">末页</a></span>';
}
	  /**
	   * 	 los.store_count //每页显示条数
	   * 如果总数小于15的话 
	   */
	  
   //15条 一页
	if(zuzhi_count<15){
		 var p = " ";
	}
	
	
	$('#ppagel').html("总计 "+zuzhi_count+" 个记录，共 "+zuzhi_pagecount+"页");
	$("#page").html(p);
		
	 
	 
	 
 }
	  });
	
	
	
}


