<?
/********************************************************
**
**	images.php
**	-----------------------------------------------------
**	画像生成CLASS
**	-----------------------------------------------------
**	2010.06.22 takai
**
*********************************************************/

/* CONF FILE */
require_once(dirname(__FILE__).'/../CONF/config.php');


class images
{


	/**************************************************
	**
	**	makeImage
	**	----------------------------------------------
	**	画像生成
	**	----------------------------------------------
	**	$file			: UPLOADされたファイル
	**	$image_data		: WIDTH / SIZE等の設定値
	**	$post_data		: $_POSTで渡された別データ
	**
	**************************************************/

	function makeImage($file,$image_data,$post_data,$user_id=NULL,$thumb_width=NULL){


		# UPLOAD DATA CHECK 
		$file_tmp	= $file['tmp_name'];		// TMP FILE NAME
		$file_name	= $file['name'];			// LOCAL FILE NAME
		$file_size	= $file['size'];			// SIZE
		$file_type	= $file['type'];			// TYPE

		# FILE SIZE CHECK
		if($file_size > $image_data['file_size']){
			return("error");
		}

		# THMUBNAIL
		$thumb_width		= 200;

		# WIDTH / HEIGHT GET
		list($width,$height) = getimagesize($file_tmp); 

		# RESIZE MAIN
		if($width > $image_data['max_width']){
			$new_height = round($height * $image_data['max_width'] / $width);
			$new_width	= $image_data['max_width'];
		}else{
			$new_height = $height;
			$new_width	= $width;
		}

		# RESIZE THUMBNAIL
		if(!empty($thumb_width)){
			if($width > $thumb_width){
				$new_thumb_height 	= round($height * $thumb_width / $width);
				$new_thumb_width	= $thumb_width;
			}else{
				$new_thumb_height 	= $height;
				$new_thumb_width	= $width;
			}
		}

		# FILE TYPE SELECT
		$image_type = "";
		if(is_uploaded_file($file_tmp)){

			# IMAGE TYPE
			if($file_type	== "image/gif"){ $image_type	= ".gif"; }									// GIF
			if($file_type	== "image/png" || $file_type	== "image/x-png"){ $image_type = ".png"; }	// PNG
			if($file_type	== "image/jpeg" || $file_type	== "image/pjpeg"){ $image_type = ".jpg"; }	// JPG

			if($image_type != ""){

				# ATTACHES
				if($user_id){
					$image_file		= date("YmdHis").$user_id.$image_type;
				}else{
					$image_file		= date("YmdHis").$image_type;
				}

				# SAVE PATH
				$save_imagepath		= "../img/".$image_data['file_name']."/".$image_file;

				# IMAGE MOVE
				move_uploaded_file($file_tmp,$save_imagepath);

				# Imagick UPLOAD
				$command			= "/usr/bin/convert ".$save_imagepath." -coalesce -resize ".$new_width."x -deconstruct ".$save_imagepath;
				exec($command);

				# SAVE PATH
				if(!empty($thumb_width)){
					$save_thumb_imagepath		= "../img/".$image_data['file_name']."/thumb/".$image_file;

					# Imagick UPLOAD
					$command_thumb			= "/usr/bin/convert ".$save_imagepath." -coalesce -resize ".$new_thumb_width."x -deconstruct ".$save_thumb_imagepath;
					exec($command_thumb);
				}

				/* 元処理

				# SAVE PATH
				$save_imagepath	= "../img/".$image_data['file_name']."/".$image_file;

				# IMAGE MOVE
				move_uploaded_file($file_tmp,$save_imagepath);

				# IMAGICK
		        $image = new Imagick($save_imagepath);
		        $image->resizeImage($new_width, $new_height,imagick::FILTER_POINT,1);
		        $image->writeImage($save_imagepath);
		        $image->destroy();

				# CREATE MAIN
				#exec( "mogrify -resize " . $new_width . "x" . $new_height . " -quality 100 -sharpen 0.1 " . $save_imagepath );
				*/

			}

		}else{

			return("error");

		}

		return $image_file;


	}

	/**************************************************
	**
	**	makeMultiImage
	**	----------------------------------------------
	**	画像一括生成
	**	----------------------------------------------
	**	$file			: UPLOADされたファイル
	**	$image_data		: WIDTH / SIZE等の設定値
	**	$post_data		: $_POSTで渡された別データ
	**
	**************************************************/

	function makeMultiImage($file,$image_data,$post_data,$user_id=NULL,$thumb_width=NULL){

		$image_file = [];
		$count = count($file['name']);
		for($i=0; $i<$count; $i++){
			# UPLOAD DATA CHECK 
			$file_tmp	= $file['tmp_name'][$i];		// TMP FILE NAME
			$file_name	= $file['name'][$i];			// LOCAL FILE NAME
			$file_size	= $file['size'][$i];			// SIZE
			$file_type	= $file['type'][$i];			// TYPE

			# FILE SIZE CHECK
			if($file_size > $image_data['file_size']){
				return("error");
			}

			# THMUBNAIL
			$thumb_width		= 200;

			# WIDTH / HEIGHT GET
			list($width,$height) = getimagesize($file_tmp); 

			# RESIZE MAIN
			if($width > $image_data['max_width']){
				$new_height = round($height * $image_data['max_width'] / $width);
				$new_width	= $image_data['max_width'];
			}else{
				$new_height = $height;
				$new_width	= $width;
			}

			# RESIZE THUMBNAIL
			if(!empty($thumb_width)){
				if($width > $thumb_width){
					$new_thumb_height 	= round($height * $thumb_width / $width);
					$new_thumb_width	= $thumb_width;
				}else{
					$new_thumb_height 	= $height;
					$new_thumb_width	= $width;
				}
			}

			# FILE TYPE SELECT
			$image_type = "";
			if(is_uploaded_file($file_tmp)){

				# IMAGE TYPE
				if($file_type	== "image/gif"){ $image_type	= ".gif"; }									// GIF
				if($file_type	== "image/png" || $file_type	== "image/x-png"){ $image_type = ".png"; }	// PNG
				if($file_type	== "image/jpeg" || $file_type	== "image/pjpeg"){ $image_type = ".jpg"; }	// JPG

				if($image_type != ""){

					# ATTACHES
					if($user_id){
						$image_file[]		= date("YmdHis").substr(explode(".", microtime(true))[1], 0, 3).$user_id.$image_type;
					}else{
						$image_file[]		= date("YmdHis").substr(explode(".", microtime(true))[1], 0, 3).$image_type;
					}

					# SAVE PATH
					$save_imagepath		= "../img/".$image_data['file_name']."/".$image_file[$i];

					# IMAGE MOVE
					move_uploaded_file($file_tmp,$save_imagepath);

					# Imagick UPLOAD
					$command			= "/usr/bin/convert ".$save_imagepath." -coalesce -resize ".$new_width."x -deconstruct ".$save_imagepath;
					exec($command);

					# SAVE PATH
					if(!empty($thumb_width)){
						$save_thumb_imagepath		= "../img/".$image_data['file_name']."/thumb/".$image_file[$i];

						# Imagick UPLOAD
						$command_thumb			= "/usr/bin/convert ".$save_imagepath." -coalesce -resize ".$new_thumb_width."x -deconstruct ".$save_thumb_imagepath;
						exec($command_thumb);
					}

				}

			}else{

				return("error");

			}
		}

		return $image_file;


	}


	/**************************************************
	**
	**	makeImage
	**	----------------------------------------------
	**	画像生成
	**	----------------------------------------------
	**	$file			: UPLOADされたファイル
	**	$image_data		: WIDTH / SIZE等の設定値
	**
	**************************************************/

	function makeImageCertify($file,$image_data){

		# FILE PATH
		if(file_exists(WEB_ROOT."img/".$image_data['file_name']."/".$file)){
			$file_path	= WEB_ROOT."img/".$image_data['file_name']."/".$file;
		}

		# FILE EMPTY CHECK
		if(!$file_path){
			return FALSE;
		}

		# FILE SIZE
		$file_size	= filesize($file_path);

		# FILE SIZE CHECK
		if($file_size > $image_data['file_size']){
			return FALSE;
		}

		# WIDTH / HEIGHT GET
		list($width,$height) = getimagesize($file_path); 

		# RESIZE MAIN
		if($width > $image_data['max_width']){
			$new_height = round($height * $image_data['max_width'] / $width);
			$new_width	= $image_data['max_width'];
		}else{
			$new_height = $height;
			$new_width	= $width;
		}


		# X SERVER用 Imagick UPLOAD
		$command			= "/usr/bin/convert ".$file_path." -coalesce -resize ".$new_width."x -deconstruct ".$file_path;
		exec($command);


		/* 元処理
		# IMAGICK
        $image = new Imagick($file_path);
        $image->resizeImage($new_width, $new_height,imagick::FILTER_POINT,1);
        $image->writeImage($file_path);
        $image->destroy();
		*/


		return TRUE;

	}



	/**************************************************
	**
	**	makeMovie
	**	----------------------------------------------
	**	3gp / 3g2 / flv / jpeg 生成
	**	----------------------------------------------
	**	$file			: UPLOADされたファイル
	**	$maz_size		: UPLOADできる最大サイズ
	**	$user_id		: USER ID
	**
	**************************************************/

	function makeMovie($file,$max_size,$user_id){

		# UPLOAD DATA CHECK
		$file_tmp	= $file['tmp_name'];		// TMP FILE NAME
		$file_size	= $file['size'];			// SIZE
		$file_type	= $file['type'];			// TYPE

		# FILE EMPTY CHECK
		if(!$file_tmp){
			return("error");
		}

		# FILE SIZE CHECK
		if($file_size > $max_size){
			return("error");
		}

		# FILE URL
		$file_url		= MOVIE_HTTP;

		# FILE ROOT PATH
		#$file_root_path	= WEB_ROOT."movie/";
		$file_root_path	= "/var/www/htdocs/movie/";

		# FILE TITLE
		$file_name		= date("YmdHis").$user_id;


		# FILE TYPE CHECK
		if($file_type === "video/mp4"){
			$file_path	= $file_root_path.$file_name.".mp4";
		}elseif($file_type === "video/3gpp"){
			$file_path	= $file_root_path.$file_name.".3gp";
		}elseif($file_type === "video/3gpp2"){
			$file_path	= $file_root_path.$file_name.".3g2";
		}else{
			return("error");
		}


		# FFmpeg初期設定
		# ffmpegの場所 → ffmpegXをインストールすると、/Contents/Resources/ffmpegにおかれます
	#	$ffmpeg_path	= '/usr/local/bin/ffmpeg';
		#$ffmpeg_path	= '/usr/share/ffmpeg';
		$ffmpeg_path	= 'ffmpeg';

		# FLV NAME
		$flv_name		= $file_name.".flv";
		$output_file_path = $file_root_path.$flv_name;


		# 携帯動画
		if($file_type !== "video/mp4"){

			# MP4 NAME
			$mp4_name		   = $file_name.".mp4";
			$output_file_path2 = $file_root_path.$mp4_name;


			# THUMB OPTION 1
			$thumb_name			= $file_name.".jpg";
			$thumb_path			= $file_root_path. $thumb_name;


			/******************************************
			**	オプション 意味 デフォルト
			**	-i 入力ファイル名
			**	-y 出力ファイルの上書き
			**	-b ビデオのビットレート(b/s) 200kb/s
			**	-r フレームレート 25
			**	-s フレームサイズ「幅x高さ」 160x128
			**	-ab オーディオビットレート(kb/s) 64kb/s
			**	-ac オーディオチャンネル数 1
			**	-ar オーディオサンプリング周波数 44100Hz
			**	-vframes 変換するフレームの数
			**	-ss 指定した位置に移動（秒）
			**	-f 強制フォーマット
			********************************************/

			# THUMB OPTION 2
			$thumb_option2		= ' -f image2 -s qvga -ss 2 -r 1 -t 0:0:0.001 -an  ';

			# OPTION 1
			$command_option1	= ' -y -i ';

			# OPTION 2
			$command_option2	= ' -f flv -vcodec flv -r 25 -b 200k -s qvga -ar 11025 -ab 64k ';

			# OPTION 3
			#$command_option3	= ' -s 480x270 -vcodec libx264 -b 600k -acodec libfaac -ac 2 -ar 48000 -ab 128k -coder 0 -level 13 -nr 50 -threads 2 ';
			$command_option3	= ' -s 480x270 -vcodec libx264 -b 600k -g 150 -qcomp 0.7 -qmin 10 -qmax 51 -qdiff 4 -subq 6 -me_range 16 -i_qfactor 0.714286 -acodec libfaac -ac 2 -ar 48000 -ab 128k -coder 0 -level 13 -nr 50 -threads 2 ';


			#アップロードされたファイルを受け取る
			if(move_uploaded_file($file['tmp_name'],$file_path)){

				# FILE CHMOD -> ROOT FILE
				chmod($file_path,0644);

				# GET MOVIE SIZE
				$SrcStat = stat($file_path);
				$SrcFileSize = ceil($SrcStat[7]/1024);

				# 携帯用動画を3gp/3g2コピー
				if($file_type === "video/3gpp"){
					$copy_from  = $file_root_path.$file_name.".3gp";
					$copy_to 	= $file_root_path.$file_name.".3g2";
				}elseif($file_type === "video/3gpp2"){
					$copy_from  = $file_root_path.$file_name.".3g2";
					$copy_to 	= $file_root_path.$file_name.".3gp";
				}

				# FILE COPY
				$upload_copy	= copy($copy_from,$copy_to);

				# FILE CHMOD -> COPY FILE
				chmod($copy_to,0644);

				# DoCoMoストリーミング用にコンバート system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				#$docomo_file	= system("/usr/local/bin/MP4Box -add ".$file_root_path.$file_name.".3gp -brand mmp4:1 -new ".$file_root_path.$file_name.".3gp");
				$docomo_file	= exec("/usr/local/bin/MP4Box -add ".$file_root_path.$file_name.".3gp -brand mmp4:1 -new ".$file_root_path.$file_name.".3gp");

				# FLV変換 -> 変換スクリプト作成
				$command_line_video = $ffmpeg_path.$command_option1.$file_path.$command_option2.$output_file_path;

				# FLV変換コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				#$last_line_video = system($command_line_video, $retval_video);
				$last_line_video = exec($command_line_video, $retval_video);

				if(file_exists($output_file_path)){
					chmod($output_file_path,0644);
				}else{
					return("error");
				}


				# MP4変換 -> 変換スクリプト作成
				$command_line_mp4	= $ffmpeg_path.$command_option1.$output_file_path.$command_option3.$output_file_path2;

				# MP4変換コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				#$last_line_mp4 = system($command_line_mp4, $retval_mp4);
				$last_line_mp4 = exec($command_line_mp4, $retval_mp4);

				if(file_exists($output_file_path2)){
					chmod($output_file_path,0644);
				}else{
					return("error");
				}

				# MAKE THUMB -> サムネイル作成コマンド生成
				$command_line_img	= $ffmpeg_path.$command_option1.$output_file_path.$thumb_option2.$thumb_path;

				# サムネイル作成コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				#$last_line_img	= system($command_line_img, $retval_img);
				$last_line_img	= exec($command_line_img, $retval_img);


			}


		# iPhone動画
		}else{


			# 3GP NAME
			$m3gp_name		   = $file_name.".3gp";
			$output_file_path2 = $file_root_path.$m3gp_name;

			# 3G2 NAME
			$m3g2_name		   = $file_name.".3g2";
			$output_file_path3 = $file_root_path.$m3g2_name;

			# THUMB OPTION 1
			$thumb_name			= $file_name.".jpg";
			$thumb_path			= $file_root_path. $thumb_name;


			/******************************************
			**	オプション 意味 デフォルト
			**	-i 入力ファイル名
			**	-y 出力ファイルの上書き
			**	-b ビデオのビットレート(b/s) 200kb/s
			**	-r フレームレート 25
			**	-s フレームサイズ「幅x高さ」 160x128
			**	-ab オーディオビットレート(kb/s) 64kb/s
			**	-ac オーディオチャンネル数 1
			**	-ar オーディオサンプリング周波数 44100Hz
			**	-vframes 変換するフレームの数
			**	-ss 指定した位置に移動（秒）
			**	-f 強制フォーマット
			********************************************/

			# THUMB OPTION 2
			$thumb_option2		= ' -f image2 -s qvga -ss 2 -r 1 -t 0:0:0.001 -an  ';

			# OPTION 1
			$command_option1	= ' -y -i ';

			# OPTION 2
			$command_option2	= ' -vcodec mpeg4 -b 64k -s qcif -r 15 -acodec libamr_nb -ab 12200 -ar 8000 -ac 1 -flags bitexact ';

			# OPTION 3
			$command_option3	= ' -f flv -vcodec flv -r 25 -b 200k -s qvga -ar 11025 -ab 64k ';


			#アップロードされたファイルを受け取る
			if(move_uploaded_file($file['tmp_name'],$file_path)){

				# 3G2変換 -> 変換スクリプト作成
				$command_line_3gp	= $ffmpeg_path.$command_option1.$file_path.$command_option2.$output_file_path2;

				# 3G2変換コマンド実行
				$last_line_3gp		= exec($command_line_3gp, $retval_video);

				# FILE CHMOD -> ROOT FILE
				if(file_exists($output_file_path2)){
					chmod($output_file_path2,0644);
				}else{
					return("error");
				}

				# GET MOVIE SIZE
				$SrcStat			= stat($output_file_path2);
				$SrcFileSize		= ceil($SrcStat[7]/1024);

				# 3G2 -> 3GP
				$copy_from			= $file_root_path.$file_name.".3gp";
				$copy_to			= $file_root_path.$file_name.".3g2";

				# FILE COPY
				$upload_copy		= copy($copy_from,$copy_to);

				# FILE CHMOD -> COPY FILE
				chmod($copy_to,0644);

				# DoCoMoストリーミング用にコンバート system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				$docomo_file		= exec("/usr/local/bin/MP4Box -add ".$file_root_path.$file_name.".3gp -brand mmp4:1 -new ".$file_root_path.$file_name.".3gp");

				# FLV変換 -> 変換スクリプト作成
				$command_line_video = $ffmpeg_path.$command_option1.$file_path.$command_option3.$output_file_path;

				# FLV変換コマンド実行
				$last_line_video 	= exec($command_line_video, $retval_video);

				if(file_exists($output_file_path)){
					chmod($output_file_path,0644);
				}else{
					return("error");
				}

				# MAKE THUMB -> サムネイル作成コマンド生成
				$command_line_img	= $ffmpeg_path.$command_option1.$output_file_path.$thumb_option2.$thumb_path;

				# サムネイル作成コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				$last_line_img	= exec($command_line_img, $retval_img);

			}


		}

		return $file_name;


	}




	/**************************************************
	**
	**	makeMovieCertify
	**	----------------------------------------------
	**	3gp / 3g2 / flv / jpeg 生成
	**	----------------------------------------------
	**	$file			: DIRCTORY内のデータ
	**	$maz_size		: UPLOADできる最大サイズ
	**	$user_id		: USER ID
	**
	**************************************************/

	function makeMovieCertify($file_name,$max_size,$user_id){

		# FILE PATH
		if(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3gp")){
			$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3gp";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3gpp")){
			$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3gpp";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3g2")){
			$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3g2";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3gpp2")){
			$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3gpp2";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".mp4")){
			$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".mp4";
			$iphone		= "on";
		}

		# FILE EMPTY CHECK
		if(!$file_path){
			return FALSE;
		}

		# FILE SIZE
		$file_size	= filesize($file_path);

		# FILE SIZE CHECK
		if($file_size > $max_size){
			return FALSE;
		}

		# FILE URL
		$file_url		= MOVIE_HTTP;

		# FILE ROOT PATH
	#	$file_root_path	= WEB_ROOT."movie/";
		$file_root_path	= "/var/www/htdocs/movie/";


		# FFmpeg初期設定
		# ffmpegの場所 → ffmpegXをインストールすると、/Contents/Resources/ffmpegにおかれます
	#	$ffmpeg_path	= '/usr/local/bin/ffmpeg';
		$ffmpeg_path	= '/usr/share/ffmpeg';

		# FLV NAME
		$flv_name		= $file_name.".flv";
		$output_file_path = $file_root_path.$flv_name;


		if(!$iphone){

			# MP4 NAME
			$mp4_name		   = $file_name.".mp4";
			$output_file_path2 = $file_root_path.$mp4_name;


			/******************************************
			**	オプション 意味 デフォルト
			**	-i 入力ファイル名
			**	-y 出力ファイルの上書き
			**	-b ビデオのビットレート(b/s) 200kb/s
			**	-r フレームレート 25
			**	-s フレームサイズ「幅x高さ」 160x128
			**	-ab オーディオビットレート(kb/s) 64kb/s
			**	-ac オーディオチャンネル数 1
			**	-ar オーディオサンプリング周波数 44100Hz
			**	-vframes 変換するフレームの数
			**	-ss 指定した位置に移動（秒）
			**	-f 強制フォーマット
			********************************************/

			# OPTION 1
			$command_option1	= ' -y -i ';

			# OPTION 2
			$command_option2	= ' -f flv -vcodec flv -r 25 -b 200k -s qvga -ar 11025 -ab 64k ';

			# OPTION 3
			$command_option3	= ' -s 480x270 -vcodec libx264 -b 600k -acodec libfaac -ac 2 -ar 48000 -ab 128k -coder 0 -level 13 -nr 50 -threads 2 ';

			# THUMB OPTION 1
			$thumb_name		= $file_name.".jpg";
			$thumb_path		= $file_root_path. $thumb_name;

			# THUMB OPTION 2
			$thumb_option2	= ' -f image2 -s qvga -ss 2 -r 1 -t 0:0:0.001 -an  ';

			# FILE CHMOD -> ROOT FILE
			#chmod($file_path,0644);

			# GET MOVIE SIZE
			$SrcStat = stat($file_path);
			$SrcFileSize = ceil($SrcStat[7]/1024);


			# 携帯用動画を3gp/3g2コピー
			$copy_from  = $file_path;
			$copy_to 	= $file_root_path.$file_name.".3g2";
			$copy_to2 	= $file_root_path.$file_name.".3gp";

			# FILE COPY
			$upload_copy	= copy($copy_from,$copy_to);
			$upload_copy2	= copy($copy_from,$copy_to2);

			# FILE CHMOD -> COPY FILE
			chmod($copy_to,0644);
			chmod($copy_to2,0644);

			# DoCoMoストリーミング用にコンバート system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
			#$docomo_file	= system("/usr/local/bin/MP4Box -add ".$file_root_path.$file_name.".3gp -brand mmp4:1 -new ".$file_root_path.$file_name.".3gp");
			$docomo_file	= exec("/usr/local/bin/MP4Box -add ".$file_root_path.$file_name.".3gp -brand mmp4:1 -new ".$file_root_path.$file_name.".3gp");

			# FLV変換 -> 変換スクリプト作成
			$command_line_video = $ffmpeg_path.$command_option1.$file_path.$command_option2.$output_file_path;

			#FLV変換コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
			#$last_line_video = system($command_line_video, $retval_video);
			$last_line_video = exec($command_line_video, $retval_video);

			if(file_exists($output_file_path)){
				chmod($output_file_path,0644);
			}else{
				return FALSE;
			}

			# MP4変換 -> 変換スクリプト作成
			$command_line_mp4	= $ffmpeg_path.$command_option1.$output_file_path.$command_option3.$output_file_path2;

			# MP4変換コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
			#$last_line_mp4 = system($command_line_mp4, $retval_mp4);
			$last_line_mp4 = exec($command_line_mp4, $retval_mp4);

			if(file_exists($output_file_path2)){
				chmod($output_file_path,0644);
			}else{
				return("error");
			}

			# MAKE THUMB -> サムネイル作成コマンド生成 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
			$command_line_img	= $ffmpeg_path.$command_option1.$output_file_path.$thumb_option2.$thumb_path;

			# サムネイル作成コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
			#$last_line_img	= system($command_line_img, $retval_img);
			$last_line_img	= exec($command_line_img, $retval_img);


		# iPhone 動画
		}else{


			# 3GP NAME
			$m3gp_name		   = $file_name.".3gp";
			$output_file_path2 = $file_root_path.$m3gp_name;

			# 3G2 NAME
			$m3g2_name		   = $file_name.".3g2";
			$output_file_path3 = $file_root_path.$m3g2_name;

			# THUMB OPTION 1
			$thumb_name			= $file_name.".jpg";
			$thumb_path			= $file_root_path. $thumb_name;


			/******************************************
			**	オプション 意味 デフォルト
			**	-i 入力ファイル名
			**	-y 出力ファイルの上書き
			**	-b ビデオのビットレート(b/s) 200kb/s
			**	-r フレームレート 25
			**	-s フレームサイズ「幅x高さ」 160x128
			**	-ab オーディオビットレート(kb/s) 64kb/s
			**	-ac オーディオチャンネル数 1
			**	-ar オーディオサンプリング周波数 44100Hz
			**	-vframes 変換するフレームの数
			**	-ss 指定した位置に移動（秒）
			**	-f 強制フォーマット
			********************************************/

			# THUMB OPTION 2
			$thumb_option2		= ' -f image2 -s qvga -ss 2 -r 1 -t 0:0:0.001 -an  ';

			# OPTION 1
			$command_option1	= ' -y -i ';

			# OPTION 2
			$command_option2	= ' -vcodec mpeg4 -b 64k -s qcif -r 15 -acodec libamr_nb -ab 12200 -ar 8000 -ac 1 -flags bitexact ';

			# OPTION 3
			$command_option3	= ' -f flv -vcodec flv -r 25 -b 200k -s qvga -ar 11025 -ab 64k ';


			#アップロードされたファイルを受け取る
			if(move_uploaded_file($file['tmp_name'],$file_path)){

				# 3G2変換 -> 変換スクリプト作成
				$command_line_3gp	= $ffmpeg_path.$command_option1.$file_path.$command_option2.$output_file_path2;

				# 3G2変換コマンド実行
				$last_line_3gp		= exec($command_line_3gp, $retval_video);

				# FILE CHMOD -> ROOT FILE
				if(file_exists($output_file_path2)){
					chmod($output_file_path2,0644);
				}else{
					return("error");
				}

				# GET MOVIE SIZE
				$SrcStat			= stat($output_file_path2);
				$SrcFileSize		= ceil($SrcStat[7]/1024);

				# 3G2 -> 3GP
				$copy_from			= $file_root_path.$file_name.".3gp";
				$copy_to			= $file_root_path.$file_name.".3g2";

				# FILE COPY
				$upload_copy		= copy($copy_from,$copy_to);

				# FILE CHMOD -> COPY FILE
				chmod($copy_to,0644);

				# DoCoMoストリーミング用にコンバート system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				$docomo_file		= exec("/usr/local/bin/MP4Box -add ".$file_root_path.$file_name.".3gp -brand mmp4:1 -new ".$file_root_path.$file_name.".3gp");

				# FLV変換 -> 変換スクリプト作成
				$command_line_video = $ffmpeg_path.$command_option1.$file_path.$command_option3.$output_file_path;

				# FLV変換コマンド実行
				$last_line_video 	= exec($command_line_video, $retval_video);

				if(file_exists($output_file_path)){
					chmod($output_file_path,0644);
				}else{
					return("error");
				}

				# MAKE THUMB -> サムネイル作成コマンド生成
				$command_line_img	= $ffmpeg_path.$command_option1.$output_file_path.$thumb_option2.$thumb_path;

				# サムネイル作成コマンド実行 system関数だと処理が出力されてしまう為exec関数に切り替え 2012-03-07 takai
				$last_line_img	= exec($command_line_img, $retval_img);

			}


		}


		return TRUE;


	}



	/**************************************************
	**
	**	makeImageData
	**	----------------------------------------------
	**	UPLOAD IMAGE DATA SETTING / MAKE
	**
	**************************************************/

	function makeImageData($file_name,$file_size,$max_width){

		$image_data['file_name']	= $file_name;
		$image_data['file_size']	= $file_size;
		$image_data['max_width']	= $max_width;
		$image_data['display_size']	= $file_size / 10000;

		return $image_data;

	}


	/**************************************************
	**
	**	getPreMovieFile
	**	----------------------------------------------
	**	ファイルサーバーにUPLOADされた未承認動画の表示タグ
	**	----------------------------------------------
	**	$file			: UPLOADされたファイル
	**
	**************************************************/

	function getPreMovieFile($movie_file){

		# FILE PATH
		if(file_exists(WEB_ROOT."movie/not_certify/".$movie_file.".3gp")){
			$movie_data	= MOVIE_HTTP."not_certify/".$movie_file.".3gp";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$movie_file.".3gpp")){
			$movie_data	= MOVIE_HTTP."not_certify/".$movie_file.".3gpp";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$movie_file.".3g2")){
			$movie_data	= MOVIE_HTTP."not_certify/".$movie_file.".3g2";
		}elseif(file_exists(WEB_ROOT."movie/not_certify/".$movie_file.".3gpp2")){
			$movie_data	= MOVIE_HTTP."not_certify/".$movie_file.".3gpp2";
		}


		/***************************************************
		**
		**	表示生成
		**
		****************************************************/
		# MAKE MOVIE : DISPLAY / HIDDEN
		$images['display']	.= "<embed src=\"".$movie_data."\" type=\"video/3gpp\" width=\"150\" height=\"150\" autostart=\"false\" autoplay=\"false\"></embed>";
		$images['hidden']	.= "<input type=\"hidden\" name=\"images[]\" value=\"".$movie_file."\" />\n";

		return $images;


	}



	/************************************************
	**
	**	deleteFileData
	**	---------------------------------------------
	**	imgディレクトリ内画像削除
	**
	************************************************/

	function deleteAttacheData($file_name,$category,$type){

		# PHOTO
		if($category == 1){

			$file_path	= WEB_ROOT."img/attaches/".$file_name;

		# MOVIE CERTIFY
		}elseif($category == 2 && $type == 1){

			if(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3gp")){
				$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3gp";
			}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3gpp")){
				$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3gpp";
			}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3g2")){
				$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3g2";
			}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".3gpp2")){
				$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".3gpp2";
			}elseif(file_exists(WEB_ROOT."movie/not_certify/".$file_name.".mp4")){
				$file_path	= WEB_ROOT."movie/not_certify/".$file_name.".mp4";
			}

		# MOVIE DELETE
		}elseif($category == 2 && $type == 2){

			if(file_exists(WEB_ROOT."movie/".$file_name.".3gp")){
				$file_path1	= WEB_ROOT."movie/".$file_name.".3gp";
			}
			if(file_exists(WEB_ROOT."movie/".$file_name.".3g2")){
				$file_path2	= WEB_ROOT."movie/".$file_name.".3g2";
			}
			if(file_exists(WEB_ROOT."movie/".$file_name.".jpg")){
				$file_path3	= WEB_ROOT."movie/".$file_name.".jpg";
			}
			if(file_exists(WEB_ROOT."movie/".$file_name.".flv")){
				$file_path4	= WEB_ROOT."movie/".$file_name.".flv";
			}
			if(file_exists(WEB_ROOT."movie/".$file_name.".mp4")){
				$file_path4	= WEB_ROOT."movie/".$file_name.".mp4";
			}

		}

		if($file_path){

			if(is_dir($file_path)){
				return FALSE;
			}

			if(!is_writable($file_path)){
				#return FALSE;
			}

			if(is_link($file_path)){
				fileDelete(realpath($file_path));
			}

			if(!@unlink($file_path)){
				return FALSE;
			}

		}

		if($category == 2 && $type == 2){

			if($file_path1){

				if(is_dir($file_path1)){
					return FALSE;
				}

				if(!is_writable($file_path1)){
					#return FALSE;
				}

				if(is_link($file_path1)){
					fileDelete(realpath($file_path1));
				}

				if(!@unlink($file_path1)){
					return FALSE;
				}

			}

			if($file_path2){

				if(is_dir($file_path2)){
					return FALSE;
				}

				if(!is_writable($file_path2)){
					#return FALSE;
				}

				if(is_link($file_path2)){
					fileDelete(realpath($file_path2));
				}

				if(!@unlink($file_path2)){
					return FALSE;
				}

			}

			if($file_path3){

				if(is_dir($file_path3)){
					return FALSE;
				}

				if(!is_writable($file_path3)){
					#return FALSE;
				}

				if(is_link($file_path3)){
					fileDelete(realpath($file_path3));
				}

				if(!@unlink($file_path3)){
					return FALSE;
				}

			}

			if($file_path4){

				if(is_dir($file_path4)){
					return FALSE;
				}

				if(!is_writable($file_path4)){
					#return FALSE;
				}

				if(is_link($file_path4)){
					fileDelete(realpath($file_path));
				}

				if(!@unlink($file_path4)){
					return FALSE;
				}

			}

		}


		return TRUE;

	}

}

?>
