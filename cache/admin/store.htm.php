<?php /* Smarty version 2.6.26, created on 2018-12-27 10:00:57
         compiled from store.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $this->_tpl_vars['lang']['home']; ?>
<?php if ($this->_tpl_vars['ur_here']): ?> - <?php echo $this->_tpl_vars['ur_here']; ?>
 <?php endif; ?></title>
<meta name="Copyright" content="Douco Design." />
<link href="templates/public.css" rel="stylesheet" type="text/css">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "javascript.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<script type="text/javascript" src="images/jquery.autotextarea.js"></script>
<script>
var lang_city="<?php echo $this->_tpl_vars['lang']['city_select_empty']; ?>
";
var lang_district="<?php echo $this->_tpl_vars['lang']['district_select_empty']; ?>
";

var dist='<?php echo $this->_tpl_vars['dist']; ?>
';
<?php echo '
$(function(){
	
	$(".province,.city").change(function(){
		
		var pid=$(this).val();
		
		var option="";
		var por=$(this).attr(\'class\');
	
		if(por==\'province\'){
			var lang=lang_city;
		}if(por==\'city\'){
			
			var lang=lang_district;
		}
		
		  $.ajax({
			  
		 		 type: "get",  //
		  		 url : "store.php", 
		  		 dataType:\'json\',// 
		  		 data:  \'rec=city&pid=\'+pid,   
		  		 success: function(res){
		  			 
		  			// console.log(res);
		  			 if(res){
		  				 
		  				option+=\'<option value="0">\'+lang+\'</option>\'; 
		  				
		  				for(var i=0;i<res.length;i++){

		  					option+="<option value=\'"+res[i].region_id+"\'>"+res[i].region_name+"</option>";

		  				 }
		  				
		  				if(por==\'province\'){
		  					$(".city").html(option);
		  					$(".district").html(\'<option value="0">\'+lang_district+\'</option>\');
		  				}
		  				if(por==\'city\'){
		  					
		  					$(".district").html(option);
		  				}
		  				 
		  			 }
		  		 }
			  });
		
	});
		
	//return false;

	$(".exc_down").click(function(){
		
		
		location.href="images/example.xlsx";
		
	});
	
	
	//组织选项联动
	 //#level1_select,#level2_select,#level3_select
	$(".level_select").change(function(){
		
		var zid=$(this).val();

      	var eqm=$(this).attr("eqm");
      	//下一个
        var next=$(".level_select").eq(Number(eqm)+1);

	//return false;
		$.ajax({
			 
			type: "get",  //
			 url : "store.php", 
			 dataType:\'json\',// 
			 data:  \'rec=get_level&zid=\'+zid,   
			 success: function(res){
				 
				if(res){
					 var option=\'<option value="0">请选择</option>\';
					 
					 for(var i=0;i<res.length;i++){
						 
						 
						 option+=\'<option value="\'+res[i].code+\'">\'+res[i].code+\'-\'+res[i].zname+\'</option>\';
					 }
					
					
					 next.html(option);
					 
					 $(".level_select").eq(Number(eqm)+2).html(\'<option value="0">请选择</option>\');
					 $(".level_select").eq(Number(eqm)+3).html(\'<option value="0">请选择</option>\');
					 $(".level_select").eq(Number(eqm)+4).html(\'<option value="0">请选择</option>\');
					
				}
			 }
		});
		
		/////////////
		
	});
	
	///////
    //init(xy);

	
});
'; ?>

</script>

<script type="text/javascript">
<?php echo '
      //图片上传预览    IE是用了滤镜。
        function previewImage(file)
        {
          var MAXWIDTH  = 155; 
          var MAXHEIGHT = 127;
          var div = document.getElementById(\'preview\');
          if (file.files && file.files[0])
          {
              div.innerHTML =\'<img id=imghead>\';
              var img = document.getElementById(\'imghead\');
              img.onload = function(){
                var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
                img.width  =  rect.width;
                img.height =  rect.height;
//                 img.style.marginLeft = rect.left+\'px\';
                img.style.marginTop = rect.top+\'px\';
              }
              var reader = new FileReader();
              reader.onload = function(evt){img.src = evt.target.result;}
              reader.readAsDataURL(file.files[0]);
          }
          else //兼容IE
          {
            var sFilter=\'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="\';
            file.select();
            var src = document.selection.createRange().text;
            div.innerHTML = \'<img id=imghead>\';
            var img = document.getElementById(\'imghead\');
            img.filters.item(\'DXImageTransform.Microsoft.AlphaImageLoader\').src = src;
            var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
            status =(\'rect:\'+rect.top+\',\'+rect.left+\',\'+rect.width+\',\'+rect.height);
            div.innerHTML = "<div id=divhead style=\'width:"+rect.width+"px;height:"+rect.height+"px;margin-top:"+rect.top+"px;"+sFilter+src+"\\"\'></div>";
          }
        }
        function clacImgZoomParam( maxWidth, maxHeight, width, height ){
            var param = {top:0, left:0, width:width, height:height};
            if( width>maxWidth || height>maxHeight )
            {
                rateWidth = width / maxWidth;
                rateHeight = height / maxHeight;
                 
                if( rateWidth > rateHeight )
                {
                    param.width =  maxWidth;
                    param.height = Math.round(height / rateWidth);
                }else
                {
                    param.width = Math.round(width / rateHeight);
                    param.height = maxHeight;
                }
            }
            param.left = Math.round((maxWidth - param.width) / 2);
            param.top = Math.round((maxHeight - param.height) / 2);
            return param;
        }
 '; ?>
       
</script>
</head>
<body>
<div id="dcWrap">
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
 <div id="dcLeft"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></div>
 <div id="dcMain">
   <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "ur_here.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
   <div class="mainBox" style="<?php echo $this->_tpl_vars['workspace']['height']; ?>
">
    <?php if ($this->_tpl_vars['rec'] == 'default'): ?>
    <h3><a href="<?php echo $this->_tpl_vars['action_link']['href']; ?>
" class="actionBtn add"><?php echo $this->_tpl_vars['action_link']['text']; ?>
</a>
    <a href="zbs.php" class="actionBtn" style="margin-right: 20px;display:none;">批量查坐标</a>
    <?php echo $this->_tpl_vars['ur_here']; ?>
</h3>
    <div class="filter">
    <form action="store.php" method="POST">
<select name="changwu" class="lsvg" id="changwu">
<option value="0">所有常务</option>
<?php $_from = $this->_tpl_vars['changwu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['chang']):
?>
<option value="<?php echo $this->_tpl_vars['chang']['id']; ?>
" <?php if ($_REQUEST['changwu'] == $this->_tpl_vars['chang']['id']): ?> selected="selected" <?php endif; ?> ><?php echo $this->_tpl_vars['chang']['username']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select>

<select name="lishi" class="lsvg" id="lishi" >
<option value="0">所有理事</option>
<?php $_from = $this->_tpl_vars['lishi']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['li']):
?>
<option value="<?php echo $this->_tpl_vars['li']['id']; ?>
" <?php if ($_REQUEST['lishi'] == $this->_tpl_vars['li']['id']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['li']['username']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>

</select>

<select name="shengdai" class="lsvg" id="shengdai">
<option value="0">所有省代</option>
<?php $_from = $this->_tpl_vars['shengdai']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sheng']):
?>
<option value="<?php echo $this->_tpl_vars['sheng']['id']; ?>
" <?php if ($_REQUEST['shengdai'] == $this->_tpl_vars['sheng']['id']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['sheng']['username']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select>

<select name="shidai" class="lsvg"  id="shidai" >
<option value="0">所有市代</option>
<?php $_from = $this->_tpl_vars['shidai']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['shi']):
?>
<option value="<?php echo $this->_tpl_vars['shi']['id']; ?>
" <?php if ($_REQUEST['shidai'] == $this->_tpl_vars['shi']['id']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['shi']['username']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select>

<select name="xiandai" class="lsvg"  id="xiandai" >
<option value="0">所有县代</option>
<?php $_from = $this->_tpl_vars['xiandai']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['xian']):
?>
<option value="<?php echo $this->_tpl_vars['xian']['id']; ?>
" <?php if ($_REQUEST['xiandai'] == $this->_tpl_vars['xian']['id']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['xian']['username']; ?>
</option>
<?php endforeach; endif; unset($_from); ?>
</select>

<select name="type_id" class="lsvg"  id="type_id" >
<option value="0">店铺类型</option>
<option value="1" <?php if ($_REQUEST['type_id'] == '1'): ?> selected="selected" <?php endif; ?>>维娜店铺</option>
<option value="2" <?php if ($_REQUEST['type_id'] == '2'): ?> selected="selected" <?php endif; ?>>SPA店铺</option>
</select>

<select name="auditing_status" class="lsvg"  id="auditing_status" >
    <option value="-1">审核状态</option>
    <option value="1" <?php if ($_REQUEST['auditing_status'] == '1'): ?> selected="selected" <?php endif; ?>>已审核</option>
    <option value="0" <?php if ($_REQUEST['auditing_status'] == '0'): ?> selected="selected" <?php endif; ?>>未审核</option>
</select>


     <select name="cat_id" style="display:none">
      <option value="0"><?php echo $this->_tpl_vars['lang']['uncategorized']; ?>
</option>
      <?php $_from = $this->_tpl_vars['store_category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cate']):
?>
      <?php if ($this->_tpl_vars['cate']['cat_id'] == $this->_tpl_vars['cat_id']): ?>
      <option value="<?php echo $this->_tpl_vars['cate']['cat_id']; ?>
" selected="selected"><?php echo $this->_tpl_vars['cate']['mark']; ?>
 <?php echo $this->_tpl_vars['cate']['cat_name']; ?>
</option>
      <?php else: ?>
      <option value="<?php echo $this->_tpl_vars['cate']['cat_id']; ?>
"><?php echo $this->_tpl_vars['cate']['mark']; ?>
 <?php echo $this->_tpl_vars['cate']['cat_name']; ?>
</option>
      <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
     </select>
     <input name="keyword" type="text" class="inpMain" placeholder="手机号/姓名/店铺名" value="<?php echo $this->_tpl_vars['keyword']; ?>
" size="20" />
     <input name="submit" class="btnGray" type="submit" value="查询" />

    </form>
    <span style="display:none;">
    <a class="btnGray" href="store.php?rec=re_thumb"><?php echo $this->_tpl_vars['lang']['store_thumb']; ?>
</a>
    <?php if ($this->_tpl_vars['sort']['handle']): ?>
    <a class="btnGray" href="store.php?rec=sort&act=handle"><?php echo $this->_tpl_vars['lang']['sort_close']; ?>
</a>
    <?php else: ?>
    <a class="btnGray" href="store.php?rec=sort&act=handle"><?php echo $this->_tpl_vars['lang']['sort_store']; ?>
</a>
    <?php endif; ?>
    </span>
    <span>
  
     <input name="" type="button" class="btnGray btn-primary btn btn-xs btn-success" data-toggle="modal" data-target="#myModal" 
id="import" value="导入门店excel" style="display: none;">
         
         </span>
    </div>
    <?php if ($this->_tpl_vars['sort']['handle']): ?>
    <div class="homeSortRight">
     <ul class="homeSortBg">
      <?php echo $this->_tpl_vars['sort']['bg']; ?>

     </ul>
     <ul class="homeSortList">
      <?php $_from = $this->_tpl_vars['sort']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['store']):
?>
      <li>
       <img src="<?php echo $this->_tpl_vars['store']['image']; ?>
" width="60" height="60">
       <a href="store.php?rec=sort&act=cancel&id=<?php echo $this->_tpl_vars['store']['id']; ?>
" title="<?php echo $this->_tpl_vars['lang']['sort_cancel']; ?>
">X</a>
      </li>
      <?php endforeach; endif; unset($_from); ?>
     </ul>
    </div>
    <?php endif; ?>
    <div id="list"<?php if ($this->_tpl_vars['sort']['handle']): ?> class="homeSortLeft"<?php endif; ?>>
    <form name="action" method="post" action="store.php?rec=action">
    <table width="100%" border="0" cellpadding="8" cellspacing="0" class="tableBasic">
      <tr>
        <th width="22" align="center"><input name='chkall' type='checkbox' id='chkall' onclick='selectcheckbox(this.form)' value='check'></th>
        <th width="25" align="center"><?php echo $this->_tpl_vars['lang']['record_id']; ?>
</th>
        <th width="70" align="center">店铺名称</th>
        <th width="50" align="center">店铺类型</th>
          <th width="250" align="center">上级代理</th>
         <th width="50" align="center">姓名</th>
        <th  width="50" align="center"><?php echo $this->_tpl_vars['lang']['store_phone']; ?>
</th>
		<th  width="50" align="center">微信名</th>
        <th width="150" align="center" style="display:none;"><?php echo $this->_tpl_vars['lang']['store_category']; ?>
</th>
       <th width="150" align="center" style="display:none;"><?php echo $this->_tpl_vars['lang']['add_time']; ?>
</th>
       <th width="50" align="center"><?php echo $this->_tpl_vars['lang']['store_province']; ?>
</th>
        <th width="50" align="center"><?php echo $this->_tpl_vars['lang']['store_city']; ?>
</th>
          <th width="55" align="center">审核状态</th>
          <th width="70" align="center">更新时间</th>

         <th width="90" align="center"><?php echo $this->_tpl_vars['lang']['handler']; ?>
</th>
      </tr>
      <?php $_from = $this->_tpl_vars['store_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['store']):
?>
      <tr>
        <td align="center"><input type="checkbox" name="checkbox[]" value="<?php echo $this->_tpl_vars['store']['id']; ?>
" /></td>
        <td align="center"><?php echo $this->_tpl_vars['store']['id']; ?>
</td>
        <td><a href="store.php?rec=edit&id=<?php echo $this->_tpl_vars['store']['id']; ?>
"><?php echo $this->_tpl_vars['store']['store_name']; ?>
</a></td>
        <td align="center"><?php if ($this->_tpl_vars['store']['type_id'] == '1'): ?>维娜店铺<?php else: ?>SPA店铺<?php endif; ?></td>
          <td align="right">
		  <?php $_from = $this->_tpl_vars['store']['up_level']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['level'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['level']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['le']):
        $this->_foreach['level']['iteration']++;
?>
		  <font <?php if (($this->_foreach['level']['iteration'] == $this->_foreach['level']['total'])): ?>style="color:#0343ff" <?php endif; ?>><?php echo $this->_tpl_vars['le']['username']; ?>
 <?php if (! ($this->_foreach['level']['iteration'] == $this->_foreach['level']['total'])): ?> ><?php endif; ?> </font> 
		  <?php endforeach; endif; unset($_from); ?>
		  </td>
          <td align="center"><?php echo $this->_tpl_vars['store']['name']; ?>
</td>
			
          <td align="center" style="display:none;"><?php if ($this->_tpl_vars['store']['cat_name']): ?><a href="store.php?cat_id=<?php echo $this->_tpl_vars['store']['cat_id']; ?>
"><?php echo $this->_tpl_vars['store']['cat_name']; ?>
</a><?php else: ?><?php echo $this->_tpl_vars['lang']['uncategorized']; ?>
<?php endif; ?></td>
     
        <td align="center"><?php echo $this->_tpl_vars['store']['phone']; ?>
</td>
		<td align="center"><?php echo $this->_tpl_vars['store']['nickName']; ?>
</td>
        <td align="center"><?php echo $this->_tpl_vars['store']['province']; ?>
</td>
        <td align="center"><?php echo $this->_tpl_vars['store']['city']; ?>
</td>
          <td align="center"><?php if ($this->_tpl_vars['store']['auditing_status'] == 0): ?>未审核<?php else: ?>审核通过<?php endif; ?></td>
          <td align="center"><?php if ($this->_tpl_vars['store']['update_time']): ?><?php echo $this->_tpl_vars['store']['update_time']; ?>
<?php else: ?><?php echo $this->_tpl_vars['store']['add_time']; ?>
<?php endif; ?></td>
        <td align="center">
         <?php if ($this->_tpl_vars['sort']['handle']): ?>
         <a href="store.php?rec=sort&act=set&id=<?php echo $this->_tpl_vars['store']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['sort_btn']; ?>
</a>
         <?php else: ?>
         <a href="store.php?rec=edit&id=<?php echo $this->_tpl_vars['store']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['edit']; ?>
</a>
            | <a href="store.php?rec=del&id=<?php echo $this->_tpl_vars['store']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['del']; ?>
</a>
        <?php if ($this->_tpl_vars['store']['auditing_status'] == '0'): ?> |

            <a href="javascript:void(0)"  style="" onclick="shenhe(<?php echo $this->_tpl_vars['store']['id']; ?>
)"> 审核 </a><?php endif; ?>
         <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; endif; unset($_from); ?>
    </table>
    <div class="action">
     <select name="action" onchange="douAction()">
      <option value="0"><?php echo $this->_tpl_vars['lang']['select']; ?>
</option>
      <option value="del_all"><?php echo $this->_tpl_vars['lang']['del']; ?>
</option>
    <?php echo $this->_tpl_vars['lang']['category_move']; ?>

     </select>
     <select name="new_cat_id" style="display:none">
      <option value="0"><?php echo $this->_tpl_vars['lang']['uncategorized']; ?>
</option>
      <?php $_from = $this->_tpl_vars['store_category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cate']):
?>
      <?php if ($this->_tpl_vars['cate']['cat_id'] == $this->_tpl_vars['cat_id']): ?>
      <option value="<?php echo $this->_tpl_vars['cate']['cat_id']; ?>
" selected="selected"><?php echo $this->_tpl_vars['cate']['mark']; ?>
 <?php echo $this->_tpl_vars['cate']['cat_name']; ?>
</option>
      <?php else: ?>
      <option value="<?php echo $this->_tpl_vars['cate']['cat_id']; ?>
"><?php echo $this->_tpl_vars['cate']['mark']; ?>
 <?php echo $this->_tpl_vars['cate']['cat_name']; ?>
</option>
      <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
     </select>
     <input name="submit" class="btn" type="submit" value="<?php echo $this->_tpl_vars['lang']['btn_execute']; ?>
" />
    </div>
    </form>
    </div>
    <div class="clear"></div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "pager.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    
    <div id="sout" style="display:none;text-align: center;position:fixed;	opacity: 1;top:50%;left: 0;z-index: 2050; 	right: 0;">
		<img src="images/wait.jpg"  style="width:40px;"/>
		<p style="color:white;">正在查询，请稍等</p>
	</div>
    
        <script type="text/javascript">
    <?php echo '
	 function check_coordinate(id){
		 
		 $("#sout").show();
		 $("#fadein").show();
		 
		  $.ajax({
			    //查询坐标
		 		 type: "POST",  //
		  		 url : "store.php", 
		  		 dataType:\'json\',// 
		  		 data:  \'rec=check_coordinate&id=\'+id,   
		  		 success: function(res){
		  			 
		  			 if(res.msg==0){
		  				 
		  				 $("#sout").hide();
		  				 $("#fadein").hide();
		  			 }else{
		  				 
		  				$("#sout").find(\'p\').html(res.data);
		  				setTimeout(function(){
			  				 $("#sout").hide();
			  				 $("#fadein").hide();
			  				if(res.msg==1){
			  					window.location=window.location;
			  				}
			  				 
			  				 
		  				}, 3000);
		  				
		  			 }
		  			 
		  		 }
		  
		  })
		 
	 };

    function  shenhe(id){

        //alert(\'功能实现中 请等待\');

        $.ajax({
            //查询坐标
            type: "POST",  //
            url : "store.php",
            dataType:\'json\',//
            data:  \'rec=shenhe&id=\'+id,
            success: function(res){

                if(res.code==1){

                    window.location=window.location;

                }else{

                    alert(\'审核失败\');


                }

            }

        })


    }
	/**
	*下一个等级
	*/
	
	$(function(){
		//alert("11");
			$(".lsvg").change(function(){
				
				var id=$(this).val();
				var index=$(".lsvg").index(this);
				//如果选中所有的
					
				var onext=$(this).nextAll(\'.lsvg\');
					onext.val(\'0\');
			
				
				var selectid=$(this).attr(\'id\');
				
				if(selectid==\'xiandai\'){
					
					return false;
				}
				
				$.ajax({
					//查询坐标
					type: "POST",  //
					url : "store.php",
					dataType:\'json\',//
					data:  \'rec=level_next&id=\'+id,
					success: function(res){
						
						console.log(res);
						
						var  level_name="";		
						if(selectid==\'changwu\'){
							
							level_name="所有理事";
						}else if(selectid==\'lishi\'){
							level_name="所有省代";
						}else if(selectid==\'shengdai\'){
							level_name="所有市代";
						}else if(selectid==\'shidai\'){
							level_name="所有县代";
						}else{
							
							level_name="所有常务";	
						}
						var option="<option value=\'0\'>"+level_name+"</option>";
						
						
						if(res.msg==0){
						
						console.log(\'查询所有的\');	
						//onext.attr("disabled","disabled");
							
						}else{
						
						
							for(var i=0;i<res.length;i++){

		  					option+="<option value=\'"+res[i].id+"\'>"+res[i].username+"</option>";

		  				 	}
						//console.log(option);
							//onext.attr("disabled","disabled");
							//$(".lsvg").eq(index+1).attr("disabled",false);
							//$(".lsvg").eq(index+2).attr("disabled","disabled");
							
						}
						
						$(".lsvg").eq(index+1).html(option);
						

					}

				})
				
				
			});
	
	});
	
	
	
    '; ?>

    </script>
    
    
    <?php endif; ?>
    <?php if ($this->_tpl_vars['rec'] == 'add' || $this->_tpl_vars['rec'] == 'edit'): ?>
  
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=U3FBZ-LGQKV-646PO-U6XAN-YFJT7-VDFAM"></script>
	<script>
	var xy_str='<?php echo $this->_tpl_vars['store']['xy_axis']; ?>
';
		strs=xy_str.split(",");
		//console.log(strs);
	
	<?php echo '
	var init = function() {
		//alert(xy);
		if(xy_str){
		    var center = new qq.maps.LatLng(strs[0],strs[1]);
		}else{
			var center = new qq.maps.LatLng(31.239580,121.499763);//默认东方明珠
		}

	    var map = new qq.maps.Map(document.getElementById(\'container\'),{
	        center: center,
	        zoom: 14
	    });
	    var marker = new qq.maps.Marker({
	        position: center,
	        draggable: true,
	        map: map
	    });
	    /****自定义图标*****/
        //var anchor = new qq.maps.Point(0, 39),
           // size = new qq.maps.Size(42, 68),  //默认图片大小，不需要其他参数了
           // origin = new qq.maps.Point(0, 0),
            markerIcon = new qq.maps.MarkerImage(
                //http://api.map.baidu.com/img/markers.png
                //"https://3gimg.qq.com/lightmap/api_v2/2/4/99/theme/default/imgs/marker.png",
                "images/marker.png"
            //    size,
            //    origin,
              //  anchor
            );
        marker.setIcon(markerIcon);
	    /***end***/
        /***********多Marker****************/
        var infoWin = new qq.maps.InfoWindow({
            map: map
        });
        var latlngs = [];
        var ups=[];
        //alert(dist);
        //遍历json
        dist=eval(dist);
        //console.log(dist);
        for (var i=0;i<dist.length;i++){
            console.log(dist[i].xy_axis);
            var xy= dist[i].xy_axis;
            var xya=xy.split(\',\');
            var x=xya[0];
            var y=xya[1];
            var up_level=dist[i].up_level;
            var sj="";
            if(up_level){
                
                for (var j=0;j<up_level.length;j++){
                    sj +=up_level[j][\'username\']+\'>\';

                }

                sj=sj.substr(0,sj.length-1); //去掉最后一个尖括号
                console.log(sj);
            }
            ups[i]=sj;
            latlngs[i]= new qq.maps.LatLng(x,y);

            (function(n){
                var marker1 = new qq.maps.Marker({
                    position: latlngs[n],
                    map: map
                });
                qq.maps.event.addListener(marker1, \'click\', function() {
                    infoWin.open();
                    infoWin.setContent(\'<div style="text-align:left;white-space:\'+
                        \'nowrap;margin:10px;">店铺名称:\' +
                        dist[n][\'store_name\'] + \'<br>姓名:\'+dist[n][\'name\']+\'<br>电话:\'+dist[n][\'phone\']+\'<br>店铺类型:\'+dist[n][\'store_type\']+\'<br>上级代理:\'+ups[n]+\'</div>\');
                    infoWin.setPosition(latlngs[n]);
                });
            })(i);




        }
        console.log(latlngs);


        /* 官方多点标准demo
          var infoWin = new qq.maps.InfoWindow({
            map: map
        });

        var latlngs = [
             new qq.maps.LatLng(31.447947553154027,104.6740399255371),
             new qq.maps.LatLng(31.487947553154027,104.6640399255371),
             new qq.maps.LatLng(31.417947553154027,104.6140399255371)
        ];
        for(var i = 0;i < latlngs.length; i++) {
            (function(n){
                var marker1 = new qq.maps.Marker({
                    position: latlngs[n],
                    map: map
                });
                qq.maps.event.addListener(marker1, \'click\', function() {
                    infoWin.open();
                    infoWin.setContent(\'<div style="text-align:center;white-space:\'+
                        \'nowrap;margin:10px;">这是第 \' +
                        n + \' 个标注</div>\');
                    infoWin.setPosition(latlngs[n]);
                });
            })(i);
        }
        */
	    /************end多marker****************/

	     //添加圆形覆盖物1
        var circle_color =new qq.maps.Color(255, 208, 70, 0.3); //设置填充颜色和透明度

        var circle=new qq.maps.Circle({
            map:map,
            center:center,
            radius:2000,
            fillColor:circle_color,
            strokeWeight:1
        });
        //end圆形覆盖物

        //添加圆形覆盖物2
        var circle_color1 =new qq.maps.Color(255, 208, 70, 0.2); //设置填充颜色和透明度

        var circle1=new qq.maps.Circle({
            map:map,
            center:center,
            radius:1000,
            fillColor:circle_color1,
            strokeWeight:1
        });
        //end圆形覆盖物

        //添加信息窗口
        var info = new qq.maps.InfoWindow({
            map: map
        });
        //设置Marker停止拖动事件
        qq.maps.event.addListener(marker, \'dragend\', function() {
            /*info.open();
            info.setContent(\'<div style="text-align:center;white-space:nowrap;\' +
                \'margin:10px;">拖动标记</div>\');
            */
            //getPosition()  返回Marker的位置
           // info.setPosition(marker.getPosition());
            console.log(marker.getPosition());
            document.getElementById(\'xy_axis\').value=marker.getPosition();
            
        });
	}
	window.onload=init;
	'; ?>

</script>



    <h3><a href="<?php echo $this->_tpl_vars['action_link']['href']; ?>
" class="actionBtn"><?php echo $this->_tpl_vars['action_link']['text']; ?>
</a><?php echo $this->_tpl_vars['ur_here']; ?>
</h3>
    <form action="store.php?rec=<?php echo $this->_tpl_vars['form_action']; ?>
" method="post" enctype="multipart/form-data">

		<table width="100%" border="0" cellpadding="8" cellspacing="0" class="tableBasic" style="border-right: 1px solid #ddd;">
    <tr>
        <td width="10%">店铺编号</td>
        <td width="17%"><input type="text" class="inpMain" size="15" name="store_id" <?php if ($this->_tpl_vars['rec'] == 'edit'): ?> disabled="disabled" <?php endif; ?>  value="<?php if ($this->_tpl_vars['store']['store_id']): ?>  <?php echo $this->_tpl_vars['store']['store_id']; ?>
 <?php else: ?> 无<?php endif; ?>"></td>
        <td width="10%">店铺名称</td>
        <td width="17%"><input type="text" name="store_name" value="<?php echo $this->_tpl_vars['store']['store_name']; ?>
" size="15" class="inpMain"></td>
        <td width="10%"><?php echo $this->_tpl_vars['lang']['store_phone']; ?>
</td>
        <td width="17%"><input type="text" name="phone" value="<?php echo $this->_tpl_vars['store']['phone']; ?>
" size="15" class="inpMain">
        </td>
        <!--
        <td width="19%" style="border-right: 1px solid #DDD;">&nbsp;</td> -->
        <td  rowspan="5" align="center" style="">
            <div id="preview">
                <img id="imghead" src="<?php if ($this->_tpl_vars['store']['image_url']): ?> ../api/<?php echo $this->_tpl_vars['store']['image_url']; ?>
<?php else: ?>images/img_none.png<?php endif; ?>" width="155" height="127">

            </div>
            <br/>
            <br/>



            <div>
                <input class="se2" id="f_file" type="file" onchange="previewImage(this)"  name="image"/>
                <label for="f_file">
                    <input class="se1"   type="button" value="上传图片" />
                </label>
            </div>



        </td>

    </tr>
    <tr>
        <td><?php echo $this->_tpl_vars['lang']['store_diqu']; ?>
/<?php echo $this->_tpl_vars['lang']['store_province']; ?>
</td>
        <td>
        <select name="province" class="province">
        <option value="0"><?php echo $this->_tpl_vars['lang']['provice_select_empty']; ?>
</option>
        <?php $_from = $this->_tpl_vars['plist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['plist']):
?>
        <option value="<?php echo $this->_tpl_vars['plist']['region_id']; ?>
" <?php if ($this->_tpl_vars['plist']['region_id'] == $this->_tpl_vars['store']['province']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['plist']['region_name']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
        
        </select>
        
        </td>
        <td><?php echo $this->_tpl_vars['lang']['store_diqu']; ?>
/<?php echo $this->_tpl_vars['lang']['store_city']; ?>
</td>
        <td>
        <select name="city" class="city">
        <option value="0"><?php echo $this->_tpl_vars['lang']['city_select_empty']; ?>
</option>
        <?php $_from = $this->_tpl_vars['clist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['clist']):
?>
        <option value="<?php echo $this->_tpl_vars['clist']['region_id']; ?>
" <?php if ($this->_tpl_vars['clist']['region_id'] == $this->_tpl_vars['store']['city']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['clist']['region_name']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
        
        </select>
        
        </td>
        <td><?php echo $this->_tpl_vars['lang']['store_diqu']; ?>
/<?php echo $this->_tpl_vars['lang']['store_district']; ?>
</td>
        <td>
        
         <select name="district" class="district">
        <option value="0"><?php echo $this->_tpl_vars['lang']['district_select_empty']; ?>
</option>
        <?php $_from = $this->_tpl_vars['dlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['dlist']):
?>
        <option value="<?php echo $this->_tpl_vars['dlist']['region_id']; ?>
" <?php if ($this->_tpl_vars['dlist']['region_id'] == $this->_tpl_vars['store']['district']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['dlist']['region_name']; ?>
</option>
        <?php endforeach; endif; unset($_from); ?>
        
        </select>
        

        </td>

    </tr>
    <tr>
        <td><?php echo $this->_tpl_vars['lang']['store_address']; ?>
</td>
        <td colspan="5"><input type="text" size="80" name="address" value="<?php echo $this->_tpl_vars['store']['address']; ?>
" class="inpMain"></td>

     
    </tr>
    <tr>
        <td>姓名</td>
        <td>
        <select name="level3_code" id="level3_select"  eqm=2 class="level_select"  style="display:none;">
        
        <option value="0">请选择</option>
        
       <?php $_from = $this->_tpl_vars['levels']['level3']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['level']):
?>
		<option value="<?php echo $this->_tpl_vars['level']['code']; ?>
" <?php if ($this->_tpl_vars['level']['code'] == $this->_tpl_vars['store']['level3_code']): ?> selected="selected" <?php endif; ?>><?php echo $this->_tpl_vars['level']['code']; ?>
-<?php echo $this->_tpl_vars['level']['zname']; ?>
</option>
		<?php endforeach; endif; unset($_from); ?>

      </select>  
	  
	  <input type="text" name="name" value="<?php echo $this->_tpl_vars['store']['name']; ?>
" size="15" class="inpMain">
	  
        
        </td>
		<td>
		店铺类型
		</td>

		<td>
		<input type="radio" name="type_id"  value="1" <?php if ($this->_tpl_vars['store']['type_id'] == '1'): ?> checked <?php endif; ?>> 维娜店铺  &nbsp;
		<input type="radio" name="type_id"  value="2" <?php if ($this->_tpl_vars['store']['type_id'] == '2'): ?> checked <?php endif; ?>>SPA店铺
		</td>
		<td>审核状态</td>
		<td>
		<input type="radio" name="auditing_status"  value="0" <?php if ($this->_tpl_vars['store']['auditing_status'] == '0'): ?> checked <?php endif; ?>> 未审核 &nbsp;
		<input type="radio" name="auditing_status"  value="1" <?php if ($this->_tpl_vars['store']['auditing_status'] == '1'): ?> checked <?php endif; ?>>审核通过
		</td>
        
    </tr>




        <tr>
        <td>位置坐标</td>
        <td colspan="5">
        <input type="text" name="xy_axis" class="inpMain"  style="width: 250px;" id="xy_axis" value="<?php echo $this->_tpl_vars['store']['xy_axis']; ?>
">
            <span style="color: #2b579a">(圆形覆盖物半径为1km/2km)</span>
           <span style="float: right;">
               <a href="https://lbs.qq.com/tool/getpoint/" target="_blank" style="color: #8e8e8e;">
                   坐标拾取器：https://lbs.qq.com/tool/getpoint/</a>
           </span>

        </td>


		</tr>
            <tr>
                <td colspan="7" align="center">

                    <div id="container" style="width: 100%;height: 400px;">
                    
                    </div>
                </td>
            </tr>
    <tr>
    <td colspan="7" align="center">		<input type="hidden" name="token" value="<?php echo $this->_tpl_vars['token']; ?>
" />
        <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['store']['id']; ?>
">
        <input name="submit" class="btn" type="submit" value="<?php echo $this->_tpl_vars['lang']['btn_submit']; ?>
" /></td>
    </tr>
</table>
		

    </form>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['rec'] == 're_thumb'): ?>
    <h3><a href="<?php echo $this->_tpl_vars['action_link']['href']; ?>
" class="actionBtn"><?php echo $this->_tpl_vars['action_link']['text']; ?>
</a><?php echo $this->_tpl_vars['ur_here']; ?>
</h3>
    <script type="text/javascript">
    <?php echo '
     function mask(i) {
        document.getElementById(\'mask\').innerHTML += i;
        document.getElementById(\'mask\').scrollTop = 100000000;
     }
     function success() {
        var d=document.getElementById(\'success\');
        d.style.display="block";
     }
    '; ?>

    </script>
    <dl id="maskBox">
     <dt><em><?php echo $this->_tpl_vars['mask']['count']; ?>
</em><?php if (! $this->_tpl_vars['mask']['confirm']): ?><form action="store.php?rec=re_thumb" method="post"><input name="confirm" class="btn" type="submit" value="<?php echo $this->_tpl_vars['lang']['store_thumb_start']; ?>
" /></form><?php endif; ?></dt>
     <dd class="maskBg"><?php echo $this->_tpl_vars['mask']['bg']; ?>
<i id="success"><?php echo $this->_tpl_vars['lang']['store_thumb_succes']; ?>
</i></dd>
     <dd id="mask"></dd>
    </dl>
    <?php endif; ?>
   </div>
 </div>
 <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.htm", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
 </div>
<?php if ($this->_tpl_vars['rec'] == 'default'): ?>
<script type="text/javascript">
<?php echo '
onload = function()
{
 document.forms[\'action\'].reset();
}

function douAction()
{
 var frm = document.forms[\'action\'];
 frm.elements[\'new_cat_id\'].style.display = frm.elements[\'action\'].value == \'category_move\' ? \'\' : \'none\';
}
'; ?>

</script>
<?php endif; ?>
<?php if ($this->_tpl_vars['rec'] != 're_thumb'): ?>

 <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
 <!-- 新 Bootstrap 核心 CSS 文件 -->
<link href="templates/bootstrap.min.css" rel="stylesheet" >
<!-- 
<link rel="stylesheet" type="text/css" href="templates/css/bootstrap.css" />-->

<link href="templates/font-awesome.min.css" rel="stylesheet">

<link rel="stylesheet" href="templates/css/build.css">

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div style="display:none" class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<form class="form-horizontal ajaxForm2" id='formadd' method="post"  enctype="multipart/form-data" action="store.php?rec=excel_import">
<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
               <button type="button" class="close" data-dismiss="modal"   aria-hidden="true">×
               </button>
               <h4 class="modal-title" id="myModalLabel">
                  导入EXCEL
               </h4>
            </div>
		<div class="modal-body">
		<div class="row">
		<div class="col-xs-12">
		<div class="form-group">
		<!-- <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 所属商户： </label> -->

	
	<div class="radio radio-info radio-danger">
		 <input type="radio" id="inlineRadio1" value="1" name="radioInline" checked="">
	 <label for="inlineRadio1">删除现所有数据，重新上传 </label>
	</div>
	
	<div class="radio radio-danger">
		 <input type="radio" id="inlineRadio2" value="2" name="radioInline">
		<label for="inlineRadio2"> 在现有数据基础上上传(如果数据存在会更新) </label>
	  </div>
		
		<div class="col-sm-9">
<?php echo '
 <script>
      function getFilename(){
        var filename=document.getElementById("file").value;
        if(filename==undefined||filename==""){
          document.getElementById("filename").innerHTML="点击此处上传文件";
        } else{
          var fn=filename.substring(filename.lastIndexOf("\\\\")+1);
          document.getElementById("filename").innerHTML=fn; //将截取后的文件名填充到span中
        }
      }
      $(function(){
    	  
          $("#formbtn").click(function(){
        	  
            	$("#layout").show();
            	$("#fadein").show();
            	  
              });
      });

</script>
'; ?>

		<a href="javascript:;" class="a-upload">
		   <input type="file" id="file" name="excelData"   onchange="getFilename()" datatype="*4-50" />
		   <span id="filename" style="max-width: 180px; overflow: hidden;display: block;">点击这里上传文件</span>
		</a>
		<a class="exc_down"  style="margin-left: 10px;" href="javascript:void(0)"  >点击下载excel标准数据格式</a>
		</div>
		<div style="color: #861515; text-align: center; font-size: 12px;font-weight: bold; padding-top: 10px;"> 
		注意:如果文件数据量过大,请将文件转化为.CSV文件后再上传
		</div>
          </div></div>
		</div>
		</div>
            <div class="modal-footer">
               <button type="submit" id='formbtn' class="btn btn-primary">
                  提交保存
               </button>
               <button type="button" class="btn btn-default" data-dismiss="modal">
                  关闭
               </button>
            </div>
         </div><!--/.modal-content-->
	</div><!--/.modal-dialog-->
		</form></div><!--/.modal -->
		
<div id="layout" style="display:none;text-align: center;position:fixed;	opacity: 1;top:50%;left: 0;z-index: 2050; 	right: 0;">
		<img src="images/wait.jpg" />
		<p style="color:white;">正在上传，请稍后</p>
	<!-- 	<button id="closeBtn" onclick="testBtn1()">关闭</button>-->
</div>
<div id="fadein" style="display:none;position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 2000; opacity: .5;  background-color: #000;"></div>		
</body>
</html>
<?php endif; ?>