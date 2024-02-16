<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>
<script src="/common/js/a.ctrl.function.js" type="text/javascript" ></script>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>
	<div class="title"><?php print($event_title); ?></div>
	<?php
	if($reward_character>0){
	?>
		
		<!--
		<div class="title-link">
			<div><a href="/<?php print($directory); ?>/"><img src="/images/smart/button/button-ranking-event.png" /></a></div>
			<div><a href="/<?php print($directory); ?>/character/0/"><img src="/images/smart/button/button-ranking-total.png" /></a></div>
			<p class="clear"></p>
		</div>
		-->
		<div class="clear"></div>
	<div id="reward-list">
	<ul>
	<?php
		foreach($reward_character_list as $key => $val){
	?>	
		<li>
			<a href="/rewards/display/<?php print($event_id); ?>/<?php print($val["character_id"]); ?>/<?php print($val["character_id"]); ?>/">
				<img src="<?php print($val["character_image"]); ?>" border="0" />
			</a>
			<?php if(!empty($val["rewards_num"])){ ?>
				<span class="reward-items"><?php print($val["rewards_num"]."個の貢献度報酬が発生中！"); ?></span>
				<span class="reward-points"><?php print("現在の貢献ポイントは".$val["rewards_point"]."PTです！"); ?></span>
			<?php } ?>
		</li>
	<?php } ?>
	</ul>
	</div>
	<?php }else{ ?>
		<div id="exection">現在開催中の貢献度報酬イベントはありません</div>
	<?php } ?>
		

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>
