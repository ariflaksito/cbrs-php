<?php require_once('vendor/autoload.php');
require_once('Cbrs.php');

$dsn = 'mysql:host=127.0.0.1;dbname=db_hotel';
$user = 'root';
$password = '';
$database = new Nette\Database\Connection($dsn, $user, $password);

$id = $_GET['id'];
$hotel = get_hotel_detail($id, $database);

$result = $database->query('SELECT hotel_id, hotel_desc, address FROM hotel');
$data = [];
foreach($result as $row){
    $data[$row->hotel_id] = pre_process($row->hotel_desc.' '.$row->address);
}

$cbrs = new Cbrs();
$cbrs->create_index($data);
$cbrs->idf();
$w = $cbrs->weight();  
$r = $cbrs->similarity($id);
$n = 8;

function pre_process($str){
    $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
    $stemmer = $stemmerFactory->createStemmer();

    $stopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
    $stopword = $stopWordRemoverFactory->createStopWordRemover();

    $str = strtolower($str);
    $str = $stemmer->stem($str);
    $str = $stopword->remove($str);

    return $str;
}

function get_hotel_detail($id, $db){
    $rs = $db->fetch('SELECT * FROM hotel Where hotel_id = '.$id);
    return $rs;
}

?>
<!doctype html>
<html lang="en">
    <head>
        <title>Content-based Filtering</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    </head>

    <body>
        <div class="container theme-showcase">
            <div class="jumbotron">
                <h1>Daftar Hotel di Surabaya</h1>
                <p>Contoh implementasi Sistem rekomendasi berbasis kontent menggunakan metode TF-IDF dan Cosine Similarity</p>
            </div>
            
            <div class="row">
                <div class="col-md-2">
                    <img src="https://via.placeholder.com/150" />
                </div>
                <div class="col-md-10">
                    <h2><span class="label label-primary"><?php echo $hotel->hotel_name?></span></h2>
                    <p><strong>Address:</strong> <?php echo $hotel->address?></p>
                    <p><strong>Description:</strong> <?php echo $hotel->hotel_desc?></p>
                    <p><strong>Price/Night:</strong> Rp <?php echo number_format($hotel->price_per_night)?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3>Rekomendasi Hotel yang sesuai</h3>
                    <ol>
                        <?php $i=0;?>
                        <?php foreach($r as $k => $row):?>
                            <?php if($i==$n) break;?>
                            <?php if($row==1) continue;?>
                            <?php $h = get_hotel_detail($k, $database);?>
                            <li><a href="detail.php?id=<?php echo $h->hotel_id ?>">
                                <?php echo $h->hotel_name ?></a> (<?php echo $row?>)
                            </li>
                            <?php $i++ ?>
                        <?php endforeach ?>    
                    </ol>
                </div>
            </div>
        </div>
    </body>
</html>
