<?php
/****************************
**
**	このページのjavascript処理ファイル
**
*****************************/
?>

<script src="/common/js/phaser2/build/phaser.min.js"></script>
<style>
#phaser-example {
position: absolute;
left:0px;
top:0px;
opacity: 0.75;
z-index:10000;
};
</style>
	
<script>
//var game = new Phaser.Game(800, 600, Phaser.AUTO, 'phaser-example', { preload: preload, create: create });
var game = new Phaser.Game(800, 600, Phaser.AUTO, 'phaser-example', { create: create, update: update });

var filter;
var sprite;

function create() {

    //  From http://glslsandbox.com/e#16153.0

    var fragmentSrc = [

        "precision mediump float;",

        "uniform float     time;",
        "uniform vec2      resolution;",
        "uniform vec2      mouse;",

        "#define MAX_ITER 4",

        "void main( void )",
        "{",
            "vec2 v_texCoord = gl_FragCoord.xy / resolution;",

            "vec2 p =  v_texCoord * 8.0 - vec2(20.0);",
            "vec2 i = p;",
            "float c = 1.0;",
            "float inten = .05;",

            "for (int n = 0; n < MAX_ITER; n++)",
            "{",
                "float t = time * (1.0 - (3.0 / float(n+1)));",

                "i = p + vec2(cos(t - i.x) + sin(t + i.y),",
                "sin(t - i.y) + cos(t + i.x));",

                "c += 1.0/length(vec2(p.x / (sin(i.x+t)/inten),",
                "p.y / (cos(i.y+t)/inten)));",
            "}",

            "c /= float(MAX_ITER);",
            "c = 1.5 - sqrt(c);",

            "vec4 texColor = vec4(0.0, 0.01, 0.015, 1.0);",

            "texColor.rgb *= (1.0 / (1.0 - (c + 0.05)));",

            "gl_FragColor = texColor;",
        "}"
    ];

    filter = new Phaser.Filter(game, null, fragmentSrc);
    filter.setResolution(800, 600);

    sprite = game.add.sprite();
    sprite.width = 800;
    sprite.height = 600;

    sprite.filters = [ filter ];

}

function update() {

    filter.update(game.input.activePointer);

}

</script>

<script src="/common/js/benefits.script.js" type="text/javascript" ></script>
<script src="/common/js/benefits.function.js" type="text/javascript" ></script>

<style>

</style>
<div width="100%" height="100%" id="phaser-example"></div>
<?php
/********************************
**
**	特典プレゼントorエラー表示エリア
**
*********************************/
?>
<div id="dialog-benefits"></div>

<?php /* CONTAINER */ ?>
<div id="container">
<?php /* CONTAINER */ ?>

	<div class="title-nomargin">特典コードでプレゼントをゲット！！</div>

	<?php
	/****************************************
	**
	**	バナー
	**
	*****************************************/
	if(isset($banner_list['id'])){
		$count	= count($banner_list['id']);
		if($count > 0){
			for($i=0;$i<$count;$i++){
		?>
				<div class="banner">
					<img src="<?php print(HTTP_BANNER_IMAGE); ?>/<?php print($banner_list['image'][$i]); ?>" border="0" />
				</div>
			<?php } ?>
		<?php } ?>
	<?php } ?>



	<div class="benefits-list">
		<ul>
			<li>
				<form method="post">
				<input type="hidden" name="site_cd" id="site_cd" value="<?php print($members_data['site_cd']);?>" />
				<input type="hidden" name="user_id" id="user_id" value="<?php print($members_data['id']);?>" />
				<section id="form-benefits">
					<ul>
						<li>
							<p>特典コードを入力してください</p>
							<label><input type="text" name="bcode" id="bcode" maxlength="12" value="" /></label>
						</li>
					</ul>
					<div id="submit-area-benefits"><input type="button" id="button-benefits" value="&nbsp;"/></div>
				</section>
				</form>
			</li>
		</ul>
	</div>

	
	
<?php /* CONTAINER */ ?>
</div>
<?php /* CONTAINER */ ?>