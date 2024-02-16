<?
/********************************************************
**
**	filesets.php
**	-----------------------------------------------------
**	filesets CLASS
**	-----------------------------------------------------
**	inc.ファイルへ出力するhtmlタグも生成してます。
**	キャンペーンが絡んで部分部分他のファイルで呼び出す為
**	-----------------------------------------------------
**	2010.06.02 takai
**
*********************************************************/

/* REQUIRE CLASS FILE */
require_once(dirname(__FILE__).'/../class/campaignsets.php');
require_once(dirname(__FILE__).'/../class/domainlists.php');
require_once(dirname(__FILE__).'/../class/images.php');
require_once(dirname(__FILE__).'/../emoji/lib/mobile_class_8.php');

class filesets
{

	# VAR
	private $name;
	private $image_list;
	private $category_select;
	private $image_dir;
	private $image_flg;
	private $textarea_rows;
	private $sex;
	private $from_name;
	private	$replace;


	/**************************************************
	**
	**	SETTING PUBLIC / PROTECTED / PRIVATE
	**	----------------------------------------------
	**	CONSTRUCT / DESTRUCT
	**
	**	@database接続クラス	読み込み
	**	@HTMLクラス			読み込み
	**	@site_cd			読み込み
	**	@$sec_data			読み込み
	**	@$form_sec_data		読み込み
	**
	**************************************************/

	# CONSTRUCT
	public function __construct($database,$html_class,$site_cd,$post_data=NULL){

		global	$sec_data;
		global	$form_sec_data;
		global	$emoji_obj;

		$this->db			= $database;
		$this->html			= $html_class;
		$this->site_cd		= $site_cd;
		$this->post_data	= $post_data;
		$this->table		= "filesets";
		$this->sec			= $sec_data;
		$this->sec_form		= $form_sec_data;
		$this->emoji_obj	= $emoji_obj;

		# お知らせメール用 TYPE
		if($this->post_data['category'] == "0" && !$this->post_data['type']){
			$this->post_data['type']	= 1;
		}

		# 登録リメールはsexが必要
		if($this->post_data['category'] == 17 && $this->post_data['sex'] == ""){
			$this->post_data['sex'] = 1;
		}

		# HTML DEFAULTはDEFAULT categoryが必要
		if($this->post_data['file_type'] == 2 && !$this->post_data['category']){
			$this->post_data['category'] = 1;
		}

		# HTML DEFAULTはDEFAULT sexが必要
		if($this->post_data['file_type'] == 2 && $this->post_data['sex'] == ""){
			$this->post_data['sex'] = 1;
		}

		# CAMPAIGN FLG 引き回し
		if($this->post_data['campaign']){
			$this->sec		.= "&campaign=".$this->post_data['campaign'];
			$this->sec_form	.= "<input type=\"hidden\" name=\"campaign\" value=\"".$this->post_data['campaign']."\" />\n";
		}

		# DOMAIN FLG 引き回し
		if($this->post_data['domain_flg']){
			$this->sec		.= "&domain_flg=".$this->post_data['domain_flg'];
			$this->sec_form .= "<input type=\"hidden\" name=\"domain_flg\" value=\"".$this->post_data['domain_flg']."\" />\n";
		}

    }

	# DESTRUCT
	function __destruct(){

    }


	/**************************************************
	**
	**	getNavigation1
	**	----------------------------------------------
	**	setting_file.phpの左側ナビゲーション1
	**
	**	リンクメニュー
	**
	**************************************************/

	public function getNavigation1(){

		# MAIL SETTING
		if($this->post_data['file_type'] == 1){

			# CAMPAIGN
			if($this->post_data['campaign']){
				$this->getAddCategory();
				$campaignsets	= new campaignsets();
				$result			= $campaignsets->getNavigation();
				$this->name		= "キャンペーン";
			# DOMAIN FLG
			}elseif($this->post_data['domain_flg']){
				$domainlists	= new domainlists();
				$result			= $domainlists->getDomainNavigation($this->site_cd);
				$this->name		= $domainlists->dispDomainFlg($this->site_cd,$this->post_data['domain_flg']);
				$result	.= $this->getMailMenu();
				$result	.= $this->getRegistForm();
			# DEFAULT
			}else{
				$result	= $this->getMailMenu();
			}

		# HTML SETTING
		}elseif($this->post_data['file_type'] == 2){

			# CAMPAIGN
			if($this->post_data['campaign']){
				$this->getAddCategory();
				$campaignsets	= new campaignsets();
				$result			= $campaignsets->getNavigation();
				$this->name		= "キャンペーン";
			# DOMAIN FLG
			}elseif($this->post_data['domain_flg']){
				$domainlists	= new domainlists();
				$result			= $domainlists->getDomainNavigation($this->site_cd);
				$this->sub_name	= $domainlists->dispDomainFlg($this->site_cd,$this->post_data['domain_flg']);
				$result	 .= $this->getHtmlMenu();
			}else{
				$result	 = $this->getHtmlMenu();
			}
			
		}

		return $result;

	}



	/**************************************************
	**
	**	getNavigation2
	**	----------------------------------------------
	**	setting_file.phpの左側ナビゲーション2
	**	----------------------------------------------
	**	@%変換
	**	@追加フォーム
	**	@画像表示
	**
	**************************************************/

	public function getNavigation2(){

		$hidden	.= "<input type=\"hidden\" name=\"purpose\" value=\"1\" />\n";
		$hidden	.= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";

		# FILE追加 お知らせMAIL設定追加型のみ
		if($this->post_data['file_type'] == 1 && $this->post_data['list']){

			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= $hidden;
			$result	.= "<input type=\"hidden\" name=\"list\" value=\"1\" />\n";
			$result	.= "<div class=\"title_sub\">送信メール文追加</div>\n";
			$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\" width=\"80\">カテゴリー</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= $this->getMailCategory();
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\" width=\"80\">設定名</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "<input type=\"text\" name=\"name\" size=\"25\" />\n";
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\">追加</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= $this->html->htmlSubmit("追加",1);
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "</table>\n";
			$result	.= "</form>\n";

		# FILE追加 HTML設定追加型のみ
		}elseif($this->post_data['file_type'] == 2 && $this->post_data['list']){

			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= $hidden;
			$result	.= "<input type=\"hidden\" name=\"category\" value=\"".$this->post_data['category']."\" />\n";
			$result	.= "<input type=\"hidden\" name=\"list\" value=\"1\" />\n";
			$result	.= "<div class=\"title_sub\">".$this->name." HTML文言追加</div>\n";
			$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\" width=\"80\">設定名</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "<input type=\"text\" name=\"name\" size=\"25\" />\n";
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\">追加</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= $this->html->htmlSubmit("追加",1);
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "</table>\n";
			$result	.= "</form>\n\n";

		}

		# %変換はお知らせMAIL設定のみ
		if($this->post_data['file_type'] == 1){

			global	$mail_base_replace;
			global	$mail_delivery_replace;
			global	$mail_remail_replace;
			global	$mail_replace;

			# REMAIL系
			if($this->post_data['category'] >= 1 && $this->post_data['category'] <= 3){
				$replace_array	= $mail_remail_replace;
			# INFO同報 特殊
			}elseif($this->post_data['category'] == 4){
				$replace_array	= $mail_replace;
			}elseif($this->post_data['list'] && $this->replace == 0){
				$replace_array	= $mail_base_replace;
			}elseif($this->post_data['list'] && $this->replace == 1){
				$replace_array	= $mail_delivery_replace;
			}elseif($this->post_data['fixed']){
				$replace_array	= $mail_delivery_replace;
			}else{
				$replace_array	= $mail_base_replace;
			}

			$this->name		= $this->getPageTitle();

			# % REPLACE
			ob_start();
			$this->html->getDeliveryReplace($replace_array,$this->name);
			$result	.= ob_get_contents();
			ob_end_clean();

			# MAX
			if($this->post_data['category'] == 8){

				$result	.= "<div class=\"title_sub\">掲示板キャラURL</div>\n";
				$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$result	.= "<tr>\n";
				$result	.= "<td class=\"table_title\" width=\"80\">URL</td>\n";
				$result	.= "<td class=\"table_contents\">\n";
				$result	.= "<input type=\"text\" size=\"35\" value=\"%url_bbs%id~ここにキャラID\" readonly />\n";
				$result	.= "</td>\n";
				$result	.= "</tr>\n";
				$result	.= "</table>\n";

			}


		}

		# IMAGE LIST
		if($this->post_data['category']){
			$images		= new images();
			$image_list	= $images->getImageList($this->site_cd,$this->post_data['file_type'],$this->post_data['category'],"0");
			if($image_list){
			$result	.= "<div class=\"title_sub\">".$this->name." 画像一覧</div>\n";
			$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$result	.= $image_list;
			$result	.= "</table>\n\n";
			}
		}

		return	$result;

	}



	/**************************************************
	**
	**	getFileContents
	**	----------------------------------------------
	**	setting_file.phpのメインコンテンツ
	**
	**************************************************/

	public function getFileContents(){


		/*****************************************
		**
		**	追加型
		**	--------------------------------------
		**	お知らせMAIL設定 & HTML設定
		**	--------------------------------------
		**	@file_tyoe	= 1 or 2
		**	@list		= 1
		**
		**
		******************************************/

		if($this->post_data['list']){

			# IMAGES CLASS
			$images			= new images();

			# CAMPAIGN CLASS
			$campaignsets	= new campaignsets($this->db,$this->html_class,$this->site_cd,NULL);

			# IMAGE DIR
			if(!$this->image_dir && $this->post_data['file_type'] == 1){
				$this->image_dir	= "mail";
			}elseif(!$this->image_dir && $this->post_data['file_type'] == 2){
				$this->image_dir	= "campaign";
				$this->image_flg	= 1;
			}

			# TITLE
			$result .= "<div class=\"title_sub\">\n";
			$result .= $this->getPageTitle()." &nbsp;&nbsp;\n";

			# INFO 同報
			if($this->post_data['file_type'] == 1){
				if($this->post_data['category'] == 4 ||  $this->post_data['category'] == 7){
					$result	.= "<a href=\"/setting_db/setting_image.php?".$this->sec."&file_type=".$this->post_data['file_type']."&category=".$this->post_data['category']."\">HTML画像UPLOADはこちら</a>&nbsp;&nbsp;";
				}
			}


			$result .= "<input type=\"button\" value=\"全詳細表示\" class=\"button\" onclick=\"display_controll_all();\" />\n";
			$result .= "</div>\n\n";

			# INFO同報ベタで注意書き
			if($this->post_data['file_type'] == 1 && $this->post_data['category'] == 4){
			$result	.= "<div class=\"style_pink\" align=\"center\">";
			$result	.= "INFO同報は『お知らせメール』ではありません。実際にメールBOXに入るメールですのでHTMLタグの使用が可能です(本文のみ)</div><br />\n";
			}


			# GET FILESETS DATA
			$rtn	= $this->getFilesetsData(NULL,NULL,$this->post_data['category'],NULL,NULL,NULL);
			$this->db->errorDb("",$db->errno,__FILE__,__LINE__);
			if($db_err){ print($db_err); exit; }

			# HTML RETRY FLG
			if($this->post_data['file_type'] == 1 && $this->post_data['category'] == 7){
				$html_retry_flg		= "on";
				$html_retry_title	= "(HTML RETRY)";
			}

			$i=1;
			while($data = $this->db->fetchAssoc($rtn)){

				$hidden	 = NULL;
				$hidden  = "<input type=\"hidden\" name=\"file_id\" value=\"".$data['id']."\" />\n";
				$hidden .= "<input type=\"hidden\" name=\"file_type\" value=\"".$data['file_type']."\" />\n";
				$hidden .= "<input type=\"hidden\" name=\"category\" value=\"".$data['category']."\" />\n";
				$hidden .= "<input type=\"hidden\" name=\"list\" value=\"1\" />\n";

				# CAMPAIGN
				if($data['category'] == 99){
					# CAMPAIGNに設定中かどうか
					$check_rows	= $campaignsets->countCampaignFile($data['id'],$this->post_data['file_type']);
				}

				# EMOJI
				$title_normal	= $this->emoji_obj->emj_decode($data['title_normal']);
				$body_normal		= $this->emoji_obj->emj_decode($data['body_normal']);

				# TITLE
				$file_title		= $title_normal[web];
				$file_message	= $images->replaceImage($body_normal[web],$this->site_cd,$this->post_data['file_type'],$this->post_data['category'],$this->image_dir);

				# HTML
				if($this->image_flg != 1){
					$file_message	= nl2br($file_message);
				}


				$result .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\" style=\"margin-bottom: 10px;\">\n";

				$result .= "<tr>\n";
				$result .= "<td class=\"table_title\" width=\"80\" style=\"vertical-align:middle;\">設定【".$i."】</td>\n";
				$result .= "<td class=\"table_contents\" style=\"vertical-align:middle;\">".$data['name']."</td>\n";

				# CONTENTS
				$result .= "<td class=\"table_contents\" width=\"80\" style=\"vertical-align:middle;\">\n";
				$result .= "<a href=\"javascript:void(0)\" onclick=\"display_controll(".$data['id'].");\">詳細 / 編集</a>\n";
				$result .= "</td>\n";

				# DELETE
				$result .= "<td class=\"table_contents\" width=\"80\" style=\"vertical-align:middle;\">\n";
				if($data['category'] == 99 && $check_rows != 0){
				$result .= "<span class=\"style_pink\">現在設定中</span><br />\n";
				}
				$result .= "<form action=\"setting_filesets_exe.php\" method=\"post\">\n";
				$result .= $this->sec_form;
				$result .= "<input type=\"hidden\" name=\"purpose\" value=\"3\" />\n";
				$result .= $hidden;
				$result	.= $this->html->htmlSubmit("削除",1);
				$result .= "</form>\n";
				$result .= "</td>\n";

				$result .= "</tr>\n";

				$result .= "</table>\n";

				# FADE
				$result .= "<div id=\"display_fade".$data['id']."\" class=\"display_fade\" style=\"margin-top: -5px;\">\n";
				# UPDATE
				$result .= "<form action=\"setting_filesets_exe.php\" method=\"post\">\n";
				$result .= $this->sec_form;
				$result .= "<input type=\"hidden\" name=\"purpose\" value=\"2\" />\n";
				$result .= $hidden;

				$result .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";

				# FROM NAME登録可
				if($this->from_name == 1){
				$result .= "<tr>\n";
				$result .= "<td class=\"table_title\" colspan=\"2%\" valign=\"top\">【お知らせ送信者名】</td>\n";
				$result .= "</tr>\n";
				$result .= "<tr>\n";
				$result .= "<td class=\"table_contents\" colspan=\"2%\">\n";
				$result	.= "<input type=\"text\" name=\"title_etc\" size=\"30\" value=\"".$data['title_etc']."\" />\n";
				$result	.= "(空の場合はサイト名で送信されます)</td>\n";
				$result .= "</tr>\n";
				}


				$result .= "<tr>\n";
				$result .= "<td class=\"table_title\" width=\"50%\" valign=\"top\">【現在の一言】</td>\n";
				$result .= "<td class=\"table_title\" width=\"50%\" valign=\"top\">【一言変更】</td>\n";
				$result .= "</tr>\n";
				$result .= "<tr>\n";
				$result .= "<td class=\"table_contents\">\n".$file_title."\n</td>\n";
				$result .= "<td class=\"table_contents\">\n<input type=\"text\" name=\"title_normal\" size=\"60\" value=\"".$title_normal[text]."\" />\n</td>\n";
				$result .= "</tr>\n";

				$result .= "<tr>\n";
				$result .= "<td class=\"table_title\" width=\"50%\" valign=\"top\">【現在の内容".$html_retry_title."】</td>\n";
				$result .= "<td class=\"table_title\" width=\"50%\" valign=\"top\">【内容変更".$html_retry_title."】</td>\n";
				$result .= "</tr>\n";
				$result .= "<tr>\n";
				$result .= "<td class=\"table_contents\">\n".$file_message."\n</td>\n";
				$result .= "<td class=\"table_contents\">\n<textarea name=\"body_normal\" cols=\"40\" rows=\"25\">".$body_normal[text]."</textarea>\n</td>\n";
				$result .= "</tr>\n";

				# HTML RETRY はNORMAL MAILも
				if($html_retry_flg){

					$result .= "<tr>\n";
					$result .= "<td class=\"table_title\" width=\"50%\" valign=\"top\">【現在の内容(通常RETRY)】</td>\n";
					$result .= "<td class=\"table_title\" width=\"50%\" valign=\"top\">【内容変更(通常RETRY)】</td>\n";
					$result .= "</tr>\n";
					$result .= "<tr>\n";
					$result .= "<td class=\"table_contents\">\n".nl2br($data['body_pays'])."\n</td>\n";
					$result .= "<td class=\"table_contents\">\n<textarea name=\"body_pays\" cols=\"40\" rows=\"25\">".$data['body_pays']."</textarea>\n</td>\n";
					$result .= "</tr>\n";

				}

				$result .= "</table>\n";






				$result .= "<p align=\"center\">";
				$result	.= $this->html->htmlSubmit("変更",1);
				$result .= "</p><br /><br />\n";
				$result .= "</form>\n";
				$result .= "</div>\n";

				$i++;

			}


		/*****************************************
		**
		**	折り返しメール固定型
		**	--------------------------------------
		**	@file_type	= 1
		**	@list		= NULL
		**	@fixed		= 1
		**
		******************************************/

		}elseif($this->post_data['fixed']){

			$data	= $this->getFilesetsData($default=1,NULL,$this->post_data['category'],NULL,$this->post_data['sex'],NULL);

			$hidden .= "<input type=\"hidden\" name=\"file_id\" value=\"".$data['id']."\" />\n";
			$hidden .= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
			$hidden .= "<input type=\"hidden\" name=\"fixed\" value=\"".$this->post_data['fixed']."\" />\n";
			$hidden .= "<input type=\"hidden\" name=\"category\" value=\"".$this->post_data['category']."\" />\n";
			$hidden .= "<input type=\"hidden\" name=\"sex\" value=\"".$this->post_data['sex']."\" />\n";
			$hidden	.= "<input type=\"hidden\" name=\"name\" value=\"".$this->getPageTitle()."\" />\n";

			# CANCEL
			if($this->post_data['category'] == 1){

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">コンビニダイレクト用</td>\n";
				$contents	.= "<td class=\"table_title\">&nbsp;</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;<br />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;<br />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

			# メルアド変更
			}elseif($this->post_data['category'] == 2){

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">変更確認前</td>\n";
				$contents	.= "<td class=\"table_title\">変更確認後</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_pays\" size=\"50\" value=\"".$data['title_pays']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_pays\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_pays']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">変更失敗</td>\n";
				$contents	.= "<td class=\"table_title\" style=\"width:50%;\">&nbsp;</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_fixed\" size=\"50\" value=\"".$data['title_fixed']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_fixed\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_fixed']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

			# 入金関係
			}elseif($this->post_data['category'] == 3){

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">通常入金後折り返し</td>\n";
				$contents	.= "<td class=\"table_title\">後払い利用後折り返し</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_pays\" size=\"50\" value=\"".$data['title_pays']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_pays\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_pays']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">後払い清算後折り返し(未清算)</td>\n";
				$contents	.= "<td class=\"table_title\">入金+後払い清算完了</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_fixed\" size=\"50\" value=\"".$data['title_fixed']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_free\" size=\"50\" value=\"".$data['title_free']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_fixed\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_fixed']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_free\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_free']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

			# 登録
			}elseif($this->post_data['category'] == 17){

				# OTHER FOMR
				$other_form	 = "<form action=\"./setting_filesets.php\" method=\"post\" target=\"contentsFrame\">\n";
				$other_form	.= $this->sec_form;
				$other_form .= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"fixed\" value=\"".$this->post_data['fixed']."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"category\" value=\"".$this->post_data['category']."\" />\n";
				$other_form	.= "<input type=\"hidden\" name=\"name\" value=\"".$this->getPageTitle()."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"sex\" value=\"1\" />\n";
				$other_form .= "<input type=\"submit\" class=\"button\" value=\"男性\" />\n";
				$other_form	.= "</form>\n\n";
				$other_form	.= "<form action=\"./setting_filesets.php\" method=\"post\" target=\"contentsFrame\">\n";
				$other_form	.= $this->sec_form;
				$other_form .= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"fixed\" value=\"".$this->post_data['fixed']."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"category\" value=\"".$this->post_data['category']."\" />\n";
				$other_form	.= "<input type=\"hidden\" name=\"name\" value=\"".$this->getPageTitle()."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"sex\" value=\"2\" />\n";
				$other_form .= "<input type=\"submit\" class=\"button\" value=\"女性\" />\n";
				$other_form	.= "</form>\n\n";
				$other_form	.= "<form action=\"./setting_filesets.php\" method=\"post\" target=\"contentsFrame\">\n";
				$other_form	.= $this->sec_form;
				$other_form .= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"fixed\" value=\"".$this->post_data['fixed']."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"category\" value=\"".$this->post_data['category']."\" />\n";
				$other_form	.= "<input type=\"hidden\" name=\"name\" value=\"".$this->getPageTitle()."\" />\n";
				$other_form .= "<input type=\"hidden\" name=\"sex\" value=\"3\" />\n";
				$other_form .= "<input type=\"submit\" class=\"button\" value=\"性別不明\" />\n";
				$other_form	.= "</form>\n\n";


				# REG REMAIL
				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">エントリーリメール成功</td>\n";
				$contents	.= "<td class=\"table_title\">エントリーリメール失敗</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_pays\" size=\"50\" value=\"".$data['title_pays']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_pays\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_pays']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

				# REG REMAIL
				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">本登録完了リメール</td>\n";
				$contents	.= "<td class=\"table_title\">同時登録完了後メール(一発本登録)</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_fixed\" size=\"50\" value=\"".$data['title_fixed']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_free\" size=\"50\" value=\"".$data['title_free']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_fixed\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_fixed']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_free\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_free']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

				# REG REMAIL
				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\" width=\"50%\">同時登録完了後メール(仮登録)</td>\n";
				$contents	.= "<td class=\"table_title\">&nbsp;</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_drop\" size=\"50\" value=\"".$data['title_drop']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;<br />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_drop\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_drop']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;<br />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

			# 年齢認証
			}elseif($this->post_data['category'] == 18){


				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">年齢認証誘導リメール</td>\n";
				$contents	.= "<td class=\"table_title\">年齢認証完了メール</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_pays\" size=\"50\" value=\"".$data['title_pays']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_pays\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_pays']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">年齢認証画像添付失敗</td>\n";
				$contents	.= "<td class=\"table_title\" width=\"50%\">年齢認証完了メール(既存ユーザー用)</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_fixed\" size=\"50\" value=\"".$data['title_fixed']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_free\" size=\"50\" value=\"".$data['title_free']."\" />\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_fixed\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_fixed']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_free\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_free']."</textarea>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";


			# 添付関係
			}elseif($this->post_data['category'] == 19){

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">写メ申請成功</td>\n";
				$contents	.= "<td class=\"table_title\">写メ申請失敗</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_pays\" size=\"50\" value=\"".$data['title_pays']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_pays\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_pays']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";


				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\" width=\"50%\">写メ申請エラー</td>\n";
				$contents	.= "<td class=\"table_title\">&nbsp;</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_fixed\" size=\"50\" value=\"".$data['title_fixed']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;<br />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_fixed\" cols=\"35\" rows=\"20\">";
				$contents	.= $data['body_fixed']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "&nbsp;<br />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

			}else{

				$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_title\">折り返し文</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【件名】<br />\n";
				$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"60\" value=\"".$data['title_normal']."\" />\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "<tr>\n";
				$contents	.= "<td class=\"table_contents\">\n";
				$contents	.= "【内容】<br />\n";
				$contents	.= "<textarea name=\"body_normal\" cols=\"50\" rows=\"20\">";
				$contents	.= $data['body_normal']."</textarea>\n";
				$contents	.= "</td>\n";
				$contents	.= "</tr>\n";
				$contents	.= "</table>\n\n";

			}

			if($this->post_data['sex'] == 1){
				$sub_title	= "【男性】";
			}elseif($this->post_data['sex'] == 2){
				$sub_title	= "【女性】";
			}elseif($this->post_data['sex'] == 3){
				$sub_title	= "【性別不明】";
			}

			# CONETNTS START
			$result	 = "<div class=\"title_sub\">\n";
			$result	.= $this->getPageTitle()." 折り返しメール文言".$sub_title."\n";
			$result	.= $other_form;
			$result	.= "</div>\n";
			$result	.= "<br />\n\n";


			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= $hidden;
			if($data['id']){
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"2\" />\n";
			}else{
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"1\" />\n";
			}

			$result	.= $contents;
			$result	.= "<p align=\"center\">\n";
			$result	.= $this->html->htmlSubmit("送信文言を更新",1);
			$result	.= "</p>\n";

			$result	.= "</form>\n";
			$result	.= "<br />\n\n";




		/*****************************************
		**
		**	お知らせMAIL設定 デフォルト
		**	--------------------------------------
		**	@file_type	= 1
		**	@list		= NULL
		**
		******************************************/

		}elseif($this->post_data['file_type'] == 1){

			# GET FILESETS DATA
			$data	= $this->getFilesetsData($default=1,$this->post_data['file_id'],NULL,$this->post_data['type'],$this->post_data['sex'],NULL);

			# EMPTY
			if(!$data['id']){
				return FALSE;
			}


			# ARRAY SETTING
			global $mail_type_array;

			if($this->post_data['sex']){
				$input_sex	= $this->post_data['sex'];
			}else{
				$input_sex	= $data['sex'];
			}

			$input_hidden	 = "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
			$input_hidden	.= "<input type=\"hidden\" name=\"sex\" value=\"".$input_sex."\" />\n";
			$input_hidden	.= "<input type=\"hidden\" name=\"category\" value=\"".$data['category']."\" />\n";

			$count	= count($mail_type_array);
			for($i=1;$i<$count;$i++){

				$input_value	= $mail_type_array[$i][0];
				$input_name		= $mail_type_array[$i][1];

				# JAVASCRIPT
				$button		.= "<form action=\"./setting_filesets.php\" method=\"post\" target=\"contentsFrame\">\n";
				$button		.= $this->sec_form;
				$button		.= $input_hidden;
				$button		.= "<input type=\"hidden\" name=\"type\" value=\"".$input_value."\" />\n";
				$button		.= "<input type=\"submit\" class=\"button\" value=\"".$input_name."\" />\n";
				$button		.= "</form>\n\n";

			}

			$hidden		.= "<input type=\"hidden\" name=\"file_id\" value=\"".$data['id']."\" />\n";
			$hidden		.= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
			$hidden		.= "<input type=\"hidden\" name=\"category\" value=\"".$data['category']."\" />\n";
			$hidden		.= "<input type=\"hidden\" name=\"type\" value=\"".$data['type']."\" />\n";
			$name		 = $mail_type_array[$data['type']][1];

			# DISPLAY CONTENTS
			$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_title\">".$name."入無し</td>\n";
			$contents	.= "<td class=\"table_title\">".$name."入有り</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【件名】<br />\n";
			$contents	.= "<input type=\"text\" name=\"title_normal\" size=\"50\" value=\"".$data["title_normal"]."\" />\n";
			$contents	.= "</td>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【件名】<br />\n";
			$contents	.= "<input type=\"text\" name=\"title_pays\" size=\"50\" value=\"".$data["title_pays"]."\" />\n";
			$contents	.= "</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【内容】<br />\n";
			$contents	.= "<textarea name=\"body_normal\" cols=\"35\" rows=\"20\">";
			$contents	.= $data["body_normal"]."</textarea>\n";
			$contents	.= "</td>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【内容】<br />\n";
			$contents	.= "<textarea name=\"body_pays\" cols=\"35\" rows=\"20\">";
			$contents	.= $data["body_pays"]."</textarea>\n";
			$contents	.= "</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "</table>\n\n";

			$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_title\">".$name."定額</td>\n";
			$contents	.= "<td class=\"table_title\">".$name."無料</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【件名】<br />\n";
			$contents	.= "<input type=\"text\" name=\"title_fixed\" size=\"50\" value=\"".$data["title_fixed"]."\" />\n";
			$contents	.= "</td>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【件名】<br />\n";
			$contents	.= "<input type=\"text\" name=\"title_free\" size=\"50\" value=\"".$data["title_free"]."\" />\n";
			$contents	.= "</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【内容】<br />\n";
			$contents	.= "<textarea name=\"body_fixed\" cols=\"35\" rows=\"20\">";
			$contents	.= $data["body_fixed"]."</textarea>\n";
			$contents	.= "</td>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【内容】<br />\n";
			$contents	.= "<textarea name=\"body_free\" cols=\"35\" rows=\"20\">";
			$contents	.= $data["body_free"]."</textarea>\n";
			$contents	.= "</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "</table>\n";

			$contents	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_title\" width=\"50%\">".$name."落とし込み</td>\n";
			$contents	.= "<td class=\"table_title\">&nbsp;</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【件名】<br />\n";
			$contents	.= "<input type=\"text\" name=\"title_drop\" size=\"50\" value=\"".$data["title_drop"]."\" />\n";
			$contents	.= "</td>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "&nbsp;<br />\n";
			$contents	.= "</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "<tr>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "【内容】<br />\n";
			$contents	.= "<textarea name=\"body_drop\" cols=\"35\" rows=\"20\">";
			$contents	.= $data["body_drop"]."</textarea>\n";
			$contents	.= "</td>\n";
			$contents	.= "<td class=\"table_contents\">\n";
			$contents	.= "&nbsp;<br />\n";
			$contents	.= "</td>\n";
			$contents	.= "</tr>\n";
			$contents	.= "</table>\n";


			# CONETNTS START
			/*
			$result	 = "<script language=\"javascript\">\n";
			$result	.= "<!--\n";
			$result	.= "window.onload = function() { multi_controll(1); }\n";
			$result	.= "// -->\n";
			$result	.= "</script>\n\n";
			*/

			$result	.= "<div class=\"title_sub\">\n";
			$result	.= $this->getPageTitle()."\n";
			$result	.= $button;
			#$result	.= "<input type=\"button\" class=\"button\" value=\"エントリー\" onClick=\"multi_controll(99)\" />\n";
			#$result	.= "<input type=\"button\" class=\"button\" value=\"本登録\" onClick=\"multi_controll(100)\" />\n";
			$result	.= "</div>\n";
			$result	.= "<br />\n\n";

			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= $hidden;
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"2\" />\n";

			$result	.= $contents;
			$result	.= "<p align=\"center\">\n";
			$result	.= $this->html->htmlSubmit("送信文言を更新",1);
			$result	.= "</p>\n";

			$result	.= "</form>\n";
			$result	.= "<br />\n\n";

			# NO DEFAULT
			if($data['sex'] != 0 || $this->post_data['domain_flg'] != 0){

			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= $hidden;
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"4\" />\n";
			$result	.= "<p align=\"center\">\n";
			$result	.= "<input type=\"submit\" class=\"button\" value=\"送信文言をデフォルト設定にする\" onClick=\"return confirm('設定しますか？')\" />\n";
			$result	.= "</p>\n";
			$result	.= "</form>\n\n";

			$result	.= "<br /><br /><br />";
			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= $hidden;
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"3\" />\n";
			$result	.= "<p align=\"center\">\n";
			$result	.= "<input type=\"submit\" class=\"button\" value=\"削除(要注意)\" onClick=\"return confirm('本当に削除しますか？')\" />\n";
			$result	.= "</p>\n";
			$result	.= "</form>\n\n";

			}



		/*****************************************
		**
		**	HTML設定 デフォルト
		**	--------------------------------------
		**	@file_type	= 2
		**	@list		= NULL
		**	--------------------------------------
		**	
		**
		******************************************/

		}elseif($this->post_data['file_type'] == 2){

			# GET FILESETS DATA
			$data	= $this->getFilesetsData($default=1,$this->post_data['file_id'],$this->post_data['category'],NULL,$this->post_data['sex'],NULL);

			$body_normal	= $this->emoji_obj->emj_decode($data['body_normal']);
			$body_pays 		= $this->emoji_obj->emj_decode($data['body_pays']);
			$body_fixed		= $this->emoji_obj->emj_decode($data['body_fixed']);
			$body_free 		= $this->emoji_obj->emj_decode($data['body_free']);

			$result	.= "<div class=\"title_sub\">".$this->sub_name.$this->name." HTML文言</div>\n\n";

			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result .= "<input type=\"hidden\" name=\"file_id\" value=\"".$data['id']."\" />\n";
			$result .= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
			$result .= "<input type=\"hidden\" name=\"category\" value=\"".$this->post_data['category']."\" />\n";
			$result	.= "<input type=\"hidden\" name=\"sex\" value=\"".$this->sex."\" />\n";
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"2\" />\n";

			$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\">入金無し用</td>\n";
			$result	.= "<td class=\"table_title\">入金有り用</td>\n";
			$result	.= "</tr>\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "【表示文言】<br />\n";
			$result	.= "<textarea name=\"body_normal\" cols=\"40\" rows=\"".$this->textarea_rows."\">".$body_normal[text]."</textarea>\n";
			$result	.= "</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "【表示文言】<br />\n";
			$result	.= "<textarea name=\"body_pays\" cols=\"40\" rows=\"".$this->textarea_rows."\">".$body_pays[text]."</textarea>\n";
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "</table>\n\n";

			$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\">定額用</td>\n";
			$result	.= "<td class=\"table_title\">無料用</td>\n";
			$result	.= "</tr>\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "【表示文言】<br />\n";
			$result	.= "<textarea name=\"body_fixed\" cols=\"40\" rows=\"".$this->textarea_rows."\">".$body_fixed[text]."</textarea>\n";
			$result	.= "</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "【表示文言】<br />\n";
			$result	.= "<textarea name=\"body_free\" cols=\"40\" rows=\"".$this->textarea_rows."\">".$body_free[text]."</textarea>\n";
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "</table>\n\n";

			$result	.= "<p align=\"center\">\n";
			$result	.= "<input type=\"submit\" class=\"button\" value=\" HTML 文 言 を 更 新 \" onClick=\"return confirm('更新しますか？')\" />\n";
			$result	.= "</p>\n\n";

			$result	.= "</form>\n\n";


		}


		return $result;

	}



	/**************************************************
	**
	**	getMailMenu
	**	----------------------------------------------
	**	setting_file.phpの左側ナビゲーション
	**	file_type	= 1
	**	MAILコンテンツ
	**
	**************************************************/

	private function getMailMenu(){

		# GLOBAL
		global	$mail_file_type;	// MAIL TYPE ARRAY

		$result	 = "<div class=\"title_sub\">カテゴリー 種別</div>\n";
		$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
		$result	.= "<tr>\n";
		$result	.= "<td class=\"table_title\" width=\"80\">お知らせ設定</td>\n";
		$result	.= "<td class=\"table_contents\">\n";

		$rtn	 = $this->getFilesetsData(NULL,NULL,NULL,NULL,NULL,"id,name",1);
		while($data = $this->db->fetchAssoc($rtn)){

			# domain_flgがある場合はデフォルト破棄
			if($this->post_data['domain_flg'] && $data['sex'] == 0 && $data['category'] == 0 && $data['domain_flg']){
				continue;
			}

			$result	.= "<a href=\"setting_filesets.php?".$this->sec."&file_type=".$this->post_data['file_type']."&file_id=".$data['id']."\">";
			$result	.= $data['name']."</a><br />\n";

		}

		$this->db->free_result($rtn);

		$result	.= "</td>\n";
		$result	.= "</tr>\n";

		if(!$this->post_data['domain_flg']){

			# CATEGORY LINK
			$count	= count($mail_file_type);
			for($i=1;$i<$count;$i++){

				# $mail_file_typeの3列目 FIXED値
				$list_flg	= $mail_file_type[$i][2];

				# 固定型
				if($list_flg == 0){

					# FILE TYPE / PAGE TITLE
					if($this->post_data['category'] == $mail_file_type[$i][1]){
						$this->name			= $mail_file_type[$i][4];
						$this->image_flg	= $mail_file_type[$i][3];
						$this->from_name	= $mail_file_type[$i][6];
					}

					$category_link	 = "<a href=\"setting_filesets.php?".$this->sec."&file_type=".$this->post_data['file_type'];
					$category_link	.= "&category=".$mail_file_type[$i][1]."&fixed=1\">";
					$category_link	.= $mail_file_type[$i][4]."折り返し文</a>";

				# 追加型
				}else{

					# CATEGORY SELECT TAG 生成
					if($mail_file_type[$i][1] == $this->post_data['category']){
						$category_option	.= "<option value=\"".$mail_file_type[$i][1]."\" selected>".$mail_file_type[$i][4]."</option>\n";
					}else{
						$category_option	.= "<option value=\"".$mail_file_type[$i][1]."\">".$mail_file_type[$i][4]."</option>\n";
					}


					# FILE TYPE / PAGE TITLE
					if($this->post_data['list'] && $this->post_data['category'] == $mail_file_type[$i][1]){
						$this->name			= $mail_file_type[$i][4];
						$this->image_flg	= $mail_file_type[$i][3];
						$this->from_name	= $mail_file_type[$i][6];
						$this->replace		= $mail_file_type[$i][7];
					}

					$category_link	 = "<a href=\"setting_filesets.php?".$this->sec."&file_type=".$this->post_data['file_type'];
					$category_link	.= "&category=".$mail_file_type[$i][1]."&list=1\">";
					$category_link	.= $mail_file_type[$i][4]."送信文一覧</a>";

				}

				$result	.= "<tr>\n";
				$result	.= "<td class=\"table_title\" width=\"80\">".$mail_file_type[$i][4]."</td>\n";
				$result	.= "<td class=\"table_contents\">".$category_link."</td>\n";
				$result	.= "</tr>\n";

			}

		}

		$result .= "</table>\n\n";


		$this->category_select	 = "<select name=\"category\">\n";
		$this->category_select	.= $category_option;
		$this->category_select	.= "</select>\n";

		return $result;

	}



	/**************************************************
	**
	**	getHtmlMenu
	**	----------------------------------------------
	**	setting_file.phpの左側ナビゲーション
	**	file_type	= 2
	**	HTMLコンテンツ
	**
	**************************************************/

	private function getHtmlMenu() {

		# GLOBAL
		global	$html_file_type;
		global	$sex_array;

		$result	 = "<div class=\"title_sub\">カテゴリー 種別</div>\n";
		$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";
		$result	.= "<tr>\n";

		# CATEGORY LINK
		$count	= count($html_file_type);
		for($i=1;$i<$count;$i++){

			# HEADER
			if($html_file_type[$i][1] != $post_category){
				$result	.= "<tr>\n";
				$result	.= "<td class=\"table_title\" width=\"80\">".$html_file_type[$i][4]."</td>\n";
				$result	.= "<td class=\"table_contents\">\n";
			}

			# $html_file_typeの3列目 FIXED値
			$list_flg	= $html_file_type[$i][2];

			# NORMAL
			if($list_flg == 0){

				# SEX
				$sex_flg	= $html_file_type[$i][6];
				if($sex_flg != 0){
					$disp_sex	= $sex_array[$sex_flg][1];
				}else{
					$disp_sex	= NULL;
				}

				# PAGE TITLE
				if($this->post_data['category'] == $html_file_type[$i][1] && $this->post_data['sex'] == $sex_flg){
					$this->name				= $html_file_type[$i][4]." ".$disp_sex;
					$this->sex				= $html_file_type[$i][6];
					$this->textarea_rows	= $html_file_type[$i][7];
					$this->image_flg		= $html_file_type[$i][2];
				}

				$result	.= "<a href=\"setting_filesets.php?".$this->sec."&file_type=".$this->post_data['file_type'];
				$result	.= "&category=".$html_file_type[$i][1]."&sex=".$sex_flg."\">";
				$result	.= $html_file_type[$i][4]." ".$disp_sex."</a><br />\n";

			# 追加型
			}else{

				$result	.= "<a href=\"setting_filesets.php?".$this->sec."&file_type=".$this->post_data['file_type'];
				$result	.= "&category=".$html_file_type[$i][1]."&list=1\">";
				$result	.= $html_file_type[$i][4]."HTML文言一覧</a><br />\n";

				# PAGE TITLE
				if($this->post_data['category'] == $html_file_type[$i][1]){
					$this->name				= $html_file_type[$i][4];
					$this->image_dir		= $html_file_type[$i][5];
					$this->textarea_rows	= $html_file_type[$i][7];
					$this->image_flg		= $html_file_type[$i][2];
				}

			}

			# FOOTER
			if($html_file_type[$i][1] == $post_category){
				$result	.= "</td>\n";
				$result	.= "</tr>\n";
			}


			# POST
			$post_category	= $html_file_type[$i][1];

		}

		$result	.= "</td>\n";
		$result	.= "</tr>\n";
		$result .= "</table>\n\n";

		return $result;

	}



	/**************************************************
	**
	**	getRegistForm
	**	----------------------------------------------
	**	ドメインフラグ用文言追加フォーム
	**
	**************************************************/

	private function getRegistForm(){

		if(!$this->checkFilesDomain()){

			global	$sex_array;
			global	$mail_type_array;

			$result	.= "<form action=\"./setting_filesets_exe.php\" method=\"post\" target=\"contentsFrame\">\n";
			$result	.= $this->sec_form;
			$result	.= "<input type=\"hidden\" name=\"purpose\" value=\"1\" />\n";
			$result	.= "<input type=\"hidden\" name=\"file_type\" value=\"".$this->post_data['file_type']."\" />\n";
			$result	.= "<input type=\"hidden\" name=\"format\" value=\"1\" />\n";
			$result	.= "<div class=\"title_sub\">".$this->name." 送信メール文追加</div>\n";
			$result	.= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" class=\"table_frame\">\n";

			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\" width=\"80\">設定名</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "<input type=\"text\" name=\"name\" size=\"25\" />\n";
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\" width=\"80\">性別</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= $this->html->getRadioTags("sex",$sex_array,NULL,NULL,1,1);
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			/*
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\" width=\"80\">種別</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= $this->html->getSelectTags("type",$mail_type_array,NULL,NULL,1,1,NULL);
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			*/
			$result	.= "<tr>\n";
			$result	.= "<td class=\"table_title\">追加</td>\n";
			$result	.= "<td class=\"table_contents\">\n";
			$result	.= "<input type=\"submit\" class=\"button\" value=\"追加\" onClick=\"return confirm('追加しますか？')\" />\n";
			$result	.= "</td>\n";
			$result	.= "</tr>\n";
			$result	.= "</table>\n";
			$result	.= "</form>\n\n";

		}else{

			$result = FALSE;

		}

		return $result;

	}



	/**************************************************
	**
	**	getAddCategory
	**	----------------------------------------------
	**	追加用セレクト単独取得
	**
	**************************************************/

	private function getAddCategory(){

		# GLOBAL
		global	$mail_file_type;	// MAIL TYPE ARRAY

		# CATEGORY LINK
		$count	= count($mail_file_type);
		for($i=1;$i<$count;$i++){

			# $mail_file_typeの3列目 FIXED値が0だったらCONTINUE
			$list_flg	= $mail_file_type[$i][2];
			if($list_flg == 0){ continue; }


			# CATEGORY SELECT TAG 生成
			if($mail_file_type[$i][1] == $this->post_data['category']){
				$category_option	.= "<option value=\"".$mail_file_type[$i][1]."\" selected>".$mail_file_type[$i][4]."</option>\n";
			}else{
				$category_option	.= "<option value=\"".$mail_file_type[$i][1]."\">".$mail_file_type[$i][4]."</option>\n";
			}

		}

		$this->category_select	 = "<select name=\"category\">\n";
		$this->category_select	.= $category_option;
		$this->category_select	.= "</select>\n";

	}



	/**************************************************
	**
	**	getPageTitle
	**	----------------------------------------------
	**	setting_filesets.phpのタイトル取得
	**
	**************************************************/

	private function getPageTitle() {

		if(!$this->name){
			$data	= $this->getFilesetsData(NULL,$this->post_data['file_id'],NULL,NULL,NULL,'name');
			$result	= $data['name'];
		}else{
			$result	= $this->name;
		}

		return $result;
	}


	/**************************************************
	**
	**	getImageList
	**	----------------------------------------------
	**	setting_filesets.phpの画像取得
	**
	**************************************************/

	private function getImage() {
		return $this->image_list;
	}


	/**************************************************
	**
	**	getMailCategory
	**	----------------------------------------------
	**	setting_filesets.phpのメールカテゴリ取得
	**
	**************************************************/

	private function getMailCategory() {
		return $this->category_select;
	}



	/**************************************************
	**
	**	getFilesetsData
	**	----------------------------------------------
	**	filesetsのデータ取得
	**
	***************************************************/

	public function getFilesetsData($default=NULL,$file_id=NULL,$category=NULL,$type=NULL,$sex=NULL,$column=NULL,$index=NULL){

		if(!$column){
			$column	 = "*";
		}

		if($file_id){

			$where	 = "id = ".$file_id;
			$order	 = "";
			$limit	 = "1";

			$rtn	 = $this->db->selectDb($this->table,$column,$where,$order,$limit);
			$this->db->errorDb("getFilesetsData : ",$db->errno,__FILE__,__LINE__);
			if($this->db_err){ print($this->db_err); exit; }

			$result	 	= $this->db->fetchAssoc($rtn);
			$this->name	= $result['name'];

		}elseif($default){

			$where	 = "site_cd = '".$this->site_cd."' ";
			$where	.= "AND file_type = '".$this->post_data['file_type']."' ";

			if($this->post_data['domain_flg']){
			$where	.= "AND domain_flg = '".$this->post_data['domain_flg']."' ";
			}else{
			$where	.= "AND domain_flg = 0 ";
			}

			if($sex){
				$where	.= "AND sex = ".$sex." ";
			}else{
				if($this->post_data['domain_flg']){
					$where	.= "AND sex = 1 ";
				}else{
					$where	.= "AND sex = 0 ";
				}
			}
			if($category){
				$where	.= "AND category = ".$category." ";
			}else{
				$where	.= "AND category = 0 ";
			}
			if($this->post_data['type']){
				$where	.= "AND type = ".$this->post_data['type']." ";
			}else{
				if($index){
					$where	.= "AND type = 1 ";
				}else{
					$where	.= "AND type = 0 ";
				}
			}

			$where	.= "AND status = 0";
			$order	 = "";
			$limit	 = "1";

			$rtn	 = $this->db->selectDb($this->table,$column,$where,$order,$limit);
			$this->db->errorDb("getFilesetsData : ",$db->errno,__FILE__,__LINE__);
			if($this->db_err){ print($this->db_err); exit; }

			$result		= $this->db->fetchAssoc($rtn);

			if($this->post_data['file_type'] == 1 && $result['name']){
				$this->name	= $result['name'];
			}

		}else{

			$where	 = "site_cd = '".$this->site_cd."' ";
			$where	.= "AND file_type = '".$this->post_data['file_type']."' ";
			if($category){
			$where	.= "AND category = '".$category."' ";
			}else{
			$where	.= "AND category = 0 ";
			}
			if($this->post_data['type']){
				$where	.= "AND type = ".$this->post_data['type']." ";
			}else{
				if($index){
					$where	.= "AND type = 1 ";
				}else{
					$where	.= "AND type = 0 ";
				}
			}
			if($this->post_data['domain_flg']){
			$where	.= "AND domain_flg = '".$this->post_data['domain_flg']."' ";
			}else{
			$where	.= "AND domain_flg = 0 ";
			}
			$where	.= "AND status = 0";
			$order	 = "sex,id";
			$limit	 = "";

			$result	 = $this->db->selectDb($this->table,$column,$where,$order,$limit);
			$this->db->errorDb("getFilesetsData : ",$db->errno,__FILE__,__LINE__);
			if($this->db_err){ print($this->db_err); exit; }

		}

		return $result;

	}



	/*********************************************
	**
	**	filesets カテゴリー毎 リスト取得
	**
	*********************************************/

	public function getFileSelect($mail_id){

		$column	 = "id,name,file_type";
		$where	.= "category = '".$this->post_data['category']."' ";
		$where	.= "AND site_cd = '".$this->site_cd."' ";
		$where	.= "AND status != 9";
		$order	 = "";
		$limit	 = "";

		$rtn	= $this->db->selectDb($this->table,$column,$where,$order,$limit);
		$this->db->errorDb("",$db->errno,___,__LINE__);
		if($this->db_err){ print($this->db_err); exit; }


		while($data = $this->db->fetchAssoc($rtn)){

			if($mail_id['mail']	== $data['id']){
				$selected_m	= " selected";
			}else{
				$selected_m	= "";
			}

			if($mail_id['html']	== $data['id']){
				$selected_h	= " selected";
			}else{
				$selected_h	= "";
			}

			# MAIL FILE
			if($data['file_type'] == 1){
				$result['mail'] .= "<option value=\"".$data['id']."\"".$selected_m.">".$data['name']."</option>\n";
			# HTML FILE
			}else{
				$result['html'] .= "<option value=\"".$data['id']."\"".$selected_h.">".$data['name']."</option>\n";
			}

		}

		return $result;

	}


	/*********************************************
	**
	**	checkFilesDomain
	**
	*********************************************/

	public function checkFilesDomain(){

		$column	 = "id";
		$where	 = "domain_flg = '".$this->post_data['domain_flg']."' ";
		$where	.= "AND file_type = '".$this->post_data['file_type']."' ";
		$where	.= "AND status = 0 ";
		$where1	 = "AND sex = 1";
		$where2	 = "AND sex = 2";
		$where3	 = "AND sex = 3";
		$order	 = "";
		$limit	 = "";

		# MAN
		$rtn	= $this->db->selectDb($this->table,$column,$where.$where1,$order,$limit);
		$this->db->errorDb("",$db->errno,___,__LINE__);
		if($this->db_err){ print($this->db_err); exit; }

		$row1 = $this->db->numRows($rtn);

		# LADY
		$rtn	= $this->db->selectDb($this->table,$column,$where.$where2,$order,$limit);
		$this->db->errorDb("",$db->errno,___,__LINE__);
		if($this->db_err){ print($this->db_err); exit; }

		$row2 = $this->db->numRows($rtn);

		# NON
		$rtn	= $this->db->selectDb($this->table,$column,$where.$where3,$order,$limit);
		$this->db->errorDb("",$db->errno,___,__LINE__);
		if($this->db_err){ print($this->db_err); exit; }

		$row3 = $this->db->numRows($rtn);

		if($row1 != 0 && $row2 != 0 && $row3 != 0){
			return TRUE;
		}else{
			return FALSE;
		}

	}



	/*********************************************
	**
	**	checkFilesDomain
	**
	*********************************************/

	public function getDefaultSetting($type=NULL){

		$column	 = "*";
		$where	 = "site_cd = '".$this->site_cd."' ";
		$where	.= "AND file_type = 1 ";
		$where	.= "AND status = 0 ";
		$where	.= "AND category = 0 ";
		if($type){
		$where	.= "AND type = ".$type." ";
		}else{
		$where	.= "AND type = 0 ";
		}
		$where	.= "AND domain_flg = 0 ";
		$order	 = "sex";
		$limit	 = "";

		$rtn	= $this->db->selectDb($this->table,$column,$where,$order,$limit);
		$this->db->errorDb("",$db->errno,___,__LINE__);
		if($this->db_err){ print($this->db_err); exit; }

		return $rtn;

	}





}

?>
