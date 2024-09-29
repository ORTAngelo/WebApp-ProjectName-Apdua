<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Order Form</title>
</head>

<body>
    <?php

    function displayOrderSummary()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            echo "<h2>Order Summary</h2>";

            $coffee_prices = [
                "espresso" => 150,
                "latte" => 180,
                "cappuccino" => 200,
                "americano" => 220,
                "mocha" => 190,
            ];

            $size_prices = [
                "small" => 30.00,
                "medium" => 50.0,
                "large" => 80.0,
            ];

            $extras_prices = [
                "sugar" => 3.25,
                "cream" => 5.85,
            ];

            $name = $_POST["name"];
            $coffeeType = $_POST["coffee"];
            $size = $_POST["size"];
            $instructions = $_POST["instructions"];

            if(isset($_POST["extras"])){
                $extra = $_POST["extras"];
            }else{
                $extras = [];
            }

            $total_price = $coffee_prices[$coffee_type] +$size_prices[$size];
            $total_price = calculateTotalPrice($coffee_prices, $size_prices, $extras_prices, $coffee_type, $size, $extras);

            $receiptContent = generateReceiptContent($name, $coffeeType, $coffee_prices, $size, $size_prices, $extras, $extras_prices, $total_price, $instructions);
            saveReceiptToFile($receiptContent);

            foreach ($extras as $extra) {
                $total_price += $extras_prices[$extra];
            }

            echo $total_price;
            echo "<br/>";
            displayOrderDetails($name, $coffee_prices, $size_prices, $extras_prices, $coffee_type, $size, $extras, $total_price);
            displayJokeAndPassword($coffee_type, $_POST["name"], $total_price);
            echo "</div>";
        }
    }

    function calculateTotalPrice($coffee_prices, $size_prices, $extras_prices, $coffee_type, $size, $extras){
        $total_price = $coffee_prices[$coffee_type] + $size_prices[$size];

        return $total_price;
    }

    function displayOrderDetails($name, $coffee_prices, $size_prices, $extras_prices, $coffee_type, $size, $extras, $total_price){
        echo "<table>";

        echo "<tr><td>Name</td><td>" . htmlspecialchars($name) . "</td></tr>";

        echo "<tr><td>Coffee Type</td><td>" . htmlspecialchars($coffee_type) . " (₱" . number_format($coffee_prices[$coffee_type], 2) . ")</td></tr>";

        echo "<tr><td>Size</td><td>" . htmlspecialchars($size) . " (₱" . number_format($size_prices[$size], 2) . ")</td></tr>";

        if (!empty($extras)) {
            echo "<tr><td>Extras:</td><td>" . implode(", ", $extras) . " (₱" . number_format(array_sum(array_intersect_key($extras_prices, array_flip($extras))), 2) . ")</td></tr>";
        }

        echo "<tr><td>Total Price</td><td>₱" . number_format($total_price, 2) . "</td></tr>";

        echo "<tr><td>Special Instructions</td><td>" . htmlspecialchars($_POST["instructions"]) . "</td></tr>";

        echo "</table>";
    }

    function displayJokeAndPassword($coffee_type, $name, $total_price)
    {
        if ($coffee_type !== "espresso") {
            echo "Hey, " . htmlspecialchars($name) . "!";
            echo "<p>Here's a joke for you: Why did the coffee file a police report? It got mugged!</p>";
        }
        if ($total_price > 250 && $total_price < 350) {
            echo "<p>Password for the CR: coffee123</p>";
        } elseif ($total_price >= 350) {
            echo "<p>Password for Wi-Fi: mocha456</p>";
        }
    }
    function generateReceiptContent($name, $coffeeType, $coffee_prices, $size, $size_prices, $extras, $extras_prices, $total_price, $instructions){

        $receiptContent = "Order Summary\n";

        $receiptContent .= "-----------------\n";

        $receiptContent .= "Name: " . $name . "\n";

        $receiptContent .= "Coffee Type: " . $coffeeType . " (₱" . number_format($coffee_prices[$coffeeType], 2) . ")\n";

        $receiptContent .= "Size: " . $size . " (₱" . number_format($size_prices[$size], 2) . ")\n";

        if (!empty($extras)) {
            $receiptContent .= "Extras: " . implode(", ", $extras) . " (₱" . number_format(array_sum(array_intersect_key($extras_prices, array_flip($extras))), 2) . ")\n";
        }

        $receiptContent .= "Total Price: ₱" . number_format($total_price, 2) . "\n";

        $receiptContent .= "Special Instructions: " . $instructions . "\n";

        $receiptContent .= "\n";
        $receiptContent .= "Thank you for your order!";

        return $receiptContent;
    }

    function saveReceiptToFile($receiptContent){
        $file = fopen("Coffee Shop Order Summary.txt", "w") or die("Unable to open file!");

        fwrite($file, $receiptContent);
        fclose($file);

        echo "Receipt created successfully as Coffee Shop Order Summary.txt!";
    }

    displayOrderSummary();

    ?>

</body>

</html>