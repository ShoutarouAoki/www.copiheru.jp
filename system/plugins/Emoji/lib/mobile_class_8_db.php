<?php

###############################################################################
# 携帯絵文字変換ﾗｲﾌﾞﾗﾘ 2008(ﾃﾞｰﾀﾍﾞｰｽ処理ｸﾗｽﾗｲﾌﾞﾗﾘ)
# Potora/inaken(C) 2008.
# MAIL: support@potora.dip.jp
#       inaken@jomon.ne.jp
# URL : http://potora.dip.jp/
#       http://www.jomon.ne.jp/~inaken/
###############################################################################
# 2008.10.07 v.1.00.00 新規
# 2008.11.28 v.1.00.01 Heapﾃｰﾌﾞﾙｻｲｽﾞ取得方法修正
# 2008.11.28 v.1.00.02 ｸﾞﾛｰﾊﾞﾙ変数扱い変更
###############################################################################

###############################################################################
# ﾃﾞｰﾀﾍﾞｰｽ処理ｸﾗｽ #############################################################
###############################################################################
class emj_db {
  # ﾊﾞｰｼﾞｮﾝ設定
  var $ver = 'db_v.1.00.00';

  # 変数設定
  var $DB_TYPE;
  var $DBH;

  # ﾃﾞｰﾀﾍﾞｰｽ接続設定値設定
  var $dbd            = '';
  var $db_hostname    = '';
  var $db_hostport    = '';
  var $db_name        = '';
  var $db_username    = '';
  var $db_usrpassword = '';

  var $emj_obj_flag   = 0;

  # ﾃﾞｰﾀﾍﾞｰｽ処理ｺﾝｽﾄﾗｸﾀ ///////////////////////////////////////////////////////
  function db() {
    global $emoji_obj;
    $this->DB_TYPE = array();
    $this->DBH     = array();

    # 絵文字変換ﾗｲﾌﾞﾗﾘｵﾌﾞｼﾞｪｸﾄﾁｪｯｸ
    if (is_object($emoji_obj)) { $this->emj_obj_flag = 1; }

  }

  # 絵文字変換ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ取得 ///////////////////////////////////////////////
  # ｷｬﾘｱ判別と機種情報を取得します。(新処理->推奨)
  # [引渡し値]
  # 　なし
  # [返り値]
  # 　$this->ver : ﾗｲﾌﾞﾗﾘﾊﾞｰｼﾞｮﾝ
  #////////////////////////////////////////////////////////////////////////////
  function Get_Emj_Version() {
    return $this->ver;
  }

  # ﾃﾞｰﾀﾍﾞｰｽ接続設定 //////////////////////////////////////////////////////////
  function db_set_connection_data($SETTINGDATA) {
    # ﾃﾞｰﾀﾍﾞｰｽ値設定
    if (isset($SETTINGDATA['dbd']))            { $this->dbd            = $SETTINGDATA['dbd']; }
    if (isset($SETTINGDATA['db_hostname']))    { $this->db_hostname    = $SETTINGDATA['db_hostname']; }
    if (isset($SETTINGDATA['db_hostport']))    { $this->db_hostport    = $SETTINGDATA['db_hostport']; }
    if (isset($SETTINGDATA['db_name']))        { $this->db_name        = $SETTINGDATA['db_name']; }
    if (isset($SETTINGDATA['db_username']))    { $this->db_username    = $SETTINGDATA['db_username']; }
    if (isset($SETTINGDATA['db_usrpassword'])) { $this->db_usrpassword = $SETTINGDATA['db_usrpassword']; }
  }

  # ﾃﾞｰﾀﾍﾞｰｽ接続 //////////////////////////////////////////////////////////////
  # ﾃﾞｰﾀﾍﾞｰｽへ接続します。(設定ﾌｧｲﾙでDB接続設定されている必要有り)
  # MySQLの場合、DBが存在しない場合は新規にDBを生成します。
  # 引渡値：$connect_no   => 接続番号
  # 　　　　$sub_dbname   => 別DB指定
  # 返り値：$connect_flag => 接続ｽﾃｰﾀｽ '1'→接続成功、'-1'→接続失敗
  function db_connect($connect_no='',$sub_dbname='') {
    if ($connect_no == '') { $connect_no = 0; }
    $cfl = 0;
    $connect_flag = 1;
    if ($sub_dbname != '') { $dbn = $sub_dbname; } else { $dbn = $this->db_name; }
    if ($this->dbd == 'Pg') {
      # PostgreSQL接続
      if ($this->db_hostname != '') {
        $host = 'host='.$this->db_hostname.' ';
        if ($this->db_hostport != '') { $port = 'port='.$this->db_hostport.' '; }
      } else {
        $host = '';
        $port = '';
      }
      if ($this->DBH[$connect_no] = pg_connect($host.$port.'dbname='.$dbn.' user='.$this->db_username.' password='.$this->db_usrpassword)) {
      } else {
        $this->DBH[$connect_no] = -1;
        $connect_flag = 0;
      }
    } elseif ($this->dbd == 'mysql') {
      # MySQL接続
      if ($this->db_hostname != '') {
        $host = $this->db_hostname;
        if ($this->db_hostport != '') { $host = $host.':'.$this->db_hostport; }
      } else {
        $host = 'localhost';
      }
      if ($this->DBH[$connect_no] = mysql_connect($host,$this->db_username,$this->db_usrpassword)) {
        # ﾃﾞｰﾀﾍﾞｰｽ指定
        if (!($dbst = mysql_select_db($dbn))) {
          $sth = mysql_query('create database '.$dbn, $this->DBH[$connect_no]);
          if (!($dbst = mysql_select_db($dbn))) { $this->DBH[$connect_no] = -1; $connect_flag = 0; }
        }
      } else {
        $this->DBH[$connect_no] = -1;
        $connect_flag = 0;
      }
    }
    $this->DB_TYPE[$connect_no] = $this->dbd;
    return $connect_flag;
  }

  # ﾃﾞｰﾀﾍﾞｰｽ切断 ////////////////////////////////////////////////////////////////
  # ﾃﾞｰﾀﾍﾞｰｽへ接続を切断します。
  # 引渡値：$connect_no => 接続番号
  # 返り値：$cfl        => 切断成功→'1'、切断失敗→'0'
  function db_disconnect ($connect_no='') {
    if ($connect_no == '') { $connect_no = 0; }
    $cfl = 0;
    if ($this->DBH[$connect_no] != -1) {
      if ($this->dbd == 'Pg') {
        # PostgreSQL切断
        if ($dbst = pg_close($this->DBH[$connect_no])) { $cfl = 1; }
      } elseif ($this->dbd == 'mysql') {
        # MySQL切断
        if ($dbst = mysql_close($this->DBH[$connect_no])) { $cfl = 1; }
      }
    }
    $this->DB_TYPE[$connect_no] = '';
    return $cfl;
  }

  # ﾃｰﾌﾞﾙﾁｪｯｸ /////////////////////////////////////////////////////////////////
  # ﾃｰﾌﾞﾙの有無をﾁｪｯｸし、ﾃｰﾌﾞﾙが存在しない場合、新規にﾃｰﾌﾞﾙを作成します。
  # 引渡値：$connect_no      => 接続番号
  # 　　　　$check_tablename => ﾃｰﾌﾞﾙ名
  # 　　　　$field_list      => ﾌｨｰﾙﾄﾞﾘｽﾄ
  # 　　　　$maketable_flag  => 指定なしor'0':ﾃｰﾌﾞﾙの有無のみﾁｪｯｸ、'1':新規にﾃｰﾌﾞﾙを生成する
  # 　　　　$dbnm            => 別DB名
  # 　　　　$heap_flag       => ﾋｰﾌﾟﾃｰﾌﾞﾙの場合指定('0'又は指定なし:通常ﾃｰﾌﾞﾙ、'1':ﾋｰﾌﾟﾃｰﾌﾞﾙ)
  # 　　　　$INDEXLIST       => ｲﾝﾃﾞｯｸｽ生成
  # 返り値：$cfl             => ﾁｪｯｸ結果 '0'→ﾃｰﾌﾞﾙ無し、'1'→ﾃｰﾌﾞﾙ有り
  function db_check($connect_no,$check_tablename,$field_list,$maketable_flag,$dbnm='',$heap_flag='',$INDEXLIST='') {
    $cfl = 0;
    if ($connect_no == '') { $connect_no = 0; }
    if ($heap_flag == '1') {
      # Heapﾃｰﾌﾞﾙ生成(MySQLのみ)
      if ($this->DB_TYPE[$connect_no] == 'mysql') {
        $cfl = $this->db_heap_check($connect_no,$check_tablename,$field_list,$maketable_flag,$dbnm,$INDEXLIST);
      }
    } else {
      # 通常ﾃｰﾌﾞﾙ生成
      if ($this->DB_TYPE[$connect_no] == 'Pg') {
        # PostgreSQLﾃｰﾌﾞﾙﾁｪｯｸ
        $tables = $this->pg_list_tables($this->DBH[$connect_no]);
        while ($row = pg_fetch_array($tables)) {
          if ($check_tablename == $row[0]) { $cfl = 1; break; }
        }
      } elseif ($this->DB_TYPE[$connect_no] == 'mysql') {
        # MySQLﾃｰﾌﾞﾙﾁｪｯｸ
        if ($dbnm == '') {
          $dbn = $this->db_name;
          $dbst = mysql_select_db($this->db_name,$this->DBH[$connect_no]);
        } else {
          $dbn = $dbnm;
          $dbst = mysql_select_db($dbnm,$this->DBH[$connect_no]);
        }
        $tables = mysql_list_tables($dbn,$this->DBH[$connect_no]);
        while ($row = mysql_fetch_array($tables,MYSQL_BOTH)) {
          if ($check_tablename == $row[0]) { $cfl = 1; break; }
        }
      }
      if (($maketable_flag == 1) and ($cfl == 0)) {
        # ﾃｰﾌﾞﾙ新規作成
        $query = 'CREATE TABLE '.$check_tablename.' ('.$field_list.')';
        if ($this->DB_TYPE[$connect_no] == 'Pg') {
          # PostgreSQLﾃｰﾌﾞﾙ新規作成
          $result = pg_query($this->DBH[$connect_no],$query);
        } elseif ($this->DB_TYPE[$connect_no] == 'mysql') {
          # MySQLﾃｰﾌﾞﾙ新規作成
          $result = mysql_query($query,$this->DBH[$connect_no]);
          # ｲﾝﾃﾞｯｸｽ作成
          if ($result and is_array($INDEXLIST)) {
            foreach ($INDEXLIST as $idt) {
              if ($idt) {
                $query  = 'ALTER TABLE '.$check_tablename.' ADD INDEX ('.$idt.')';
                $result = mysql_query($query,$this->DBH[$connect_no]);
              }
            }
          }
        }
        $cfl = 1;
      }
    }
    return $cfl;
  }

  # Heapﾃｰﾌﾞﾙｻｲｽﾞ取得 /////////////////////////////////////////////////////////
  # Heapﾃｰﾌﾞﾙ最大ｻｲｽﾞを取得します。(MySQL専用)
  # 引渡値：$connect_no      => 接続番号
  # 　　　　$check_tablename => ﾃｰﾌﾞﾙ名
  # 　　　　$dbnm            => 別DB名
  # 返り値：$max_heap_table_size => Heapﾃｰﾌﾞﾙｻｲｽﾞ
  function get_heap_size($connect_no,$check_tablename,$dbnm='') {
    if ($connect_no == '') { $connect_no = 0; }
    $max_heap_table_size = 0;
    $cfl = 0;
    if ($this->DB_TYPE[$connect_no] == 'mysql') {
      # Heapﾃｰﾌﾞﾙ最大ｻｲｽﾞ取得
#      $result = mysql_query("show variables",$this->DBH[$connect_no]);
#      while ($row = mysql_fetch_array($result,MYSQL_BOTH)) {
#        if ($row[0] == 'max_heap_table_size') { $max_heap_table_size = $row[1]; break; }
#      }
      $result = mysql_query("show variables LIKE 'max_heap_table_size'",$this->DBH[$connect_no]);
      $row    = mysql_fetch_array($result,MYSQL_BOTH);
      $max_heap_table_size = $row[1];
    }
    return $max_heap_table_size;
  }

  # Heapﾃｰﾌﾞﾙﾁｪｯｸ /////////////////////////////////////////////////////////////
  # Heapﾃｰﾌﾞﾙの有無をﾁｪｯｸし、ﾃｰﾌﾞﾙが存在しない場合、新規にﾃｰﾌﾞﾙを作成します。(MySQL専用)
  # 引渡値：$connect_no      => 接続番号
  # 　　　　$check_tablename => ﾃｰﾌﾞﾙ名
  # 　　　　$field_list      => ﾌｨｰﾙﾄﾞﾘｽﾄ
  # 　　　　$maketable_flag  => ﾃｰﾌﾞﾙの有無のみﾁｪｯｸし新規にﾃｰﾌﾞﾙを生成しない場合"1"指定
  # 　　　　$dbnm            => 別DB名
  # 返り値：$cfl             => ﾁｪｯｸ結果 '0'→ﾃｰﾌﾞﾙ無し、'1'→ﾃｰﾌﾞﾙ有り
  function db_heap_check($connect_no,$check_tablename,$field_list,$maketable_flag,$dbnm='',$INDEXLIST='') {
    if ($connect_no == '') { $connect_no = 0; }
    $cfl = 0;
    if ($this->DB_TYPE[$connect_no] == 'mysql') {
      # MySQLﾃｰﾌﾞﾙﾁｪｯｸ
      if ($dbnm == '') {
        $dbn = $this->db_name;
        $dbst = mysql_select_db($this->db_name,$this->DBH[$connect_no]);
      } else {
        $dbn = $dbnm;
        $dbst = mysql_select_db($dbnm,$this->DBH[$connect_no]);
      }
      $tables = mysql_list_tables($dbn,$this->DBH[$connect_no]);
      while ($row = mysql_fetch_array($tables,MYSQL_BOTH)) {
        if ($check_tablename == $row[0]) { $cfl = 1; break; }
      }

      if (($maketable_flag == 1) and ($cfl == 0)) {
        # Heapﾃｰﾌﾞﾙ最大ｻｲｽﾞ取得
#        $max_heap_table_size = $this->get_heap_size($connect_no,$check_tablename,$dbnm);

        # ﾃｰﾌﾞﾙ新規作成
#        $query = 'CREATE TABLE '.$check_tablename.' ('.$field_list.') type=heap max_rows='.$GLOBALS['max_rows'];
        $query = 'CREATE TABLE '.$check_tablename.' ('.$field_list.') type=heap';
        $result = mysql_query($query,$this->DBH[$connect_no]);
        # ｲﾝﾃﾞｯｸｽ作成
        if ($result and is_array($INDEXLIST)) {
          foreach ($INDEXLIST as $idt) {
            if ($idt) {
              $query  = 'ALTER TABLE '.$check_tablename.' ADD INDEX ('.$idt.')';
              $result = mysql_query($query,$this->DBH[$connect_no]);
            }
          }
        }
      }
    }
    return $cfl;
  }

  # ﾃｰﾌﾞﾙﾘｽﾄ取得 //////////////////////////////////////////////////////////////
  # ﾃｰﾌﾞﾙの有無をﾁｪｯｸし、ﾃｰﾌﾞﾙが存在しない場合、新規にﾃｰﾌﾞﾙを作成します。
  # 引渡値：$connect_no      => 接続番号
  # 　　　　$check_tablename => ﾃｰﾌﾞﾙ名
  # 　　　　$field_list      => ﾌｨｰﾙﾄﾞﾘｽﾄ
  # 　　　　$maketable_flag  => ﾃｰﾌﾞﾙの有無のみﾁｪｯｸし新規にﾃｰﾌﾞﾙを生成しない場合"1"指定
  # 　　　　$dbnm            => 別DB名
  # 返り値：$cfl             => ﾁｪｯｸ結果 '0'→ﾃｰﾌﾞﾙ無し、'1'→ﾃｰﾌﾞﾙ有り
  function get_table_list($connect_no,$dbnm='') {
    global $db_name;
    if ($connect_no == '') { $connect_no = 0; }
    $TABLES = array();
    if ($this->DB_TYPE[$connect_no] == 'Pg') {
      # PostgreSQLﾃｰﾌﾞﾙﾁｪｯｸ
      $tables = $this->pg_list_tables($this->DBH[$connect_no]);
      while ($row = pg_fetch_array($tables)) {
        $TABLES[] = $row[0];
      }
    } elseif ($this->DB_TYPE[$connect_no] == 'mysql') {
      # MySQLﾃｰﾌﾞﾙﾁｪｯｸ
      if ($dbnm == '') {
        $dbn = $db_name;
        $dbst = mysql_select_db($db_name,$this->DBH[$connect_no]);
      } else {
        $dbn = $dbnm;
        $dbst = mysql_select_db($dbnm,$this->DBH[$connect_no]);
      }
      $tables = mysql_list_tables($dbn,$this->DBH[$connect_no]);
      while ($row = mysql_fetch_array($tables,MYSQL_BOTH)) {
        $TABLES[] = $row[0];
      }
    }
    return $TABLES;
  }

  # PostgreSQL用ﾃｰﾌﾞﾙ一覧取得 /////////////////////////////////////////////////
  # PostgreSQL用にﾃｰﾌﾞﾙ一覧を取得します。
  # 引渡値：$connect_no      => 接続番号
  # 返り値：ﾃｰﾌﾞﾙ一覧取得結果
  function pg_list_tables($connect_no='') {
    if ($connect_no == '') { $connect_no = 0; }
    assert(is_resource($dbh));
    $query = "
  SELECT
   c.relname as \"Name\", 
   CASE c.relkind WHEN 'r' THEN
    'table' WHEN 'v' THEN 'view' WHEN
    'i' THEN 'index' WHEN 'S' THEN 'special'
    END as \"Type\",
   u.usename as \"Owner\" 
  FROM
   pg_class c LEFT JOIN pg_user u ON
   c.relowner = u.usesysid 
  WHERE
   c.relkind IN ('r','v','S','')
   AND c.relname !~ '^pg_' 
  ORDER BY 1;
";
    return pg_query($this->DBH[$connect_no],$query);
  }

  # ｸｴﾘｰ送信 //////////////////////////////////////////////////////////////////
  # SELECT(複数ﾃﾞｰﾀ取得時),INSERT,SELECT実行
  # 引渡値：$connect_no => 接続No
  # 　　　　$sql0       => PostgreSQL用ｸｴﾘｰ(指定が無い場合MySQL共用のｸｴﾘｰ)
  # 　　　　$sql1       => MySQL用ｸｴﾘｰ
  # 　　　　$dbnm       => 接続ﾃﾞｰﾀﾍﾞｰｽ名
  # 返り値：ｸｴﾘｰ送信ﾘｿｰｽ
  function sql_set_data($connect_no,$sql0,$sql1='',$dbnm='',$code_change='') {
    global $emoji_obj;
    if ($connect_no == '') { $connect_no = 0; }
    if ($this->emj_obj_flag == 1) {
      if ($emoji_obj->chg_code_sjis != '') {
        $sjis_type = $emoji_obj->chg_code_sjis;
      } else {
        $sjis_type = 'SJIS';
      }
      if ($emoji_obj->chg_code_euc != '') {
        $euc_type = $emoji_obj->chg_code_euc;
      } else {
        $euc_type = 'EUC';
      }
    } else {
      $sjis_type = 'SJIS';
      $euc_type  = 'EUC';
    }
    # ｸｴﾘｰ送信
    if ($sql1 == '') { $sql1 = $sql0; }
    # ﾃﾞｰﾀﾍﾞｰｽ名設定
    if ($dbnm == '') { $dbnm = $this->db_name; }
    if ($this->DB_TYPE[$connect_no] == 'Pg') {
      # PostgrSQL
      if ($code_change == 'EtoS') {
        $sql0 = @mb_convert_encoding($sql0,$sjis_type,$euc_type);
      } elseif ($code_change == 'EtoU') {
        $sql0 = @mb_convert_encoding($sql0,'UTF-8',$euc_type);
      } elseif ($code_change == 'StoE') {
        $sql0 = @mb_convert_encoding($sql0,$euc_type,$sjis_type);
      } elseif ($code_change == 'StoU') {
        $sql0 = @mb_convert_encoding($sql0,'UTF-8',$sjis_type);
      } elseif ($code_change == 'UtoS') {
        $sql0 = @mb_convert_encoding($sql0,$sjis_type,'UTF-8');
      } elseif ($code_change == 'UtoE') {
        $sql0 = @mb_convert_encoding($sql0,$euc_type,'UTF-8');
      } elseif ($code_change == 'autoS') {
        $de = mb_detect_encoding($sql0,'auto');
#        $de = mb_detect_encoding($sql0);
        if ($de) {
          if (mb_preferred_mime_name($de) != mb_preferred_mime_name('SJIS')) {
            $sql0 = @mb_convert_encoding($sql0,$sjis_type,mb_detect_encoding($sql0,'auto'));
#            $sql0 = @mb_convert_encoding($sql0,$sjis_type,mb_detect_encoding($sql0));
          }
        }
      } elseif ($code_change == 'autoE') {
        $de = mb_detect_encoding($sql0,'auto');
#        $de = mb_detect_encoding($sql0);
        if ($de) {
          if (mb_preferred_mime_name($de) != mb_preferred_mime_name('EUC')) {
            $sql0 = @mb_convert_encoding($sql0,$euc_type,mb_detect_encoding($sql0,'auto'));
#            $sql0 = @mb_convert_encoding($sql0,$euc_type,mb_detect_encoding($sql0));
          }
        }
      } elseif ($code_change == 'autoU') {
        $de = mb_detect_encoding($sql0,'auto');
#        $de = mb_detect_encoding($sql0);
        if ($de) {
          if (mb_preferred_mime_name($de) != mb_preferred_mime_name('UTF-8')) {
            $sql0 = @mb_convert_encoding($sql0,'UTF-8',mb_detect_encoding($sql0,'auto'));
#            $sql0 = @mb_convert_encoding($sql0,'UTF-8',mb_detect_encoding($sql0));
          }
        }
      }
      $sth = pg_query($this->DBH[$connect_no],$sql0);
    } else {
      # MySQL
      if ($code_change == 'EtoS') {
        $sql1 = @mb_convert_encoding($sql1,$sjis_type,$euc_type);
      } elseif ($code_change == 'EtoU') {
        $sql1 = @mb_convert_encoding($sql1,'UTF-8',$euc_type);
      } elseif ($code_change == 'StoE') {
        $sql1 = @mb_convert_encoding($sql1,$euc_type,$sjis_type);
      } elseif ($code_change == 'StoU') {
        $sql1 = @mb_convert_encoding($sql1,'UTF-8',$sjis_type);
      } elseif ($code_change == 'UtoS') {
        $sql1 = @mb_convert_encoding($sql1,$sjis_type,'UTF-8');
      } elseif ($code_change == 'UtoE') {
        $sql1 = @mb_convert_encoding($sql1,$euc_type,'UTF-8');
      } elseif ($code_change == 'autoS') {
        $de = mb_detect_encoding($sql0,'auto');
#        $de = mb_detect_encoding($sql0);
        if ($de) {
          if (mb_preferred_mime_name($de) != mb_preferred_mime_name('SJIS')) {
            $sql1 = @mb_convert_encoding($sql0,$sjis_type,mb_detect_encoding($sql1,'auto'));
#            $sql1 = @mb_convert_encoding($sql0,$sjis_type,mb_detect_encoding($sql1));
          }
        }
      } elseif ($code_change == 'autoE') {
        $de = mb_detect_encoding($sql0,'auto');
#        $de = mb_detect_encoding($sql0);
        if ($de) {
          if (mb_preferred_mime_name($de) != mb_preferred_mime_name('EUC')) {
            $sql1 = @mb_convert_encoding($sql0,$euc_type,mb_detect_encoding($sql1,'auto'));
#            $sql1 = @mb_convert_encoding($sql0,$euc_type,mb_detect_encoding($sql1));
          }
        }
      } elseif ($code_change == 'autoU') {
        $de = mb_detect_encoding($sql0,'auto');
#        $de = mb_detect_encoding($sql0);
        if ($de) {
          if (mb_preferred_mime_name() != mb_preferred_mime_name('UTF-8')) {
            $sql1 = @mb_convert_encoding($sql0,'UTF-8',mb_detect_encoding($sql1,'auto'));
#            $sql1 = @mb_convert_encoding($sql0,'UTF-8',mb_detect_encoding($sql1));
          }
        }
      }
      $dbst = mysql_select_db($dbnm,$this->DBH[$connect_no]);
      $sth  = mysql_query($sql1,$this->DBH[$connect_no]);
    }
    return $sth;
  }

  # ﾃﾞｰﾀ取得用ｸｴﾘｰ送信 ////////////////////////////////////////////////////////
  # SELECT(特定ﾃﾞｰﾀ取得時)-ﾌｨｰﾙﾄﾞNo+ﾌｨｰﾙﾄﾞ名でﾃﾞｰﾀ取得
  # 引渡値：$connect_no   => 接続No
  # 　　　　$sql0         => PostgreSQL用ｸｴﾘｰ
  # 　　　　　　　　　　　　 $sql1の指定が無い場合MySQL共用のｸｴﾘｰ
  # 　　　　　　　　　　　　 $get_mode = '' 又は '0' の場合はDB接続ﾘｿｰｽ
  # 　　　　$sql1         => MySQL用ｸｴﾘｰ
  # 　　　　$dbnm         => 接続ﾃﾞｰﾀﾍﾞｰｽ名
  # 　　　　$get_mode     => 指定無し 又は 'single' →単一ﾃﾞｰﾀ取得ﾓｰﾄﾞ、'loop'→複数ﾃﾞｰﾀ取得
  # 　　　　$data_mode    => 指定無し→ﾌｨｰﾙﾄﾞNo+ﾌｨｰﾙﾄﾞ名、'num'→ﾌｨｰﾙﾄﾞNo、'ass'→ﾌｨｰﾙﾄﾞ名
  # 　　　　$data_chanege => 指定無し 又は 0 →処理なし、1→ｱﾝｴｽｹｰﾌﾟ処理有り
  # 返り値：$GETDATA      => 取得ﾃﾞｰﾀ
  function sql_get_data($connect_no,$sql0,$sql1='',$dbnm='',$get_mode='',$data_mode='',$data_change='',$code_change='') {
    global $emoji_obj;
    if ($connect_no == '') { $connect_no = 0; }
    if ($this->emj_obj_flag == 1) {
      if ($emoji_obj->chg_code_sjis != '') {
        $sjis_type = $emoji_obj->chg_code_sjis;
      } else {
        $sjis_type = 'SJIS';
      }
      if ($emoji_obj->chg_code_euc != '') {
        $euc_type = $emoji_obj->chg_code_euc;
      } else {
        $euc_type = 'EUC';
      }
    } else {
      $sjis_type = 'SJIS';
      $euc_type  = 'EUC';
    }
    # ｸｴﾘｰ・ﾘｿｰｽ設定
    if (($get_mode == '') or ($get_mode == 'single')) {
      # 特定ﾃﾞｰﾀ取得ﾓｰﾄﾞ→ｸｴﾘｰ設定
      if ($sql1 == '') { $sql1 = $sql0; }
    } else {
      # 複数ﾃﾞｰﾀ取得ﾓｰﾄﾞ→ﾘｿｰｽ設定
      $sth = $sql0;
    }
    # ﾃﾞｰﾀﾍﾞｰｽ名設定
    if ($dbnm == '') { $dbnm = $this->db_name; }
    $GETDATA = array();
    if ($this->DB_TYPE[$connect_no] == 'Pg') {
      # PostgreSQL
      # 単一ﾃﾞｰﾀ取得時ｸｴﾘｰ送信
      if (($get_mode == '') or ($get_mode == 'single')) { $sth = pg_query($this->DBH[$connect_no],$sql0); }
      # ﾃﾞｰﾀ取得
      if ($sth) {
        if ($data_mode == '') {
          # ﾌｨｰﾙﾄﾞNo+ﾌｨｰﾙﾄﾞ名取得ﾓｰﾄﾞ
          $GETDATA = pg_fetch_array($sth,PGSQL_BOTH);
        } elseif ($data_mode == 'num') {
          # ﾌｨｰﾙﾄﾞNo取得ﾓｰﾄﾞ
          $GETDATA = pg_fetch_array($sth,PGSQL_NUM);
        } elseif ($data_mode == 'ass') {
          # ﾌｨｰﾙﾄﾞ名取得ﾓｰﾄﾞ
          $GETDATA = pg_fetch_array($sth,PGSQL_ASSOC);
        }
      }
    } else {
      # MySQL
      # ﾃﾞｰﾀﾍﾞｰｽ選択
      $dbst = mysql_select_db($dbnm,$this->DBH[$connect_no]);
      # 単一ﾃﾞｰﾀ取得時ｸｴﾘｰ送信
      if (($get_mode == '') or ($get_mode == 'single')) { $sth = mysql_query($sql1,$this->DBH[$connect_no]); }
      # ﾃﾞｰﾀ取得
      if ($sth) {
        if ($data_mode == '') {
          # ﾌｨｰﾙﾄﾞNo+ﾌｨｰﾙﾄﾞ名取得ﾓｰﾄﾞ
          $GETDATA = mysql_fetch_array($sth,MYSQL_BOTH);
        } elseif ($data_mode == 'num') {
          # ﾌｨｰﾙﾄﾞNo取得ﾓｰﾄﾞ
          $GETDATA = mysql_fetch_array($sth,MYSQL_NUM);
        } elseif ($data_mode == 'ass') {
          # ﾌｨｰﾙﾄﾞ名取得ﾓｰﾄﾞ
          $GETDATA = mysql_fetch_array($sth,MYSQL_ASSOC);
        }
      }
    }
    # ｱﾝｴｽｹｰﾌﾟ処理
    if ($data_change == '1') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) { $GETDATA[$kdt] = stripslashes($GETDATA[$kdt]); }
      }
    }
    # ｺｰﾄﾞ変換
    if ($code_change == 'EtoS') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$sjis_type,$euc_type);
        }
      }
    } elseif ($code_change == 'UtoS') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$sjis_type,'UTF-8');
        }
      }
    } elseif ($code_change == 'StoE') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$euc_type,$sjis_type);
        }
      }
    } elseif ($code_change == 'UtoE') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$euc_type,'UTF-8');
        }
      }
    } elseif ($code_change == 'StoU') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],'UTF-8',$sjis_type);
        }
      }
    } elseif ($code_change == 'EtoU') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],'UTF-8',$euc_type);
        }
      }
    } elseif ($code_change == 'autoS') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $de = mb_detect_encoding($GETDATA[$kdt],'auto');
#          $de = mb_detect_encoding($GETDATA[$kdt]);
          if ($de) {
            if (mb_preferred_mime_name($de) != mb_preferred_mime_name('SJIS')) {
              $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$sjis_type,mb_detect_encoding($GETDATA[$kdt],'auto'));
#              $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$sjis_type,mb_detect_encoding($GETDATA[$kdt]));
            }
          }
        }
      }
    } elseif ($code_change == 'autoE') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $de = mb_detect_encoding($GETDATA[$kdt],'auto');
#          $de = mb_detect_encoding($GETDATA[$kdt]);
          if ($de) {
            if (mb_preferred_mime_name() != mb_preferred_mime_name('EUC')) {
              $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$euc_type,mb_detect_encoding($GETDATA[$kdt],'auto'));
#              $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],$euc_type,mb_detect_encoding($GETDATA[$kdt]));
            }
          }
        }
      }
    } elseif ($code_change == 'autoU') {
      if (is_array($GETDATA)) {
        $KEYDT = array();
        $KEYDT = array_keys($GETDATA);
        foreach ($KEYDT as $kdt) {
          $de = mb_detect_encoding($GETDATA[$kdt],'auto');
#          $de = mb_detect_encoding($GETDATA[$kdt]);
          if ($de) {
            if (mb_preferred_mime_name() != mb_preferred_mime_name('UTF-8')) {
              $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],'UTF-8',mb_detect_encoding($GETDATA[$kdt],'auto'));
#              $GETDATA[$kdt] = @mb_convert_encoding($GETDATA[$kdt],'UTF-8',mb_detect_encoding($GETDATA[$kdt]));
            }
          }
        }
      }
    }
    return $GETDATA;
  }

  # ﾃｰﾌﾞﾙﾁｪｯｸ ///////////////////////////////////////////////////////////////////
  function table_check($connect_no,$table_name='',$dbnm='') {
    $RTNDATA = array();
    # ｺﾏﾝﾄﾞ調整
    if ($this->dbd == 'Pg') { $auto_no = 'serial'; } elseif ($this->dbd == 'mysql') { $auto_no = 'auto_increment'; }
    # 絵文字変換対象'emj_emoji'ﾃｰﾌﾞﾙﾁｪｯｸ
    if (($table_name == '') or ($table_name == 'emj_emoji')) {
      $check_tablename = 'emj_emoji';
      $field_list      = "Base_emj_id char(10) primary key,script_code char(20),DoCoMo_no char(10),SoftBank_no char(10),au_no char(10),yusen_no char(10),sub0 text,sub1 text,sub2 text,sub3 text,sub4 text,regdate int8,editdate int8";
      $INDEXLIST       = array();
      $INDEXLIST[]     = 'DoCoMo_no';
      $INDEXLIST[]     = 'SoftBank_no';
      $INDEXLIST[]     = 'au_no';
      $sts             = $this->db_check($connect_no,$check_tablename,$field_list,'1',$dbnm,'',$INDEXLIST);
      $RTNDATA[$check_tablename] = $sts;
    }
    # DoCoMo絵文字情報'emj_DoCoMo'ﾃｰﾌﾞﾙﾁｪｯｸ
    if (($table_name == '') or ($table_name == 'emj_DoCoMo')) {
      $check_tablename = 'emj_DoCoMo';
      $field_list      = "DoCoMo_emj_id char(10) primary key,emj_name char(50),emj_file char(255),sjis16 char(10),sjis10 char(10),web_code char(10),unicode char(10),color char(10),mail_code char(10),utf_8 char(10),sub0 text,sub1 text,sub2 text,sub3 text,sub4 text,regdate int8,editdate int8";
      $INDEXLIST       = array();
      $INDEXLIST[]     = 'sjis16';
      $INDEXLIST[]     = 'sjis10';
      $sts             = $this->db_check($connect_no,$check_tablename,$field_list,'1',$dbnm,'',$INDEXLIST);
      $RTNDATA[$check_tablename] = $sts;
    }
    # au絵文字情報'emj_au'ﾃｰﾌﾞﾙﾁｪｯｸ
    if (($table_name == '') or ($table_name == 'emj_au')) {
      $check_tablename = 'emj_au';
      $field_list      = "au_emj_id char(10) primary key,emj_name char(50),emj_file char(255),sjis16 char(10),sjis10 char(10),web_code char(10),unicode char(10),color char(10),mail_code char(10),utf_8 char(10),sub0 text,sub1 text,sub2 text,sub3 text,sub4 text,regdate int8,editdate int8";
      $INDEXLIST       = array();
      $INDEXLIST[]     = 'sjis16';
      $INDEXLIST[]     = 'sjis10';
      $INDEXLIST[]     = 'mail_code';
      $sts             = $this->db_check($connect_no,$check_tablename,$field_list,'1',$dbnm,'',$INDEXLIST);
      $RTNDATA[$check_tablename] = $sts;
    }
    # SoftBank絵文字情報'emj_SoftBank'ﾃｰﾌﾞﾙﾁｪｯｸ
    if (($table_name == '') or ($table_name == 'emj_SoftBank')) {
      $check_tablename = 'emj_SoftBank';
      $field_list      = "SoftBank_emj_id char(10) primary key,emj_name char(50),emj_file char(255),sjis16 char(10),sjis10 char(10),web_code char(10),unicode char(10),color char(10),mail_code char(10),utf_8 char(10),sub0 text,sub1 text,sub2 text,sub3 text,sub4 text,regdate int8,editdate int8";
      $INDEXLIST       = array();
      $INDEXLIST[]     = 'sjis16';
      $sts             = $this->db_check($connect_no,$check_tablename,$field_list,'1',$dbnm,'',$INDEXLIST);
      $RTNDATA[$check_tablename] = $sts;
    }
    # 携帯端末情報'Phone_Spec'ﾃｰﾌﾞﾙﾁｪｯｸ
    if (($table_name == '') or ($table_name == 'Phone_Spec')) {
      $check_tablename = 'Phone_Spec';
      $field_list      = "career char(20),kubun char(10),maker char(20),model char(10),yusen char(5),user_agent_patt char(255),sikibetu char(5),check_point char(5),check_string char(100),img_mime char(20),img_ext char(20),mov_mime char(20),mov_ext char(20),mov_size char(10),mov_download_max_size char(10),mov_stream_max_size char(10),display_width char(5),display_height char(5),display_color char(10),cache_size char(10),fitmov_patt_name1 char(255),fitmov_patt_name2 char(255),biko0 char(255),biko1 char(255),biko2 char(255),sub0 text,sub1 text,sub2 text,sub3 text,sub4 text,regdate int8,editdate int8";
      $INDEXLIST       = array();
      $INDEXLIST[]     = 'model';
      $INDEXLIST[]     = 'check_string';
      $sts             = $this->db_check($connect_no,$check_tablename,$field_list,'1',$dbnm,'',$INDEXLIST);
      $RTNDATA[$check_tablename] = $sts;
    }
    return $RTNDATA;
  }

}

?>
