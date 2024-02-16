<?php
################################## ファイル概要 #################################
##
##	user_upcsv_new_etc.inc
##	----------------------------------------------------------------------------
##	ユーザcsvアップロードページ
##	----------------------------------------------------------------------------
##
##################################### HTML ######################################
?>
<div id="main_contents">



<? if($action == 'upload'){ ?>

	<table cellspacing="0" cellpadding="0" border="0" class="table_frame" style="width: 400px;">
	<tr>
	<td class="table_contents" style="text-align:center;">
	<? print($cnt); ?> / <? print($total); ?> 登録完了しました。
	</td>
	</tr>
	</table>

	<br />

<? }else{ ?>

	<form action="user_img_upcsv.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="upload">
	<? print($form_sec_data); ?>

	<table cellspacing="0" cellpadding="0" border="0" class="table_frame" style="width: 720px;">

	<tr>
		<td class="table_title">CSV FILE</td>
		<td class="table_contents">
			<input type="hidden" name="MAX_FILE_SIZE" value="50000000">
			<input type="file" name="upfile">
		</td>
	</tr>

	<tr>
		<td class="table_contents" colspan="2">
		<div align="center">
			<input type="submit" value="UPLOAD" class="submit" onClick="return confirm('UPLOADします。よろしいですか？')" />
		</div>
		</td>
	</tr>
	</table>
	</form>
<br />

<? } ?>

</div>
