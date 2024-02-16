<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/pc/ranking.css" rel="stylesheet" type="text/css" />

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

<script>
	$(function() {
        var delaySpeed = 100;
        var fadeSpeed = 1000;
	    $('.percent-width').each(function(i){
			var element					= $(this);
			var display_width			= element.attr("name");
			var width_percent			= display_width + "%";
	        $(this).delay(i*(delaySpeed)).css({width:'0px'}).animate({width:width_percent},fadeSpeed);
	    });
	});
</script>

	<div class="title-nomargin"><?php print($event_title); ?></div>

	<?php if(isset($ranking_list['character_id'])){ ?>
		<div id="ranking-area" class="container-box">
			<ul class="ranking-list">
			<?php
			$count	= count($ranking_list['character_id']);
			if($count > 0){
				for($i=0;$i<$count;$i++){

					if($ranking_list['ranking'][$i] == 1){

						$list_class			= NULL;
						$image_class		= "ranking-image";
						$border_class		= " ranking1";
						$contents_class		= "ranking-contents";
						$name_class			= "ranking-name1";
						$result_class		= "ranking-result";
						$option_class		= "ranking-option1";
						$user_class			= "ranking-user";
						$percent_class		= "ranking-percent";

					}elseif($ranking_list['ranking'][$i] == 2){

						$list_class			= NULL;
						$image_class		= "ranking-image";
						$border_class		= " ranking2";
						$contents_class		= "ranking-contents";
						$name_class			= "ranking-name2";
						$result_class		= "ranking-result";
						$option_class		= "ranking-option2";
						$user_class			= "ranking-user";
						$percent_class		= "ranking-percent";

					}elseif($ranking_list['ranking'][$i] == 3){

						$list_class			= NULL;
						$image_class		= "ranking-image";
						$border_class		= " ranking3";
						$contents_class		= "ranking-contents";
						$name_class			= "ranking-name3";
						$result_class		= "ranking-result";
						$option_class		= "ranking-option3";
						$user_class			= "ranking-user";
						$percent_class		= "ranking-percent";

					}else{

						$list_class			= "list-background";
						$image_class		= "image";
						$border_class		= NULL;
						$contents_class		= "list-contents";
						$name_class			= "ranking-name";
						$result_class		= "ranking-result-normal";
						$option_class		= "option";
						$user_class			= "ranking-user-normal";
						$percent_class		= "ranking-percent-normal";

					}


			?>
					<li class="<?php print($list_class); ?>">
						<a href="/ranking/user/<?php print($data['event_id']); ?>/<?php print($ranking_list['character_id'][$i]); ?>/">
							<div class="<?php print($image_class); ?><?php print($border_class); ?>">
								<?php if(!empty($ranking_list['image'][$i])){ ?>
									<img src="<?php print(HTTP_ATTACHES); ?>/<?php print($ranking_list['image'][$i]); ?>" />
								<?php }else{ ?>
									<img src="/images/pc/noimage.png" />
								<?php } ?>
							</div>
							<div class="<?php print($contents_class); ?>">
								<div class="content">
									<div class="name-line"><span class="<?php print($name_class); ?>"><?php print($ranking_list['name'][$i]); ?></span></div>
									<div class="<?php print($result_class); ?>">
										<span>総合順位</span>&nbsp;&nbsp;<?php print($ranking_list['ranking'][$i]) ?>位&nbsp;&nbsp;&nbsp;
										<span>得票数(％)</span>&nbsp;&nbsp;<?php print($ranking_list['percent'][$i]); ?>％
										<div class="margin10"></div>
										<div class="<?php print($percent_class); ?>"><div class="percent-width" name="<?php print($ranking_list['percent_width'][$i]); ?>"></div></div>
									</div>
								</div>
							</div>
							<div class="<?php print($user_class); ?>"><img src="/images/pc/button/button-ranking-user.png" /></div>
							<div class="clear"></div>
						</a>
					</li>
				<?php } ?>
			<?php } ?>
			</ul>
		</div>

		<?php if(!empty($data['event_id'])){ ?>
			<div id="event-ranking-button"><a href="/event/detail/<?php print($data['event_id']); ?>/">このイベントの詳細を見る</a></div>
		<?php } ?>

	<?php
	}else{
	?>
		<div class="margin15"></div>
		<div id="exection">ランキング情報はありません</div>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>