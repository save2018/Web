<?php /* Smarty version 2.6.26, created on 2018-12-27 10:06:32
         compiled from user.htm */ ?>
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
<script type="text/javascript" src="images/jquery.autotextarea.js"></script>
<script>
<?php echo '
$(function(){

	$("#level").change(function(){
		
		var cid=$(this).val();
	
		$.ajax({
			 
			type: "get",  //
			 url : "zuzhi.php", 
			 dataType:\'json\',// 
			 data:  \'rec=get_parent&cid=\'+cid,   
			 success: function(res){
				 
				if(res){
					 var option=\'<option value="0">请选择</option>\';
					 
					 for(var i=0;i<res.length;i++){
						 
						 
						 option+=\'<option value="\'+res[i].zid+\'">\'+res[i].code+\'-\'+res[i].zname+\'</option>\';
					 }
					
					
					$("#level_parent").html(option);
					
				}
			 }
		});
		
		/////////////
		
	});
	
	$(".exc_down").click(function(){
		
		
		location.href="images/代理商信息批量上传格式.xlsx";
		
	});
	
	
});

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

    <?php echo $this->_tpl_vars['ur_here']; ?>
</h3>
        <div class="filter">
    
<form action="user.php" method="POST">
<select name="level" class="lsvg" id="quanbu">
<option value="0">全部</option>
<option value="J" <?php if ($_REQUEST['level'] == 'J'): ?> selected="selected" <?php endif; ?> >常务</option>
<option value="L" <?php if ($_REQUEST['level'] == 'L'): ?> selected="selected" <?php endif; ?> >理事</option>
<option value="D" <?php if ($_REQUEST['level'] == 'D'): ?> selected="selected" <?php endif; ?> >省代</option>
<option value="B" <?php if ($_REQUEST['level'] == 'B'): ?> selected="selected" <?php endif; ?> >市代</option>
<option value="G" <?php if ($_REQUEST['level'] == 'G'): ?> selected="selected" <?php endif; ?> >县代</option>
</select>









     <input name="keyword" type="text" class="inpMain" placeholder="手机号/姓名" value="<?php echo $this->_tpl_vars['keyword']; ?>
" size="20" />
     <input name="submit" class="btnGray" type="submit" value="查询" />

    </form>
    
        <span>
  
     <input name="" type="button" class="btnGray btn-primary btn btn-xs btn-success" data-toggle="modal" data-target="#myModal" 
id="import" value="批量上传excel" style="">
         
         </span>
    
    </div>

    <div id="list"<?php if ($this->_tpl_vars['sort']['handle']): ?> class="homeSortLeft"<?php endif; ?> >
    

    <form name="action" method="post" action="user.php?rec=action" style="">
    <table width="100%" border="0" cellpadding="8" cellspacing="0" class="tableBasic" style="">
     <tr>
      <th width="22" align="center"><input name='chkall' type='checkbox' id='chkall' onclick='selectcheckbox(this.form)' value='check'></th>
      <th width="40" align="center"><?php echo $this->_tpl_vars['lang']['record_id']; ?>
</th>
      <th align="center" width="50"><?php echo $this->_tpl_vars['lang']['user_name']; ?>
</th>
       <th align="center" width="250">上级代理</th>
       <!--<th width="150" align="center"><?php echo $this->_tpl_vars['lang']['zuzhi_category']; ?>
</th>
     <th width="80" align="center"><?php echo $this->_tpl_vars['lang']['add_time']; ?>
</th>-->
     <th width="80" align="center">ID</th>
      <th width="80" align="center">编码</th>
      <th width="80" align="center">级别</th>
      <th width="80" align="center">电话</th>
      <th width="80" align="center">上级编码</th>
      <th width="80" align="center"><?php echo $this->_tpl_vars['lang']['handler']; ?>
</th>
     </tr>
     <?php $_from = $this->_tpl_vars['user_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
     <tr>
      <td align="center"><input type="checkbox" name="checkbox[]" value="<?php echo $this->_tpl_vars['user']['id']; ?>
" /></td>
      <td align="center"><?php echo $this->_tpl_vars['user']['id']; ?>
</td>

      <td align="center"><a href="user.php?rec=edit&id=<?php echo $this->_tpl_vars['user']['id']; ?>
"><?php echo $this->_tpl_vars['user']['username']; ?>
</a></td>
      <td align="right">
      	 <?php $_from = $this->_tpl_vars['user']['up_level']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['level'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['level']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['le']):
        $this->_foreach['level']['iteration']++;
?>
		  <font <?php if (($this->_foreach['level']['iteration'] == $this->_foreach['level']['total'])): ?> style="color:#0343ff;" <?php endif; ?>><?php echo $this->_tpl_vars['le']['username']; ?>
 &nbsp; <?php if (! ($this->_foreach['level']['iteration'] == $this->_foreach['level']['total'])): ?> ><?php endif; ?></font> 
		  <?php endforeach; endif; unset($_from); ?>
      </td>
      <td align="center"><?php echo $this->_tpl_vars['user']['user_id']; ?>
 </td>
      <td align="center"><?php echo $this->_tpl_vars['user']['code']; ?>
 </td>
      <td align="center"><?php echo $this->_tpl_vars['user']['level_code']; ?>
 </td>
      <td align="center"><?php echo $this->_tpl_vars['user']['phone']; ?>
 </td>
      <td align="center"><?php echo $this->_tpl_vars['user']['code_up']; ?>
 </td>
      <td align="center">
      <?php if ($this->_tpl_vars['user']['level_code'] == 'J' || $this->_tpl_vars['user']['level_code'] == 'L' || $this->_tpl_vars['user']['level_code'] == 'D' || $this->_tpl_vars['user']['level_code'] == 'B'): ?>
      <a href="user.php?rec=privilege&id=<?php echo $this->_tpl_vars['user']['id']; ?>
">权限</a> | 
      <?php endif; ?>
      
       <?php if ($this->_tpl_vars['sort']['handle']): ?>
       <a href="user.php?rec=sort&act=set&id=<?php echo $this->_tpl_vars['user']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['sort_btn']; ?>
</a>
       <?php else: ?>
       <a href="user.php?rec=edit&id=<?php echo $this->_tpl_vars['user']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['edit']; ?>
</a> | <a href="user.php?rec=del&id=<?php echo $this->_tpl_vars['user']['id']; ?>
"><?php echo $this->_tpl_vars['lang']['del']; ?>
</a>
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
      <option value="category_move"><?php echo $this->_tpl_vars['lang']['category_move']; ?>
</option>
     </select>
     <select name="new_cat_id" style="display:none">
      <option value="0"><?php echo $this->_tpl_vars['lang']['uncategorized']; ?>
</option>
      <?php $_from = $this->_tpl_vars['zuzhi_category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
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
    <?php endif; ?>
    <?php if ($this->_tpl_vars['rec'] == 'add' || $this->_tpl_vars['rec'] == 'edit'): ?>
    <h3><a href="<?php echo $this->_tpl_vars['action_link']['href']; ?>
" class="actionBtn"><?php echo $this->_tpl_vars['action_link']['text']; ?>
</a><?php echo $this->_tpl_vars['ur_here']; ?>
</h3>
    <form action="user.php?rec=<?php echo $this->_tpl_vars['form_action']; ?>
" method="post" >
     <div class="formBasic">
      <dl>
       <dt>ID</dt>
       <dd>
        <input type="text" name="user_id" value="<?php echo $this->_tpl_vars['user']['user_id']; ?>
" size="50" class="inpMain" />
       </dd>
      </dl>
      <dl>
       <dl>
       <?php if ($this->_tpl_vars['rec'] == 'add'): ?>
        <dt>密码</dt>
        <dd>
         <input type="text" name="password" value="<?php echo $this->_tpl_vars['user']['password']; ?>
" size="50" class="inpMain" />
        </dd>
       </dl>
       <?php endif; ?>
       
       <dt>姓名</dt>
       <dd>
        <input type="text" name="username" value="<?php echo $this->_tpl_vars['user']['username']; ?>
" size="50" class="inpMain" />
       </dd>
      </dl>
            <dl>
       <dt>编号</dt>
       <dd>
        <input type="text" name="code" value="<?php echo $this->_tpl_vars['user']['code']; ?>
" size="50" class="inpMain" />
       </dd>
      </dl>
      
      
      <dl>
       <dt>级别</dt>
       <dd>
        <input type="text" name="level" value="<?php echo $this->_tpl_vars['user']['level_code']; ?>
" size="50" class="inpMain" />
		<font size="3" color="red">(J:常务;L:理事;D:省代;B:市代;G:县代;)</font>
       </dd>
      </dl>
      
       <dl>
       <dt>电话</dt>
       <dd>

        <input type="text" name="phone" value="<?php echo $this->_tpl_vars['user']['phone']; ?>
" size="50" maxlength="11"  class="inpMain" />
       </dd>
      </dl>
      <dl>
       <dt>上级编码</dt>
       <dd>

        <input type="text" name="code_up" value="<?php echo $this->_tpl_vars['user']['code_up']; ?>
" size="50" class="inpMain" />
       </dd>
      </dl>
      
      
      <?php if ($this->_tpl_vars['zuzhi']['defined']): ?>
      <dl>
       <dt valign="top"><?php echo $this->_tpl_vars['lang']['zuzhi_defined']; ?>
</dt>
       <dd>
        <textarea name="defined" id="defined" cols="50" class="textAreaAuto" style="height:<?php echo $this->_tpl_vars['zuzhi']['defined_count']; ?>
0px"><?php echo $this->_tpl_vars['zuzhi']['defined']; ?>
</textarea>
        <script type="text/javascript">
         <?php echo '
          $("#defined").autoTextarea({maxHeight:300});
         '; ?>

        </script>
        </dd>
      </dl>
      <?php endif; ?>
      <dl style="display:none">
       <dt valign="top"><?php echo $this->_tpl_vars['lang']['zuzhi_content']; ?>
</dt>
       <dd >
       
        <script charset="utf-8" src="include/kindeditor/kindeditor.js"></script>
        <script charset="utf-8" src="include/kindeditor/lang/zh_CN.js"></script>
        <script>
        <?php echo '
         var editor;
         KindEditor.ready(function(K) {
             editor = K.create(\'#content\');
         });
        '; ?>

        </script>
        <!-- /KindEditor -->
        <textarea id="content" name="content" style="width:98%;height:500px;" class="textArea"><?php echo $this->_tpl_vars['zuzhi']['content']; ?>
</textarea>
       </dd>
      </dl>

      <dl>
       <input type="hidden" name="token" value="<?php echo $this->_tpl_vars['token']; ?>
" />
       <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['user']['id']; ?>
">
       <input name="submit" class="btn" type="submit" value="<?php echo $this->_tpl_vars['lang']['btn_submit']; ?>
" />
      </dl>
     </div>
    </form>
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


 <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="https://cdn.bootcss.com/jquery/2.1.1/jquery.min.js"></script>
 <!-- 新 Bootstrap 核心 CSS 文件 -->
<link href="templates/bootstrap.min.css" rel="stylesheet" >
<!-- 
<link rel="stylesheet" type="text/css" href="templates/css/bootstrap.css" />-->

<link href="http://cdn.bootcss.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="templates/css/build.css">

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<div style="display:none" class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<form class="form-horizontal ajaxForm2" id='formadd' method="post"  enctype="multipart/form-data" action="user.php?rec=excel_import">
<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
               <button type="button" class="close" data-dismiss="modal"   aria-hidden="true">×
               </button>
               <h4 class="modal-title" id="myModalLabel">
                  批量导入代理商信息 EXCEL
               </h4>
            </div>
		<div class="modal-body">
		<div class="row">
		<div class="col-xs-12">
		<div class="form-group">
		<!-- <label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 所属商户： </label> -->

	<!--
	<div class="radio radio-info radio-danger">
		 <input type="radio" id="inlineRadio1" value="1" name="radioInline" checked="">
	 <label for="inlineRadio1">删除现所有数据，重新上传 </label>
	</div>
	
	<div class="radio radio-danger">
		 <input type="radio" id="inlineRadio2" value="2" name="radioInline">
		<label for="inlineRadio2"> 在现有数据基础上上传(如果数据存在会更新) </label>
	  </div>
	-->	
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

		<a href="javascript:;" class="a-upload" style="background: #130101;color: white;">
		   <input type="file" id="file" name="excelData"   onchange="getFilename()" datatype="*4-50" />
		   <span id="filename" style="max-width: 140px; overflow: hidden;display: block;">点击这里上传文件</span>
		</a>
		<a class="exc_down"  style="margin-left: 10px;" href="javascript:void(0)"  >点击下载xls标准数据格式</a>
		</div>
		<div style="display:none;color: #861515; text-align: center; font-size: 12px;font-weight: bold; padding-top: 10px;"> 
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






<?php endif; ?>
</body>
</html>