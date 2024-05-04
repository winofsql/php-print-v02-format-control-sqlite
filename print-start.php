<?php
require_once("setting.php");
require_once("db_connect.php");
require_once("print-setting.php");

// ******************************
// 初期処理や変数の設定
// ******************************
// フォント選択とフォントのサイズ指定
$pdf->SetFont('roundedmplus2cr', '', 14);

$counter = 0;           // ページ用カウンタ
$rmax = 15;             // ページ内の最大明細行数
$lcount = 0;            // 次に印字する行位置

$row_height = 8;        // 行の高さ
$header_height = 40;    // ヘッダ部分の物理的な高さ

$init = true;           // 初回フラグ

// 現在の物理位置
$cur_position = $header_height;

// データの印字
$_POST["query"] = <<<QUERY
SELECT 社員マスタ.*,コード名称マスタ.名称 as 所属名 
    FROM 社員マスタ left outer join コード名称マスタ
    on 社員マスタ.所属 = コード名称マスタ.コード
    and コード名称マスタ.区分 = 2
QUERY;

// クエリーの実行
$result = $mysqli->query( $_POST["query"] );
while ( $row = $result->fetch_array( MYSQLI_BOTH ) ) {

    // 初回のみヘッダを印字する
    if(  $init  ) {
        $init = false;
        // ページを追加
        $pdf->AddPage();
        // ヘッダーの出力
        print_header( $pdf );
    }

    // 改ページ コントロール
    $lcount += 1;
    // 仕様の最大行の主力を超えたら、次のページを作成する
    if ( $lcount > $rmax ) {
        // ページ追加
        $pdf->AddPage();

        // ページカウンタをカウントアップ
        $counter += 1;
        // ヘッダーの出力
        print_header( $pdf );

        // 行カウントを初期化する( 次に印字する行位置 )
        $lcount = 1;
        // 印字位置を先頭に持っていく
        $cur_position = $header_height;
    }

    $pos = $posx;

    user_text( $pdf, $pos, $cur_position, $row["社員コード"] );
    user_text( $pdf, $pos += $cpos[1], $cur_position, $row["氏名"] );

    user_text( $pdf, $pos += $cpos[2], $cur_position, $row["フリガナ"] );
    user_text( $pdf, $pos += $cpos[3], $cur_position, $row["所属名"] );
    //user_text( $pdf, $pos += 30, $cur_position, "性別" );
    // 性別用の画像を使用
    if ( $row["性別"] == "0" ) {
        $pdf->Image("man.png", ($pos += $cpos[4])+3, $cur_position, 5, 5);
    }
    else {
        $pdf->Image("lady.png", ($pos += $cpos[4])+3, $cur_position, 5, 5);
    }

    user_text( $pdf, $pos += $cpos[5], $cur_position, number_format($row["給与"]), 20, 0, "R" );

    user_text( $pdf, $pos += $cpos[6], $cur_position, number_format($row["手当"]+0), 20, 0, "R" );
    user_text( $pdf, $pos += $cpos[7], $cur_position, substr($row["生年月日"],0,10) );

    $cur_position += $GLOBALS['row_height'];

}

$mysqli->close();

// ブラウザへ PDF を出力します
$pdf->Output("test_output.pdf", "I");

// ************************************
// ヘッダの印字
// ************************************
function print_header( $pdf ) {

    global $counter,$posx,$cpos;

    $pos = $posx;

    $page_info = $pdf->getPageDimensions();

    // ヘッダ内での印字位置コントロール
    $cur_position = $page_info['tm'];	// トップマージン
    
    // ページの先頭
    $pdf->SetFont('roundedmplus2cr', '', 30);
    user_text( $pdf, 100,   $cur_position-4, "社員一覧表" );
    $pdf->SetFont('roundedmplus2cr', '', 14);

    user_text( $pdf, 224,   $cur_position, "ページ :" );
    user_text( $pdf, 250,   $cur_position, number_format($counter+1), 5, 0, "R" );
    
    // データのタイトル
    $cur_position += $GLOBALS['row_height'] * 2;    // 2行進む( 1行空ける )
    user_text( $pdf, $pos,       $cur_position, "コード" );
    user_text( $pdf, $pos += $cpos[1], $cur_position, "氏名" );

    user_text( $pdf, $pos += $cpos[2], $cur_position, "フリガナ" );

    user_text( $pdf, $pos += $cpos[3], $cur_position, "所属名" );
    user_text( $pdf, $pos += $cpos[4], $cur_position, "性別" );

    user_text( $pdf, $pos += $cpos[5], $cur_position, "給与", 20, 0, "R" );
    user_text( $pdf, $pos += $cpos[6], $cur_position, "手当", 20, 0, "R" );
    user_text( $pdf, $pos += $cpos[7], $cur_position, "生年月日" );
    
}

?>
