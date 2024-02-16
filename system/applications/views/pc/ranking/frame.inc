<?php
/************************************************
**
**	INCLUED HTML HEADER
**	---------------------------------------------
**	HTML VIEWS FILE READING
**
************************************************/
include_once(dirname(__FILE__)."/../templates/html_header.inc");
?>


<?php
/****************************
**
**	このページはこのcssでデザインしてるよ
**
*****************************/
?>
<link href="/common/css/pc/ranking-frame.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" src="/plugins/malihuCustomScrollbar/jquery.mCustomScrollbar.js"></script>
<link href="/plugins/malihuCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
$(function() {

	/* スクロールバー */
	$("#container").mCustomScrollbar({
		mouseWheelPixels: 500
	});

});
</script>

<?php /* CONTAINER */ ?>
<div id="container" data-mcs-theme="minimal-dark">
<?php /* CONTAINER */ ?>

	<section class="navigationScroll">

		<div class="frame-title"><?php print($event_title); ?></div>

		<?php if(isset($ranking_list['character_id'])){ ?>
			<div id="ranking-area">
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

							$list_class			= NULL;
							$image_class		= "normail-image";
							$border_class		= NULL;
							$contents_class		= "normal-contents";
							$name_class			= "ranking-name";
							$result_class		= "ranking-result-normal";
							$option_class		= "option";
							$user_class			= "ranking-user-normal";
							$percent_class		= "ranking-percent-normal";

						}


				?>
						<li class="<?php print($list_class); ?>">
							<?php /*<a href="/ranking/user/<?php print($event_id); ?>/<?php print($ranking_list['character_id'][$i]); ?>/" target="_parent"> */ ?>

							<?php if($ranking_list['media_flg'][$i] != 1){ ?>
								<a href="/mail/detail/<?php print($ranking_list['character_id'][$i]); ?>/" target="_parent">
							<?php } ?>
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
										</div>
									</div>
								</div>
								<div class="clear"></div>
							<?php if($ranking_list['media_flg'][$i] != 1){ ?>
								</a>
							<?php } ?>
						</li>
					<?php } ?>
				<?php } ?>
				</ul>

			</div>

		</section>

		<?php if(!empty($event_id)){ ?>
			<div id="event-ranking-button"><a href="/event/detail/<?php print($event_id); ?>/" target="_parent">このイベントの詳細を見る</a></div>
		<?php } ?>


	<?php
	}else{
	?>
		<div class="margin15"></div>
		<div id="exection" style="width: 300px; margin: 0 0 0 10px;">ランキング情報はありません</div>
	<?php } ?>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>