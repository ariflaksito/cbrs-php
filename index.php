<?php require_once('vendor/autoload.php');
require_once('Cbrs.php');

$dsn = 'mysql:host=127.0.0.1;dbname=db_hotel';
$user = 'root';
$password = '';
$database = new Nette\Database\Connection($dsn, $user, $password);

$result = $database->query('SELECT hotel_id, hotel_name, address, price_per_night FROM hotel order by rand() limit 0,10');

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
            <div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hotel Name</th>
                            <th>Hotel Address</th>
                            <th>Price/Night</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1?>
                        <?php foreach($result as $row):?>
                        <tr>
                            <td><?php echo $no++?></td>
                            <td><a href="detail.php?id=<?php echo $row->hotel_id ?>">
                                <?php echo $row->hotel_name ?></a>
                            </td>
                            <td><?php echo $row->address ?></td>
                            <td><?php echo 'Rp '.number_format($row->price_per_night) ?></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
