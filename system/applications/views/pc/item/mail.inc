<?php
/****************************
**
**	CONTENT TYPE 宣言
**
*****************************/
?>
<?php header("Content-type: text/html; charset=".SITE_CHARSET.""); ?>

<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/mail.script.js" type="text/javascript" ></script>
<script src="/common/js/mail.function.js" type="text/javascript" ></script>

<?php
$count			= 0;
$count_none		= 0;
if(isset($item_list['id'])){
	$count		= count($item_list['id']);
}
if(isset($item_list_nouse['id'])){
	$count_none	= count($item_list_nouse['id']);
}
if($count > 0 || $count_non > 0){
?>
	<div class="modal-list">
		<ul>
		<?php
		/*************************************
		**
		**	使えるアイテム
		**
		**************************************/
		for($i=0;$i<$count;$i++){
			$item_id			= $item_list['id'][$i];
			$item_count			= $item_list['unit'][$i];
			if($item_count > 0){
				# 使用中
				if(isset($itemuse_list[$item_id]['id'])){
					$class		= "item-list-using";
					$name		= " name=\"using\"";
				# 使用可能
				}else{
					$class		= "item-list";
					$name		= NULL;
				}
			}else{
				# 使用中
				if(isset($itemuse_list[$item_id]['id'])){
					$class		= "item-list-using";
					$name		= " name=\"using\"";
				# 使用不可
				}else{
					$class		= "item-list-none";
					$name		= NULL;
				}
			}
		?>
			<li class="<?php print($class); ?>" id="<?php print($item_id); ?>"<?php print($name); ?>>
				<?php if(!empty($item_list['image'][$i])){ ?>
				<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list['image'][$i]); ?>" />
				<?php } ?>
				<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list['name'][$i]); ?></span>
				<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list['description'][$i]); ?></span>
				<?php if(!empty($item_count)){ ?>
					<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
				<?php } ?>
				<div class="item-using-area" id="item-use-<?php print($item_id); ?>">
					<?php if(isset($itemuse_list[$item_id]['id'])){ ?>
					<p class="item-using">現在使用中</p>
					<p class="item-limit-area" id="item-limit-<?php print($item_id); ?>">
						<span class="style-item-using">現在使用中</span><br /><br />
						【有効期限】<br />
						<?php if($itemuse_list[$item_id]['type'] == 1 || $itemuse_list[$item_id]['type'] == 3){ ?>
							残り<span class="limit-count"><?php print($itemuse_list[$item_id]['limit_time']); ?></span>分
						<?php }elseif($itemuse_list[$item_id]['type'] == 2){ ?>
							残り<span class="limit-count"><?php print($itemuse_list[$item_id]['limit_count']); ?></span>回
						<?php } ?>
					</p>
					<?php } ?>
				</div>
			</li>
		<?php } ?>

		<?php
		/*************************************
		**
		**	使えないアイテム
		**
		**************************************/
		for($i=0;$i<$count_none;$i++){
			$item_id		= $item_list_nouse['id'][$i];
			$item_count		= $item_list_nouse['unit'][$i];
		?>
			<li class="item-list-nouse" id="<?php print($item_id); ?>">
				<?php if(!empty($item_list_nouse['image'][$i])){ ?>
				<img src="<?php print(HTTP_ITEM_IMAGE); ?>/<?php print($item_list_nouse['image'][$i]); ?>" />
				<?php } ?>
				<span class="item-name"	id="item-name-<?php print($item_id); ?>"><?php print($item_list_nouse['name'][$i]); ?></span>
				<span class="item-description" id="item-description-<?php print($item_id); ?>"><?php print($item_list_nouse['description'][$i]); ?></span>
				<?php if(!empty($item_count)){ ?>
					<p class="check-count" id="item-num-<?php print($item_id); ?>"><?php print($item_count); ?></p>
				<?php } ?>
				<p class="item-nouse">ここでは使えません</p>
			</li>
		<?php } ?>
		</ul>
	</div>
<?php } ?>
