<?php
/**
 * Created by PhpStorm.
 * User: danhm
 * Date: 22-Jan-18
 * Time: 09:25 PM
 */

include 'Curl/ArrayUtil.php' ;
include 'Curl/Decoder.php' ;
include 'Curl/Url.php' ;
include 'Curl/CaseInsensitiveArray.php' ;
include 'Curl/StrUtil.php';
include 'Curl/Curl.php'  ;
include 'Curl/MultiCurl.php' ;

include 'DiDOM/Document.php';
include 'DiDOM/Element.php' ;
include "DiDOM/Query.php";
include "DiDOM/Errors.php";
include "DiDOM/StyleAttribute.php" ;
include "DiDOM/ClassAttribute.php" ;
include "DiDOM/Encoder.php" ;


use Curl\Curl ;
use DiDom\Document ;

$url = "http://www.24h.com.vn/ttcb/thoitiet/thoitiet.php" ;



function get_html($url) {
    $curl = new Curl() ;
    $curl->get($url) ;
//    var_dump($curl) ;
    if($curl->error) {
        echo $curl->errorMessage ;
    } else {
//        echo "Connected" ;
    }

    return ($curl->response);
}

function get_update_time($html) {
    $dom = new Document() ;
    $dom->load($html) ;

    $time_row = $dom->find("div[class=thoiTietBox]")[0]->find("tr[class=tb-top]")[0] ;
    $time_now = $time_row->find("span[class=cap-nhat]")[0]->text();

    echo "<tr class = 'info'>
        <th></th>
        <th><i>$time_now</i></th>
         " ;


    for($i = 0 ; $i < 3  ; $i++) {
        $time = $time_row->find("i")[$i]->text() ;
        echo "<th><i>$time</i></th>" ;
    }

    echo "</tr>" ;
}

function get_weather_detail($html) {
    $dom = new Document() ;
    $dom->load($html) ;

    $rows = $dom->find("div[class=thoiTietBox]")[0]->find("tr") ;


    foreach ($rows as $row) {
        echo "<tr class=\"active\" >" ;
        if($row->has("td[class=thoitiet-cell]")) {


            if($row->has("td[class=thoitiet-cell]")) {
                $location = $row->find("h3")[0]->text()  ;
                $location = substr($location , 13 , strlen($location)) ;
                echo "<td><h3>$location</h3></td>" ;


            }



            if($row->has("span[class=nhietdo-big]")) {
                $des = $row->find("tr")[0]->find("td")[1] ;
//                var_dump($des) ;
                $temp_now = $row->find("span[class=nhietdo-big]")[0]->text() ;
                $temp_now = str_replace("oC" , "&#176C" , $temp_now  ) ;
                
                $imgnow = $row->find("table")[0]->find("img")[0]->attr("src") ;
//                var_dump($imgnow) ;
                echo "<td>
                        <img class = '' src='$imgnow' alt='' style='width: 50px;'>
                        <h3>$temp_now</h3>
                    </td>" ;

            } else {
                echo "<td>Updating</td>";
            }

            for($i = 0 ; $i < 3 ; $i++) {
                $temp1 = $row->find("span[class=nhietdo-small]")[$i]->text() ;
                $temp1 = str_replace("oC" , "&#176C" , $temp1 ) ;

                $description = $row->find("span[class=tt-sub-small]")[$i]->text() ;
//                echo $description ;

                $img = $row->find("div[class=tt-sub-pic]")[$i]->find("img")[0]->attr("src") ;
                echo "<td>
                        <img src='$img' alt=''>
                        <h3>$temp1</h3>
                        <p>$description</p>
                    </td>" ;

            }


        }




        echo "</tr>" ;

    }
}
?>

<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Pacifico&amp;subset=vietnamese" rel="stylesheet">
    <title>Document</title>
</head>
<body>
<div class="container">
    <h1 class="text-center">Weather Forecast</h1>
    <table class="table table-bordered table-hover">
        <tr class="success">
            <th>Địa điểm</th>
            <th>Hiện tại</th>
            <th>Hôm nay</th>
            <th>Ngày mai</th>
            <th>Ngày kia</th>
        </tr>

        <?php
        get_update_time(get_html($url)) ;
        get_weather_detail(get_html($url)) ;

        ?>
    </table>
    <div class = "text-right">
        <p>Dữ liệu được lấy từ Thời tiết 24h</p>
        <p>Powered by PHP, Bootstrap 3.3.7</p>
        <p><i>Danh Manh Nguyen 2018</i></p>
    </div>

</div>
</body>
</html>
