<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <!-- <script language="javascript" src="main.js"></script> -->

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="style.css">


    <title>Jade Delight PHP</title>
</head>
<body>

    <!-- Establish a Connection and get information from database -->
    <?php
        // establish connection info
        $server = "sql105.epizy.com";
        $userid = "epiz_28359345";
        $pw = "LWEuRFFd869DZaT";
        $db = "epiz_28359345_JadeMenu";

        // create connection
        $conn = new mysqli($server, $userid, $pw);

        // check connection
        if ($conn->connect_error) {
            die("Connection to Menu Failed:".$conn->connect_error);
        }
        // echo "Connected to Menu Successfully";

        // select the database
        $conn->select_db($db);

        // run query
        $sql = "SELECT name, cost FROM menu";
        $result = $conn->query($sql);

        // process results and put it into php array
        $menuItems = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $menuItems[$row["name"]] = $row["cost"];
            }
        } else {
            echo "No Results :(";
        }

        // close the connection
        $conn->close();
    ?>

    <!-- Functions relating to displaying the menu (in PHP) -->
    <?php
        // create the select tag for each menu item
        function makeSelect($name, $minRange, $maxRange) {
            $t = "";
            $t .= "<select name='" . $name . "' size='1'>";
            for ($j = $minRange; $j <= $maxRange; $j++)
                $t .= "<option>" . $j . "</option>";
            $t .= "</select>"; 
            return $t;
        }

        // Display items
        function createMenu() {
            global $menuItems;

            $s = "";
            // Check the menuItems array
            foreach($menuItems as $item=>$price) {
                $s .= "<tr><td>";
                $s .= makeSelect("quan[]", 0, 10);
                $s .= "</td><td>" . $item . "</td>";
                $s .= "<td> $ " . $price . "</td>";
                $s .= "<td>$<input type='text' name='cost[]'/></td></tr>";
            }

            return $s;
        }
    ?>

    <!-- Create a JS Object for main.js to auto update prices during ordering -->
    <script language="javascript">

        // create an array of the keys in the order they are in in theMenu
        // to index by the key (type: string).
        function getFoodList(theMenu) {
            food = Object.keys(theMenu);

            // Change the type of the "prices" from string to numbers
            for (var j = 0; j < food.length; j++){
                // console.log(typeof(theMenu[food[j]]));
                theMenu[food[j]] = parseFloat(theMenu[food[j]]);
                // console.log(typeof(theMenu[food[j]]));
            }

            return food;
        }

        // Create a json object for the menu where
        // values are the cost of each food
        var theMenu = <?php echo json_encode($menuItems); ?>;
        var foodKey = getFoodList(theMenu);

        // set list of foods (type: string) to local storage for receipt
        localStorage.setItem("foodKey", foodKey);
    </script>

    <header>
    <div class="container">
        <div id="res-name">
            <h1>Jade Delight</h1>
        </div> <!-- End of res-name -->
    </div> <!-- End of container -->
    </header>

    <form name="Order-Form" method="GET" action="http://jumbo-jade.infinityfreeapp.com/order.php" target="_blank" onsubmit="return validate()">

        <div class="container">
            <div id="order-head">
                <h2>Place Your Order</h2>
            </div> <!-- End of order-head -->

            <div class="block">
                <div id="elem">
                    <p id="error-order">You must order at least one (1) item.</p>
                </div> <!-- End of elem -->
            </div>  <!-- End of block -->

            <div class="block">
            <div class="elem">
                <p>First Name: <input type="text"  name='fname' /></p>
            </div> <!-- End of elem -->
            <div class="elem">
                <p>Last Name*:  <input type="text"  name='lname' /></p>
            </div> <!-- End of elem -->
            <div class="elem">
                <p>Email*:  <input type="text"  name='email' /></p>
            </div> <!-- End of elem -->
            <div class="optional-display">
                <p>Street: <input type="text"  name='street' /></p>
            </div> <!-- End of optional-display -->
            <div class="optional-display">
                <p>City: <input type="text"  name='city' /></p>
            </div> <!-- End of optional-display -->
            <div class="elem">
                <p>Phone*: <input type="text"  name='phone' /></p>
            </div> <!-- End of elem -->
            <div class="elem">
                <p>
                    <input type="radio"  name="p_or_d" value = "pickup" checked="checked"/>Pickup
                    <input type="radio"  name='p_or_d' value = 'delivery'/>Delivery
                </p>
            </div> <!-- End of elem -->
            </div> <!-- End of block -->
        </div> <!-- End of .container -->


        <div class="container">
            <div class="block">
                <table border="0" cellpadding="3">
                    <tr>
                        <th>Select Item</th>
                        <th>Item Name</th>
                        <th>Cost Each</th>
                        <th>Total Cost</th>
                    </tr>

                    <!-- Display available menu/food items -->
                    <?php echo createMenu(); ?>

                </table>
            </div> <!-- End of block -->

            <div class="block">
            <div class="elem">
                <p>Subtotal: $<input type="text" name='subtotal' id="subtotal"/></p>
            </div> <!-- End of elem -->
            <div class="elem">
                <p>Mass tax 6.25%: $ <input type="text" name='tax' id="tax"/></p>
            </div> <!-- End of elem -->
            <div class="elem">
                <p>Total: $ <input type="text"  name='total' id="total"/></p>
            </div> <!-- End of elem -->
            </div> <!-- End of block -->

            <input type="text" name="time" id="time" style="display:none;">

            <div class="block">
                <input type="submit" value="Submit Order"/> <!--my change-->
            </div> <!-- End of block -->
        </div> <!-- End of .container -->
    </form> <!-- End of Order-Form -->

    <script language="javascript" src="main.js"></script>


    <!-- Page Footer: Copyright and Social media links -->
    <footer id="main-footer">

        <div class="container">
        <div class="block">
            <p>Copyright &copy; 2021 Jade Delight</p>

            <ul>
                <li><a href="#" target="_blank"><img src="images/pngkey.com-clemson-paw-png-703430.png" alt="Instagram"></a></li>

                <li><a href="#" target="_blank"><img src="images/pngkey.com-facebook-circle-png-503771.png" alt="Facebook"></a></li>

                <li><a href="#" target="_blank"><img src="images/pngkey.com-twitter-logo-png-transparent-111779.png" alt="Twitter"></a></li>
            </ul>

        </div> <!-- End of Block -->
        </div> <!-- end of ./container -->
    </footer> <!-- #main-footer -->

</body>
</html>