<?php
require('database.php'); 
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceData = json_decode($_POST['invoiceData'], true);

    $sql = "SELECT MAX(purchase_order_no) AS max_order_no FROM product";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $latestPurchaseOrderNo = $row['max_order_no'] ? $row['max_order_no'] + 1 : 1;
    }
    
    foreach ($invoiceData['products'] as $product) {
        $productName = $product['pr_name'];
        $price = $product['price'];
        $qty = $product['qty'];
        $totalPrice = $product['total_price'];
        $stmt = $conn->prepare("INSERT INTO product (pr_name, price, qty, total_price, purchase_order_no) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiii", $productName, $price, $qty, $totalPrice, $latestPurchaseOrderNo);
        $stmt->execute();
    }

    $bankName = $invoiceData['bank']['bank_name'];
    $bankBranch = $invoiceData['bank']['bank_branch'];
    $accNo = $invoiceData['bank']['acc_no'];
    $idfcCode = $invoiceData['bank']['idfc_code'];
    $addInfo = $invoiceData['bank']['add_info'] ? $invoiceData['bank']['add_info'] : '';

    $stmt = $conn->prepare("INSERT INTO bank (bank_name, bank_branch, acc_no, idfc_code, add_info) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $bankName, $bankBranch, $accNo, $idfcCode, $addInfo);
    $stmt->execute();

    $shopName = $invoiceData['vendor']['shopname'];
    $phoneNumber = $invoiceData['vendor']['phno'];
    $shopAddress = $invoiceData['vendor']['shop_addr'];

    

    $tax = 0.18*$invoiceData['subtotal'];
    $final_total = $invoiceData['subtotal'] + $tax;

    function generateRandomGSTNumber() {
        $stateCode = str_pad(rand(1, 37), 2, '0', STR_PAD_LEFT);
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $panNumber = substr(str_shuffle($letters), 0, 5) . rand(1000, 9999) . $letters[rand(0, 25)];
        $entityCode = rand(1, 9);
        $checksum = rand(0, 9);
        $gstNumber = $stateCode . $panNumber . $entityCode . $checksum . 'Z';
        return $gstNumber;
    }
    $gstNumber = generateRandomGSTNumber();
    
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Company');
    $pdf->SetTitle('Invoice');
    $pdf->SetSubject('Invoice PDF');

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $html = '';
    $html .= '
    <table style="width: 100%;" cellspacing="0" cellpadding="5">
        <tr>
            <!-- First Column: Firm Details -->
            <td style="vertical-align: top; width: 50%;">
                <table cellpadding="5" cellspacing="0" border="0">
                    <tr>
                            <td style="font-weight: bold;font-size:14px;">'.$invoiceData['firm'].' SOFTWARE PVT LTD </td>
                    </tr>
                    <tr>
                        <td>5050, 5th Floor, Marvel Fuego, Above Maruti Nexa Showroom, Opp to Season Mall (We Work Building)HADAPSAR, Pune 411028 Maharashtra India</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">GSTIN is ' . $gstNumber . '</td>
                    </tr>
                </table>
            </td>

            <!-- Second Column: Purchase Order -->
            <td style="width: 50%;" align="right">
                <table border="0" cellpadding="5" cellspacing="0" style="font-size: 14px;">
                    <tr>
                        <td align="center" style="font-weight: bold;">PURCHASE ORDER</td>
                    </tr>
                </table>
                <br>
                <table border="1" cellpadding="5" cellspacing="0">
                    <tr>
                        <td>Invoice Date</td>
                        <td>' . date('Y-m-d') . '</td>
                    </tr>
                    <tr>
                        <td>PO #</td>
                        <td>'.$latestPurchaseOrderNo.'</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br>';

    $html .= '
    <table cellpadding="5" cellspacing="0" style="width: 100%;">
        <tr>
            <td style:padding:10px;><h3 style="background-color:purple;color:white;">VENDOR</h3></td>
            <td style:padding:10px;><h3 style="background-color:purple;color:white;">SHIP TO</h3></td>
        </tr>
        <tr>
            <td>
                ' . $shopName . '<br><br>
                ' . $shopAddress . '<br><br>
                ' . $phoneNumber . '
            </td>
            <td align="right">
                5050, 5th Floor, Marvel Fuego, Above Maruti Nexa Showroom,Opp to Season Mall (We Work Building) HADAPSAR, Pune 411028 Maharashtra India
            </td>
        </tr>
    </table>
    <br><br>';

    $html .= '
    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%;">
        <thead>
            <tr style="background-color:purple;color:white">
                <th>REQUISITIONER</th>
                <th>SHIP VIA</th>
                <th>FOB</th>
                <th>SHIPPING TERMS</th>
            </tr>
        </thead>
        <tbody>';
        $html .= '
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>';
    $html .= '
        </tbody>
    </table>
    <br><br>';

    $html .= '
    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%;">
        <thead>
            <tr style="background-color:purple;color:white">
                <th>ITEM #</th>
                <th>PRODUCT NAME</th>
                <th>PRICE</th>
                <th>QUANTITY</th>
                <th>TOTAL PRICE</th>
            </tr>
        </thead>
        <tbody>';
    $itemCount = 1;
    foreach ($invoiceData['products'] as $product) {
        $html .= '
        <tr>
            <td>' . $itemCount++ . '</td>
            <td>' . $product['pr_name'] . '</td>
            <td>' . $product['price'] . '</td>
            <td>' . $product['qty'] . '</td>
            <td>' . $product['total_price'] . '</td>
        </tr>';
    }
    $html .= '
        </tbody>
    </table>
    <br><br>';
    $html .= '
    <table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
        <tr>
            <!-- Comments or Special Instructions Section -->
            <td style="width: 60%; vertical-align:top;">
                <table cellpadding="5" cellspacing="0" style="border:1px solid;">
                    <tr style="background-color:grey;color:black;font-size: 12px;">
                        <td><h3>Comments or Special Instructions</h3></td>
                    </tr>
                    <tr>
                        <td>Bank Name: ' . htmlentities($bankName) . '</td>
                    </tr>
                    <tr>
                        <td>Bank Branch: ' . htmlentities($bankBranch) . '</td>
                    </tr>
                    <tr>
                        <td>Account Number: ' . htmlentities($accNo) . '</td>
                    </tr>
                    <tr>
                        <td>IDFC Code: ' . htmlentities($idfcCode) . '</td>
                    </tr>
                    <tr>
                        <td>Additional Instructions: ' . htmlentities($addInfo) . '</td>
                    </tr>
                </table>
            </td>

            <!-- Totals Section -->
            <td style="width: 40%; text-align:right;">
                <table cellpadding="5" cellspacing="0" border="0" style="width: 100%;">
                    <tr>
                        <th>SUBTOTAL</th>
                        <td>' . number_format($invoiceData['subtotal'], 2) . '</td>
                    </tr>
                    <tr>
                        <th>TAX</th>
                        <td>' . number_format($tax, 2) . '</td>
                    </tr>
                    <tr>
                        <th>SHIPPING</th>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>OTHER</th>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-bottom: 1px solid black;"></td>
                    </tr>
                    <tr>
                        <th><strong>TOTAL</strong></th>
                        <td><strong>' . number_format($final_total, 2) . '</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br><br>';

    $pdf->writeHTML($html, true, false, true, false, '');
    ob_clean(); 
    $pdf->Output('invoice.pdf', 'D');
}
?>
