<?php
/****************************************
**
**	ECEXTION
**
*****************************************/
?>
<?php $htmlClass->getMobileTitle($file_data['title'],$title_background,$title_color,$font_size); ?>
<?php print($spacer); ?>
<?php print($contents_table); ?>
<div id="exection" style="text-align: center;">
<? if(!empty($post_data['error'])){ ?>
<? print($post_data['error']); ?><br /><br />
<? }else{ ?>
<? print nl2br($file_data['content']); ?>
<? } ?>
</div>
<?php print($table_end); ?>
<?php print($hr); ?>
<div style="text-align: right;">
<a href="/">戻る</a>
</div>
<?php print($hr); ?>
