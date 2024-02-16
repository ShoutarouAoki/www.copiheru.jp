

function SetCookie(url){
	// cookieにPHPSESSID(セッションキー)の項目が存在しない (トップフレームでcookieの初回書き込みが必要)
	if ( ! document.cookie.match(/PHPSESSID/))
	{
		// トップフレームでiframe内のドメインを呼び出す指示
		nijiyome.cookie({
			"url": url + "script/set_cookie.php"  // cookie初回書き込み用URL
			//,"callback_url": url + "index.php"  // 書き込み後の戻りでiframe内に表示するURL
			,"callback_url": location.href  // 書き込み後の戻りでiframe内に表示するURL
		});
	}
}

