<?php
if(isset($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
if($id){ // check that page exists
	$page=dbRow("SELECT * FROM pages WHERE id=$id");
	if($page!==false){
		$page_vars=json_decode($page['vars'],true);
		$edit=true;
	}
}
if(!isset($edit)){
	$parent=isset($_REQUEST['parent'])?(int)$_REQUEST['parent']:0;
	$special=0;
	if(isset($_REQUEST['hidden']))$special+=2;
	$page=array('parent'=>$parent,'type'=>'0','body'=>'','name'=>'','title'=>'','ord'=>0,'description'=>'','id'=>0,'keywords'=>'','special'=>$special,'template'=>'');
	$page_vars=array();
	$id=0;
	$edit=false;
}

// { if page is hidden from navigation, show a message saying that
if($page['special']&2)echo '<em>NOTE: this page is currently hidden from the front-end navigation. Use the "Advanced Options" to un-hide it.</em>';
// }
echo '<form id="pages_form" method="post">';
echo '<input type="hidden" name="id" value="',$id,'" />'
		,'<div class="tabs"><ul>'
		,'<li><a href="#tabs-common-details">Common Details</a></li>'
		,'<li><a href="#tabs-advanced-options">Advanced Options</a></li>';
// add plugin tabs here
echo '</ul>';
// { Common Details
echo '<div id="tabs-common-details"><table style="clear:right;width:100%;"><tr>';
// { name
echo '<th width="5%">name</th><td width="23%"><input id="name" name="name" value="',htmlspecialchars($page['name']),'" /></td>';
// }
// { title
echo '<th width="10%">title</th><td width="23%"><input name="title" value="',htmlspecialchars($page['title']),'" /></td>';
// }
// { url 
echo '<th colspan="2">';
if($edit){
	$u='/'.str_replace(' ','-',$page['name']);
	echo '<a style="font-weight:bold;color:red" href="',$u,'" target="_blank">VIEW PAGE</a>';
}
else echo '&nbsp;';
echo '</th>';
// }
echo '</tr><tr>';
// { type
echo '<th>type</th><td><select name="type"><option value="0">normal</option>';
// insert plugin page types here
echo '</select></td>';
// }
// { parent
echo '<th>parent</th><td><select name="parent">';
if($page['parent']){
	$parent=Page::getInstance($page['parent']);
	echo '<option value="',$parent->id,'">',htmlspecialchars($parent->name),'</option>';
}
else echo '<option value="0"> -- ','none',' -- </option>';
echo '</select>',"\n\n",'</td>';
// }
if(!isset($page['associated_date']) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$page['associated_date']) || $page['associated_date']=='0000-00-00')$page['associated_date']=date('Y-m-d');
echo '<th>Associated Date</th><td><input name="associated_date" class="date-human" value="',$page['associated_date'],'" /></td>';
echo '</tr>';
// }
// { page-type-specific data
echo '<tr><th>body</th><td colspan="5">';
echo ckeditor('body',$page['body']);
echo '</td></tr>';
// }
echo '</table></div>';
// }
// { Advanced Options
echo '<div id="tabs-advanced-options">';
echo '<table><tr><td>';
// { metadata 
echo '<h4>MetaData</h4><table>';
echo '<tr><th>keywords</th><td><input name="keywords" value="',htmlspecialchars($page['keywords']),'" /></td></tr>';
echo '<tr><th>description</th><td><input name="description" value="',htmlspecialchars($page['description']),'" /></td></tr>';
// { template
// we'll add this in the next chapter
// }
echo '</table>';
// }
echo '</td><td>';
// { special
echo '<h4>Special</h4>';
$specials=array('Is Home Page','Does not appear in navigation');
for($i=0;$i<count($specials);++$i){
	if($specials[$i]!=''){
		echo '<input type="checkbox" name="special[',$i,']"';
		if($page['special']&pow(2,$i))echo ' checked="checked"';
		echo ' />',$specials[$i],'<br />';
	}
}
// }
// { other
echo '<h4>Other</h4>';
echo '<table>';
// { order of sub-pages
echo '<tr><th>Order of sub-pages</th><td><select name="page_vars[order_of_sub_pages]">';
$arr=array('as shown in admin menu','alphabetically','by associated date');
foreach($arr as $k=>$v){
	echo '<option value="',$k,'"';
	if(isset($page_vars['order_of_sub_pages']) && $page_vars['order_of_sub_pages']==$k)echo ' selected="selected"';
	echo '>',$v,'</option>';
}
echo '</select><select name="page_vars[order_of_sub_pages_dir]"><option value="0">ascending (a-z, 0-9)</option>';
echo '<option value="1"';
if(isset($page_vars['order_of_sub_pages_dir']) && $page_vars['order_of_sub_pages_dir']=='1')echo ' selected="selected"';
echo '>descending (z-a, 9-0)</option></select></td></tr>';
// }
echo '</table>';
// }
echo '</td></tr></table></div>';
// }
echo '</div><input type="submit" name="action" value="',($edit?'Update Page Details':'Insert Page Details'),'" /></form>';
echo '<script>window.currentpageid='.$id.';</script>';
echo '<script src="/ww.admin/pages/pages.js"></script>';
