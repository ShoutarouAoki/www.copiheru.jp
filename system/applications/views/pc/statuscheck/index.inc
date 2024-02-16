

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title">アクセス状態チェック</div>

	<form action ="/<?php print($directory); ?>/first/" method="post">
	    <div id="form">
			<div id="check-comment">まずにじよめIDを記入し、次へを押して下さい</div>
			<ul>
				<li>
					<p>にじよめID</p>
					<label><input type="text" name="user_id" class="text_num" /></label>
				</li>
				<li><input type="submit" id="button-next" value="&nbsp;" /></li>
			</ul>
		</div>
	</form>

<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>

