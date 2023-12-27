<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            text-decoration: none;
            color: #333;
        }
        a:hover {
            color: #0066cc;
        }
    </style>
    <title>Active Stocks by Truong Dang</title>
</head>
<body>
    <?php
        require 'vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017/");
        $db = $client->{"truong_data"};
        $collection = $db->{"most_active_stocks"};
        $active_stocks = $collection->find([]);

        function generateHTMLTable($stocks){ // Generate HTML Table
            echo '<table border="1">';
                echo '<tr><th><a href="?sort=_id">Index</a></th><th><a href="?sort=Symbol">Symbol</a></th><th><a href="?sort=Name">Name</a></th><th><a href="?sort=Price">Price (Intraday)</a></th><th><a href="?sort=Change">Change</a></th><th><a href="?sort=Volume">Volume</a></th></tr>';
                foreach ($stocks as $active_stock) {
                    echo '<tr>';
                        echo '<td>' . $active_stock['_id'] . '</td>';
                        echo '<td>' . $active_stock['Symbol'] . '</td>';
                        echo '<td>' . $active_stock['Name'] . '</td>';
                        echo '<td>' . $active_stock['Price'] . '</td>';
                        echo '<td>';
                        if ($active_stock['Change'] > 0) {
                            echo '+';
                        }
                        echo $active_stock['Change'] . '</td>';
                        echo '<td>' . $active_stock['Volume'] . 'M</td>';
                    echo '</tr>';
                }
            echo '</table>';
        }

        if (isset($_GET['sort'])) {
            $selected_column = $_GET['sort'];
            $sorted = $collection->find([], ['sort' => [$selected_column => 1]]);
            generateHTMLTable($sorted);
        } else {
            generateHTMLTable($active_stocks);
        }
    ?>
</body>
</html>