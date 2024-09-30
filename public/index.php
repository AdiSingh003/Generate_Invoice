<?php
require __DIR__ .'/../api/database.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-Commerce System</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="mt-5 d-flex justify-content-center">
            <h4>Generate Invoice</h4>
        </div>
        <div class="container mt-5 d-flex justify-content-center">
            <div class="row">
                <!-- Dropdown for Firm -->
                <div class="col-auto">
                    <p>Select Firm</p>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle fixed-width-button" type="button" id="selectFirm" data-bs-toggle="dropdown" aria-expanded="false">
                            Type here to search
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="selectFirm">
                            <li class="px-3">
                                <input type="text" class="form-control searchInput" data-target="#dropdownListFirm" placeholder="Search firm...">
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <ul id="dropdownListFirm">
                                <?php
                                $firms = ["Omunim", "Vinay Computers", "Vito Techsoft"];
                                foreach ($firms as $firm) {
                                    echo '<li class="dropdown-item">' . $firm . '</li>';
                                }
                                ?>
                            </ul>
                        </ul>
                    </div>
                </div>
                <div class="col-auto">
                    <p>Select Vendor</p>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle fixed-width-button" type="button" id="selectVendor" data-bs-toggle="dropdown" aria-expanded="false">
                            Type here to search
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="selectVendor">
                            <li class="px-3">
                                <input type="text" class="form-control searchInput" data-target="#dropdownListVendor" placeholder="Search vendor...">
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <ul id="dropdownListVendor">
                                <?php
                                $vendors = [];
                                $sql = "SELECT * FROM vendor";
                                $result = $conn->query($sql);
                                if($result){
                                    if($result->num_rows > 0){
                                        while($row = $result->fetch_assoc()){
                                            $vendors[] = $row;
                                        }
                                    }
                                }
                                foreach ($vendors as $vendor) {
                                    echo '<li class="dropdown-item">' . $vendor['shopname'] . '</li>';
                                }
                                ?>
                            </ul>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container invoice-form my-5 d-flex justify-content-center d-none">
            <div class="row">

            <h5 class="">Product Details</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Price</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Total</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="invoiceBody">
                    <tr class="invoiceRow">
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle product-button text-truncate" type="button" id="selectProduct" data-bs-toggle="dropdown" aria-expanded="false">
                                    Type here to search
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="selectProduct">
                                    <li class="px-3">
                                        <input type="text" class="form-control searchInput" data-target="#dropdownListProduct" placeholder="Search...">
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <ul id="dropdownListProduct">
                                        <?php
                                        $products = ["Laptop", "Bag", "Tab", "Pen", "T-shirt", "TV","football","cricket bat"];
                                        foreach ($products as $product) {
                                            echo '<li class="dropdown-item">' . $product . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </ul>
                            </div>
                        </td>
                        <td><input type="text" class="form-control price"></td>
                        <td><input type="number" class="form-control quantity"></td>
                        <td><input type="text" class="form-control total_price" readonly></td>
                        <td>
                            <button type="button" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="btn btn-outline-secondary Add-Row-button" type="button" id="addProduct">
                                + Add Row
                            </button>
                        </td>
                        <td colspan="2">Total</td>
                        <td><input type="text" class="form-control final_total_price" readonly></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <p class="errMessage" id="errProduct"></p>

            <h5 class="mt-3">Vendor Details</h5>
                <div class="col">
                    <p>Shop Name:</p>
                    <input type="text" class="form-control shopName"  placeholder="Enter Shop Name" required>
                    <p class="errMessage" id="errshopName"></p>
                </div>

                <div class="col">
                    <p>Phone No:</p>
                    <input type="text" class="form-control phoneNumber" placeholder="Enter Phone Number" maxlength="10" required>
                    <p class="errMessage" id="errphoneNumber"></p>
                </div>
                <div class="mt-2">
                    <p>Enter Address:</p>
                    <textarea class="form-control" id="shopAddress" rows="3" placeholder="Enter Address" required></textarea>
                    <p class="errMessage" id="errshopAddress"></p>
                </div>

                <h5 class="mt-3">Comments and Special Instructions</h5>
                <div class="row">
                    <div class="col">
                        <p>Bank:</p>
                        <input type="text" class="form-control bankName"  placeholder="Enter Bank" required>
                        <p class="errMessage" id="errbankName"></p>
                    </div>

                    <div class="col">
                        <p>Branch:</p>
                        <input type="text" class="form-control branchName" placeholder="Enter Branch Name" required>
                        <p class="errMessage" id="errbranchName"></p>
                    </div>
                </div>
                <div class="mt-2 row">
                    <div class="col">
                        <p>Account No:</p>
                        <input type="text" class="form-control accountNumber" placeholder="Enter Account Number" maxlength="18" required>
                        <p class="errMessage" id="erraccountNumber"></p>
                    </div>
                    <div class="col">
                        <p>IDFC Code:</p>
                        <input type="text" class="form-control idfcCode" placeholder="Enter IDFC Code" required>
                        <p class="errMessage" id="erridfcCode"></p>
                    </div>
                </div>
                <div class="mt-2">
                    <p>Enter Additional Instructions:</p>
                    <textarea class="form-control" id="address" rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success mt-3" type="button" id="submit">Save Invoice</button>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    $(document).ready(function() {
        function bindSearchAndDropdownEvents() {
            $('.searchInput').on('keyup', function() {
                var searchValue = $(this).val().toLowerCase();
                var targetList = $(this).data('target');

                $(targetList + ' li').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });
            $(document).on('click', '#dropdownListProduct .dropdown-item', function() {
                var selectedText = $(this).text();
                $(this).closest('.dropdown').find('.dropdown-toggle').text(selectedText);
            });
        }
        $('#dropdownListFirm').on('click','.dropdown-item', function() {
            var selectedText = $(this).text();
            if (selectedText == "Omunim") {
                $('.invoice-form').removeClass('d-none'); 
            } else {
                alert('Form not available for this firm');
                $('.invoice-form').addClass('d-none'); 
            }
            $('#selectFirm').text(selectedText); 
        });

        $('#dropdownListVendor').on('click', '.dropdown-item', function() {
            var selectedVendor = $(this).text();
            $('#selectVendor').text(selectedVendor); 
            $.ajax({
                url: '/Generate_Invoice/api/getVendordetails.php',
                method: 'GET',
                data: { vendor_name: selectedVendor },
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        $('.shopName').val(response.shopname);
                        $('.phoneNumber').val(response.phno);
                        $('#shopAddress').val(response.shop_addr);
                    } else {
                        alert('No details found for this vendor.');
                    }
                },
            });
        });
        bindSearchAndDropdownEvents();

        function calculateFinalTotal() {
            let final_total = 0; 
            $('.total_price').each(function() {
                final_total += parseFloat($(this).val()) || 0; 
            });
            $('.final_total_price').val(final_total.toFixed(2)); 
        }

        $('#addProduct').on('click', function() {
            var newRow = `
                <tr class="invoiceRow">
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle product-button text-truncate" type="button" id="selectProduct" data-bs-toggle="dropdown" aria-expanded="false">
                                Type here to search
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="selectProduct">
                                <li class="px-3">
                                    <input type="text" class="form-control searchInput" data-target="#dropdownListProduct" placeholder="Search...">
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <ul id="dropdownListProduct">
                                    <?php
                                    $products = ["Laptop", "Bag", "Tab", "Pen", "T-shirt", "TV"];
                                    foreach ($products as $product) {
                                        echo '<li class="dropdown-item">' . $product . '</li>';
                                    }
                                    ?>
                                </ul>
                            </ul>
                        </div>
                    </td>
                    <td><input type="number" class="form-control quantity"></td>
                    <td><input type="text" class="form-control price"></td>
                    <td><input type="text" class="form-control total_price" readonly></td>
                    <td>
                        <button type="button" class="close" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </td>
                </tr>`;

            $('#invoiceBody').find('tr:last').before(newRow);

            bindSearchAndDropdownEvents();
        });

        $(document).on('input', '.quantity, .price', function() {
            const $row = $(this).closest('tr'); 
            let quantity = parseFloat($row.find('.quantity').val()) || 0; 
            let price = parseFloat($row.find('.price').val()) || 0; 
            let total = quantity * price; 
            $row.find('.total_price').val(total.toFixed(2)); 
            calculateFinalTotal(); 
        });

        $(document).on('click', '.close', function() {
            const $row = $(this).closest('tr');
            const rowTotal = parseFloat($row.find('.total_price').val()) || 0;
            if (confirm('Are you sure you want to delete this row?')) {
                $row.remove(); 
                calculateFinalTotal(); 
            }
        });

        $('.price').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '');  
            if ((this.value.match(/\./g) || []).length > 1) {
                this.value = this.value.slice(0, -1);  
            }
        });

        $('.quantity').on('input', function() {
            let value = $(this).val();
            if (value && (parseFloat(value) < 1 || !Number.isInteger(Number(value)))) {
                $(this).val(Math.floor(Math.max(1, value)));
            }
        });

        $('.shopName').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, ''); 
            
        });

        $('.phoneNumber').on('input', function() {
            this.value = this.value.replace(/\D/g, ''); 
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10); 
            }
        });

        $('.bankName,.branchName').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, ''); 
            
        });

        $('.accountNumber').on('input', function() {
            this.value = this.value.replace(/\D/g, ''); 
            if (this.value.length > 18) {
                this.value = this.value.slice(0, 18); 
            }
        });

        $('.idfcCode').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, ''); 
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11); 
            }
        });

        function validateForm() {
            let isValid = true;
            const productRows = document.querySelectorAll('.invoiceRow');
            let atleastOneProduct = false;
            productRows.forEach(function(row) {
                const product = row.querySelector('.product-button').textContent.trim();
                const price = row.querySelector('.price').value.trim();
                const quantity = row.querySelector('.quantity').value.trim();
                
                if (product !== "Type here to search" && price && quantity) {
                    atleastOneProduct = true;
                } else {
                    if (product === "Type here to search" || !price || !quantity) {
                        $('#errProduct').text('Please enter valid product details.');
                        isValid = false;
                    }
                }
            });
            const shopName = $('.shopName').val().trim();
            const phoneNumber = $('.phoneNumber').val();
            const shopAddress = $('#shopAddress').val().trim();
            const bankName = $('.bankName').val().trim();
            const branchName = $('.branchName').val().trim();
            const accountNumber = $('.accountNumber').val();
            const idfcCode = $('.idfcCode').val().trim();

            const phonePattern = /^\d{10}$/;
            const idfcPattern = /^[A-Z|a-z]{4}0[a-zA-Z0-9]{6}$/;
            const accountNumberPattern = /^\d{8,18}$/;

            if (!atleastOneProduct) {
                $('#errProduct').text('Please enter at least one product with valid details.');
                isValid = false;
            }
            else{
                $('#errProduct').text('');
            }
            if (!shopName) {
                $('#errshopName').text('Please enter a valid shop name');
                isValid = false;
            }
            else{
                $('#errshopName').text('');
            }
            if (!phonePattern.test(phoneNumber)) {
                $('#errphoneNumber').text('Please enter a valid 10-digit phone number.');
                isValid = false;
            }
            else{
                $('#errphoneNumber').text('');
            }
            if (!shopAddress) {
                $('#errshopAddress').text('Please enter a valid address');
                isValid = false;
            }
            else{
                $('#errshopAddress').text('');
            }
            if (!bankName) {
                $('#errbankName').text('Please enter a valid bank name.');
                isValid = false;
            }
            else{
                $('#errbankName').text('');
            }
            if (!branchName) {
                $('#errbranchName').text('Please enter a valid branch name.');
                isValid = false;
            }
            else{
                $('#errbranchName').text('');
            }
            if (!accountNumberPattern.test(accountNumber)) {
                $('#erraccountNumber').text('Please enter a valid account number (8-18 digits).');
                isValid = false;
            }
            else{
                $('#erraccountNumber').text('');
            }
            if (!idfcPattern.test(idfcCode)) {
                $('#erridfcCode').text('Please enter a valid IDFC Code in the format AAAA0123456.');
                isValid = false;
            }
            else{
                $('#erridfcCode').text('');
            }
            return isValid;
        };


        $('#submit').on('click', function(e) {
            e.preventDefault(); 
            let isValid = validateForm();
            if(!isValid){
                return
            }

            const invoiceData = {
                firm: $('#selectFirm').text().trim(),
                products: [],
                subtotal: $('.final_total_price').val(),
                vendor: {
                    shopname: $('.shopName').val(),
                    phno: $('.phoneNumber').val(),
                    shop_addr: $('#shopAddress').val()
                },
                bank: {
                    bank_name: $('.bankName').val(),
                    bank_branch: $('.branchName').val(),
                    acc_no: $('.accountNumber').val(),
                    idfc_code: $('.idfcCode').val(),
                    add_info: $('#address').val()
                },
            };

            $('#invoiceBody .invoiceRow').each(function() {
                const product = {
                    pr_name: $(this).find('.product-button').text(),
                    price: $(this).find('.price').val(),
                    qty: $(this).find('.quantity').val(),
                    total_price: $(this).find('.total_price').val()
                };
                invoiceData.products.push(product);
            });
            const $form = $('<form>', {
                action: '/Generate_Invoice/api/saveInvoiceFormDetails.php',
                method: 'POST'
            }).append($('<input>', {
                type: 'hidden',
                name: 'invoiceData',
                value: JSON.stringify(invoiceData)
            }));
            $('body').append($form);
            $form.submit();
        });
    });
</script>