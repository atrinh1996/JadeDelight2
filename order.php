<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
    
    <title>Order Receipt PHP</title>
</head>
<body>
    <header>
        <div class="container">
            <div id="res-name">
                <h1>Jade Delight</h1>
            </div>
        </div>
    </header>

    <div class="container">
        <div id="order-head">
            <h2>Your Ordery Summary</h2>
        </div>
    </div>

    <div class="container">
        <div id="receipt">
            <!-- receipt info filled here -->

            <!-- Get string list of food items to display -->
            <script language="javascript">
                var foodList = localStorage.getItem("foodKey").split(',');
            </script>

            <!-- Get form data -->
            <?php
                $fname          = $_GET['fname'];
                $lname          = $_GET['lname'];
                $email          = $_GET['email'];
                $phone          = $_GET['phone'];
                $pORd           = $_GET['p_or_d'];
                $timeDisplay    = $_GET['time'];
                $subtotal       = $_GET['subtotal'];
                $tax            = $_GET['tax'];
                $total          = $_GET['total'];

                // quantity of food ordered, indexed same as foodList.
                $quan           = $_GET["quan"];

                // cost of each order, indexed same as foodList.
                $cost           = $_GET["cost"];

                $street = "";
                $city = "";

                // set time till ready based on pickup or delivery option
                if ($pORd == "delivery") {
                    $street = $_GET['street'];
                    $city = $_GET['city'];
                }
            ?>

            <!-- Display Items ordered by writing the html here -->
            <?php
                $quanStr = "<div class='cookie'><ul id='order'>";
                $costStr = "<ul id='cost'>";
                for ($i = 0; $i < count($quan); $i++) {
                    if ($quan[$i] != 0) {
                        $quanStr .= "<li>".$quan[$i]." x <script language='javascript'> document.write(foodList[".$i."]) </script></li>";

                        $costStr .= "<li>$".$cost[$i]."</li>";
                    }
                }
                $quanStr .= "</ul>";
                $costStr .= "</ul></div>";

                echo $quanStr . $costStr;
            ?>

            <!-- Display totals at bottom of receipt -->
            <div class='cookie'>
                <ul id='display'>
                    <li>Subtotal: $ <?php echo $subtotal; ?></li>
                    <li>Mass tax 6.25%: $ <?php echo $tax; ?></li>
                    <li>Total: $ <?php echo $total; ?></li>
                </ul>
            </div> <!-- End of Cookie -->

            <!-- Display message when food will be ready -->
            <p>
                Hi<?php if ($fname != "") echo ", ".$fname; ?>! Your food will be ready at <?php echo $timeDisplay; ?>. An email confirmation has been sent.
            </p>

            <!-- Send the email -->
            <?php
                $msg = "Thank you for ordering from Jade Delight. Your total is $$total. Your ";

                if ($pORd == "delivery")
                    $msg .= "delivery order to $street in $city ";
                else
                    $msg .= "pickup order ";
                
                $msg .= "will be ready soon at $timeDisplay!";

                // send email confirmation
                // echo $msg;
                mail($email, "Order Confirmation", $msg);
            ?>

        </div> <!-- End of receipt -->
    </div> <!-- End of container -->


    <footer id="main-footer">
        <div class="container">
        <div class="block">
            <p>Copyright &copy; 2021 Jade Delight</p>
            <ul>
                <li><a href="#" target="_blank"><img src="images/pngkey.com-clemson-paw-png-703430.png" alt="Instagram"></a></li>

                <li><a href="#" target="_blank"><img src="images/pngkey.com-facebook-circle-png-503771.png" alt="Facebook"></a></li>

                <li><a href="#" target="_blank"><img src="images/pngkey.com-twitter-logo-png-transparent-111779.png" alt="Twitter"></a></li>
            </ul>
        </div>
        </div>
    </footer>

</body>
</html>