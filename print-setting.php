<?php
define ('K_PATH_FONTS', "tcpdf/");
require_once('tcpdf/tcpdf.php');

// ************************************************
//   P or PORTRAIT(縦:既定)
//   L or LANDSCAPE(横))
// ---------------------------
//   pt: ポイント
//   mm: mm(既定)
//   cm: cm
//   in: インチ
// ---------------------------
//   用紙サイズ
// ---------------------------
// boolean $unicode = true
// ---------------------------
// String $encoding = 'UTF-8'
// ---------------------------
// boolean $diskcache = false
// ---------------------------
// PDF/A モード
// ---------------------------
// 
// 全てデフォルトなので $pdf = new TCPDF("L") でもOK
// ************************************************
$pdf = new TCPDF("L");
/*
$pdf = new TCPDF(
    "L",
    "mm",
    "A4",
    true,
    "UTF-8",
    false,
    false
);
*/

// ************************************************
// 設定
// ************************************************
$pdf->setFontSubsetting(false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false);

// ************************************************
//  テキスト印字
// $p は L で左揃え、R で右揃え
// $w : 矩形領域の幅(1だと最低幅) : 右揃えに必要
// $h : 矩形領域の高さ
// http://tcpdf.penlabo.net/method/c/Cell.html
// ************************************************
function user_text( $pdf, $x, $y, $text, $w=1, $h=0, $p="L" ) {

    text( $pdf, $x, $y, $text, $w, $h, $p );

    return $y;

}

// ************************************************
// 位置指定印字
// ※ 改行コードで自動改行
// ※ ページあふれで自動改ページ
// ※ 内部印字位置は保存( 元に戻す )
// ************************************************
function text( $pdf, $x=0, $y=0, $txt='', $w=1, $h=0, $p="L" ) {

    $a = $pdf->GetX();
    $b = $pdf->GetY();

    $hm = $pdf->getPageHeight( );
    $dm = $pdf->getPageDimensions();
    $tm = $dm['tm'];
    $bm = $dm['bm'];

    $txt = str_replace( "\r","", $txt );
    $data = explode("\n", $txt );
    if ( count( $data ) > 1 ) {
        for( $i = 0; $i < count($data); $i++ ) {
            if ( $i == 0 ) {
                $pdf->SetXY( $x, $y );
            }
            else {
                $y += $pdf->getLastH();
                if ( $y >= ( $hm - $tm - $bm ) ) {
                    $pdf->AddPage();
                    $y = $tm;
                }
                $pdf->SetXY( $x, $y );
            }
            $pdf->Cell($w, $h, $data[$i], 0, 0, $p);
        }
    }
    else {
        $pdf->SetXY( $x, $y );
        $pdf->Cell($w, $h, $txt, 0, 0, $p);
    }
    $y += $pdf->getLastH();

    $pdf->SetXY($a,$b);

}

