<?php


use Slim\Http\Request;
use Slim\Http\Response;
use Model\Dao\User;
use Model\Dao\Item;

function pat($a, $l) {
	$x = array('');
	for ($i = 0; $i < $l; ++$i) {
		$_x = array();
		foreach ($a as $ak => $av) {
			$j=0;
			foreach ($x as $xk => $xv) {
				$xv .= $av;
					if($j<count($x))
						$xv .= ',';
					$_x[] = $xv;
					$j=$j+1;
				}
			}
			$x = $_x;
		}
	return $x;
}

// スッキリ画面コントローラ
$app->post('/results/', function (Request $request, Response $response) {
	//GETされた内容を取得します。
	$money = $request->getParsedBody();
	$limit = $money["zangaku"];
	$minarray = array();
	$min=100;

	$userItem = new Item($this->db);
	$c = $userItem->distinct(); //distinctのlimitを上げることでメモリー負荷
	$a = array();
	foreach($c as $b){
		array_push($a, strval($b["price"]));
	}
	//$l を上げることでメモリー負荷
	$l = 4;
	$i = 1;
	while($i<=$l) {
		$x=array();
		$x=pat($a,$i);
		foreach($x as $xy){
			$xy=substr($xy,0,-1);
			$arr = explode(',', $xy);
			if(array_sum($arr)==$limit){
				$min=0;
				$minarray=$arr;
				break;
			} elseif(array_sum($arr)<$limit) {
				$minx=$limit-array_sum($arr);
				if($min>$minx) {
					$min=$minx;
					$minarray=$arr;
				}
			}
		}
		if($min==0){
			break;
		}
		$i += 1;
	}

	$minarrays = array();
  $limit = array_sum($minarray);
  $array = array();
  $min=100;

  $l = 4;
  $i = 1;
  $minarrayscount = 0;

  while($i<=$l) {
    $x=array();
    $x=pat($a,$i);
    foreach($x as $xy) {
      $xy=substr($xy,0,-1);
      $arr = explode(',', $xy);

      if(array_sum($arr)==$limit) {
        $minarrays[$minarrayscount]=$arr;
        $minarrayscount += 1;
        break;
      }
    }

    $i += 1;

    if ($minarrayscount == 3) {
      break;
    }
  }

	$res = array();
	$total = array(0,0,0);
  for($i=0;$i<count($minarrays);$i++) {
    for($j=0;$j<count($minarrays[$i]);$j++) {
      $param["price"]=$minarrays[$i][$j];
    	$result=$userItem->select($param, "", "", "", true);
    	$total[$i] += $minarrays[$i][$j];
    	$res[$i][$j] = $result;
    }
  }
	// var_dump($res);


	return $this->view->render($response, 'sukkiri/sukkiri.twig',[
    'res' => $res,
		'res2' => urlencode($res[0][0][product_name]),
    'res3' => urlencode($res[0][1][product_name]),
    'res4' => urlencode($res[0][2][product_name]),
    'res5' => urlencode($res[0][3][product_name]),
    'res6' => urlencode($res[1][0][product_name]),
    'res7' => urlencode($res[1][1][product_name]),
    'res8' => urlencode($res[1][2][product_name]),
    'res9' => urlencode($res[1][3][product_name]),
    'res10' => urlencode($res[2][0][product_name]),
    'res11' => urlencode($res[2][1][product_name]),
    'res12' => urlencode($res[2][2][product_name]),
    'res13' => urlencode($res[2][3][product_name]),

		'total' => $total
	]);

});

// [{
// 	"name": asdvasdv,
// 	"price": 324,
// 	"img": "www.family.co.jp/lsdf"
// },{
// 	"name": asdflkj,
// 	"price": 54,
// 	"img": 'www.family.co.jp/adsl;fkjasd'
// }]
