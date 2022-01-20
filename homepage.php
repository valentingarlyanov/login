<?php
session_start();
$product_ids = array();
if (filter_input(INPUT_POST, 'add_to_cart')) {
    if (isset($_SESSION['shopping_cart'])) {
        //counting how many products are in the cart
        $count = count($_SESSION['shopping_cart']);

        //sequantial array for matching array keys to the ids
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');

        if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)) {
            $_SESSION['shopping_cart'][$count] = array(
                'id' => filter_input(INPUT_GET, 'id'),
                'title' => filter_input(INPUT_POST, 'title'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
            );
        } else { //product exist, increase quantity
            for ($i = 0; $i < count($product_ids); $i++) {
                if ($product_ids[$i] == filter_input(INPUT_GET, 'id')) {
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }
    } else {
        //create array using submitted form data, start from key 0 and fill with values
        $_SESSION['shopping_cart'][0] = array(
            'id' => filter_input(INPUT_GET, 'id'),
            'title' => filter_input(INPUT_POST, 'title'),
            'price' => filter_input(INPUT_POST, 'price'),
            'quantity' => filter_input(INPUT_POST, 'quantity')
        );
    }
}

if(filter_input(INPUT_GET, 'action')=='delete'){
    foreach($_SESSION['shopping_cart'] as $key => $products){
        if($products['id'] == filter_input(INPUT_GET, 'id')){
            unset($_SESSION['shopping_cart'][$key]);
        }
    }
    $_SESSION['shopping_cart'] == array_values([$_SESSION['shopping_cart']]);
}

pre_r($_SESSION);
function pre_r($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Jordan kicks</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="cart.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oswald&display=swap');

        * {
            font-family: 'Oswald', sans-serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        $connect = mysqli_connect('localhost', 'root', '', 'jordankicks');
        $query = "SELECT * FROM products ORDER BY id ASC";
        $result = mysqli_query($connect, $query);
        if ($result) :
            if (mysqli_num_rows($result) > 0) :
                while ($products = mysqli_fetch_array($result)) :
        ?>
                    <div class="col-sm-4 col-md-3">
                        <form method="post" action="homepage.php?action=add&id=<?php echo $products['id']; ?>"></form>
                        <div class="products">
                            <img src="<?php echo $products['image']; ?>" class="img-responsive" /><br />
                            <h4 class="text-info"><?php echo $products['title']; ?></h4>
                            <h4>$ <?php echo $products['price']; ?></h4>
                            <input type="text" name="quantity" class="form-control" value="1" />
                            <input type="hidden" name="title" value="<?php echo $products['title']; ?>" />
                            <input type="hidden" name="price" value="<?php echo $products['price']; ?>" />
                            <input type="submit" name="add_to_cart" style="margin-top: 5px" class="btn btn-info" value="Add to cart" />
                        </div>
                    </div>
        <?php
                endwhile;
            endif;
        endif;
        ?>
        <div style="clear:both"></div>
        <br />
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th colspan="5">
                        <h3>Order Details</h3>
                    </th>
                </tr>
                <tr>
                    <th width="40%">Product name</th>
                    <th width="10%">Quantity</th>
                    <th width="20%">Price</th>
                    <th width="15%">Total</th>
                    <th width="5%">Action</th>
                </tr>
                <?php
                if (!empty($_SESSION['shopping_cart'])) :
                    $total = 0;

                    foreach ($_SESSION['shopping_cart'] as $key => $products) :
                ?>
                        </tr>
                        <td><?php echo $products['title']; ?></td>
                        <td><?php echo $products['quantity']; ?></td>
                        <td>$ <?php echo $products['price']; ?></td>
                        <td>$ <?php echo number_format($products['quantity'] * $products['price'], 2); ?></td>
                        <td>
                            <a href="homepage.php?action=delete&id<?php echo $products['id']; ?>">
                                <div class="btn-danger">Remove</div>
                            </a>
                        </td>
                        </tr>
                    <?php
                        $total = $total + ($products['quantity'] * $products['price']);
                    endforeach;
                    ?>
                    <tr>
                        <td colspan="3" align="right">Total</td>
                        <td align="right">$ <?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <?php
                            if (isset($_SESSION['shopping_cart'])) :
                                if (count($_SESSION['shopping_cart']) > 0) :
                            ?>
                                    <a href="#" class="button">Checkout</a>
                            <?php endif;
                            endif; ?>
                        </td>
                    </tr>
                <?php
                endif;
                ?>
            </table>
        </div>
    </div>
</body>

</html>