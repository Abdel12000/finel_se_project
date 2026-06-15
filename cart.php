<?php
session_start();
include 'common/config.php';

$user_email = $_SESSION['user_email'] ?? '';
if (!$user_email) {
    header("Location: login.php");
    exit;
}

$email_safe = mysqli_real_escape_string($connect, $user_email);
$user_query = "SELECT user_id FROM users WHERE email = '$email_safe' LIMIT 1";
$user_result = mysqli_query($connect, $user_query);
if (!$user_result || mysqli_num_rows($user_result) === 0) {
    echo "User not found.";
    exit;
}
$user_data = mysqli_fetch_assoc($user_result);
$user_id = (int) $user_data['user_id'];

if (isset($_GET['delete_cart_id'])) {
    $delete_cart_id = (int) $_GET['delete_cart_id'];
    $delete_sql = "DELETE FROM cart WHERE cart_id = $delete_cart_id AND user_id = $user_id";
    mysqli_query($connect, $delete_sql);
    header("Location: cart.php");
    exit;
}

$sql = "
SELECT 
    c.cart_id,
    p.plant_id,
    p.name AS plant_name,
    p.description,
    p.image_url,
    u.username,
    cat.name AS category_name,
    p.price,
    c.quantity,
    p.stock_qty
FROM cart c
JOIN plants p ON c.plant_id = p.plant_id
JOIN users u ON c.user_id = u.user_id
JOIN categories cat ON p.category_id = cat.category_id
WHERE c.user_id = $user_id
ORDER BY c.cart_id DESC
";

$result = mysqli_query($connect, $sql);

$cart_total = 0.0;
$cart_total_query = "
SELECT SUM(p.price * c.quantity) AS total_cart_price
FROM cart c
JOIN plants p ON c.plant_id = p.plant_id
WHERE c.user_id = $user_id
";
$cart_total_result = mysqli_query($connect, $cart_total_query);
if ($cart_total_result && mysqli_num_rows($cart_total_result) > 0) {
    $cart_total_data = mysqli_fetch_assoc($cart_total_result);
    $cart_total = $cart_total_data['total_cart_price'] ?? 0;
}

$user_sql = "SELECT user_id, username, phone, address FROM users WHERE email = '$email_safe' LIMIT 1";
$user_result = mysqli_query($connect, $user_sql);
if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_data = mysqli_fetch_assoc($user_result);
    $username = $user_data['username'];
    $user_phone = $user_data['phone'] ?? '';
    $user_address = $user_data['address'] ?? '';
} else {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Your Cart</title>
    <link rel="stylesheet" href="user/css/bootstrap.min.css" />
    <link rel="stylesheet" href="user/icomoon/icomoon.css" />
    <link rel="stylesheet" href="user/css/vendor.css" />
    <link rel="stylesheet" href="user/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Arapey&display=swap" rel="stylesheet" />
</head>

<body>
    <?php include("user/header.php"); ?>
    <div class="container mt-5">
        <h2 class="mb-4">Your Cart Items</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <form id="cartForm" method="POST" action="update_cart_qty.php">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Plant Name</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Added By</th>
                            <th>Price ($)</th>
                            <th>Quantity (max stock)</th>
                            <th>Total Price ($)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $max_qty = 0;
                        while ($row = mysqli_fetch_assoc($result)):
                            $total_price = $row['price'] * $row['quantity'];
                            $max_qty = (int) $row['stock_qty'];
                            ?>
                            <tr>
                                <td style="width:120px;">
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                                        alt="<?php echo htmlspecialchars($row['plant_name']); ?>"
                                        style="width:100px; height:70px; object-fit:cover;">
                                </td>
                                <td><?php echo htmlspecialchars($row['plant_name']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo number_format($row['price'], 2); ?></td>
                                <td style="width:100px;">
                                    <input type="number" name="quantities[<?php echo $row['cart_id']; ?>]" min="1"
                                        max="<?php echo $max_qty; ?>" value="<?php echo $row['quantity']; ?>"
                                        class="form-control quantity-input" data-price="<?php echo $row['price']; ?>" required>
                                    <small class="text-muted">Max: <?php echo $max_qty; ?></small>
                                </td>
                                <td class="item-total-price"><?php echo number_format($total_price, 2); ?></td>
                                <td style="width:120px;">
                                    <a href="cart.php?delete_cart_id=<?php echo $row['cart_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to remove this item from your cart?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7" class="text-end">Cart Total:</th>
                            <th id="cartTotal"><?php echo number_format($cart_total, 2); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </form>
            <div class="text-end">
                <?php
                if ($max_qty != 0) {
                    echo "<button class='btn btn-success btn-lg' data-bs-toggle='modal' data-bs-target='#checkoutModal'>Checkout</button>";
                } else {
                    echo "<p>Cannot checkout</p>";
                }
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Your cart is empty.</div>
        <?php endif; ?>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="process_payment.php" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="max_qty" id="max_qty_payment" value="">
                    <div class="mb-3">
                        <label for="method" class="form-label">Payment Method</label>
                        <select name="method" id="method" class="form-select" required>
                            <option value="">Select Method</option>
                            <option value="Wish">Wish</option>
                            <option value="OMT">OMT</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_image" class="form-label">Upload Payment Receipt</label>
                        <input type="file" class="form-control" name="payment_image" id="payment_image" accept="image/*"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" readonly class="form-control" name="amount" id="amount"
                            value="<?php echo number_format($cart_total, 2); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" required
                            rows="2"><?php echo htmlspecialchars($user_address); ?></textarea>
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                    <button type="button" class="btn btn-outline-primary" onclick="openDeliveryModal()">Delivery
                        option</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delivery Modal -->
    <div class="modal fade" id="deliveryModal" tabindex="-1" aria-labelledby="deliveryModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <form method="POST" action="process_delivery.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deliveryModalLabel">Delivery Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="max_qty" id="max_qty_delivery" value="">
                    <div class="mb-3">
                        <label for="delivery_name" class="form-label">Full Name</label>
                        <input type="text" name="delivery_name" class="form-control" id="delivery_name"
                            value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_phone" class="form-label">Phone Number</label>
                        <input type="text" name="delivery_phone" class="form-control" id="delivery_phone"
                            value="<?php echo htmlspecialchars($user_phone); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" readonly class="form-control" name="amount" id="delivery_amount"
                            value="<?php echo number_format($cart_total, 2); ?>">

                    </div>
                    <div class="mb-3">
                        <label for="delivery_address" class="form-label">Delivery Address</label>
                        <textarea name="delivery_address" class="form-control" id="delivery_address" rows="2"
                            required><?php echo htmlspecialchars($user_address); ?></textarea>
                    </div>
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <input type="hidden" name="address" value="<?php echo htmlspecialchars($user_address); ?>">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm Delivery</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateAmounts() {
            const quantityInputs = document.querySelectorAll('.quantity-input');
            let total = 0;

            quantityInputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                const price = parseFloat(input.dataset.price);
                total += qty * price;
            });

            // Update cart total in table footer (if you want)
            const cartTotalElement = document.getElementById('cartTotal');
            if (cartTotalElement) {
                cartTotalElement.textContent = total.toFixed(2);
            }

            // Update amount in delivery modal
            const deliveryAmountInput = document.getElementById('delivery_amount');
            if (deliveryAmountInput) {
                deliveryAmountInput.value = total.toFixed(2);
            }

            // Optionally also update payment modal amount if needed
            const paymentAmountInput = document.getElementById('payment_amount');
            if (paymentAmountInput) {
                paymentAmountInput.value = total.toFixed(2);
            }
        }

        // Attach event listeners to quantity inputs
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', updateAmounts);
        });

        // Initial call to set amounts on page load
        updateAmounts();

        function openDeliveryModal() {
            const checkoutModalEl = document.getElementById('checkoutModal');
            const deliveryModalEl = document.getElementById('deliveryModal');
            const checkoutModal = bootstrap.Modal.getInstance(checkoutModalEl) || new bootstrap.Modal(checkoutModalEl);
            const deliveryModal = new bootstrap.Modal(deliveryModalEl);
            updateQuantities();
            document.getElementById('max_qty_delivery').value = document.getElementById('max_qty_payment').value;
            checkoutModal.hide();
            setTimeout(() => { deliveryModal.show(); }, 500);
        }

        function updateQuantities() {
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const quantities = [];
            let total = 0;
            quantityInputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                const price = parseFloat(input.dataset.price);
                const rowTotal = qty * price;
                input.closest('tr').querySelector('.item-total-price').textContent = rowTotal.toFixed(2);
                total += rowTotal;
                quantities.push(qty);
            });
            document.getElementById('cartTotal').textContent = total.toFixed(2);
            document.getElementById('amount').value = total.toFixed(2);
            document.getElementById('max_qty_payment').value = quantities.join(',');
            document.getElementById('max_qty_delivery').value = quantities.join(',');
        }

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', updateQuantities);
        });

        document.querySelectorAll('#checkoutModal form, #deliveryModal form').forEach(form => {
            form.addEventListener('submit', updateQuantities);
        });
    </script>
</body>

</html>