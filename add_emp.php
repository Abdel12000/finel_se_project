<?php
session_start();
if (!isset($_SESSION["admin_email"])) {
    header("location:login.php");
    exit;
}

include("common/config.php");

$eq_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$message = "";
$equipment_name = "";

$eq_name_q = mysqli_query($connect, "SELECT name FROM equipment WHERE equipment_id = $eq_id LIMIT 1");
if ($eq_data = mysqli_fetch_assoc($eq_name_q)) {
    $equipment_name = mysqli_real_escape_string($connect, $eq_data['name']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["selected_employees"]) && $eq_id > 0) {
        $selected_employees = $_POST["selected_employees"];
        $assigned = false;

        foreach ($selected_employees as $employee_id) {
            $employee_id = (int) $employee_id;
            $employee_res = mysqli_query($connect, "SELECT username, phone FROM users WHERE user_id = $employee_id LIMIT 1");
            $employee = mysqli_fetch_assoc($employee_res);
            $employee_name = mysqli_real_escape_string($connect, $employee['username']);
            $employee_phone = mysqli_real_escape_string($connect, $employee['phone']);

            $emp_msg = "You have been assigned to equipment: $equipment_name.";
            $emp_response = "Please proceed with the assignment.";
            $emp_msg_escaped = mysqli_real_escape_string($connect, $emp_msg);

            $emp_feedback_q = mysqli_query($connect, "SELECT 1 FROM feedback WHERE user_id = $employee_id AND message = '$emp_msg_escaped' LIMIT 1");
            if (mysqli_num_rows($emp_feedback_q) == 0) {
                mysqli_query($connect, "INSERT INTO feedback (user_id, message, admin_response) VALUES ($employee_id, '$emp_msg_escaped', '" . mysqli_real_escape_string($connect, $emp_response) . "')");
            }

            $booking_result = mysqli_query($connect, "SELECT DISTINCT user_id FROM equipment_bookings WHERE equipment_id = $eq_id");
            while ($booking = mysqli_fetch_assoc($booking_result)) {
                $client_id = (int) $booking['user_id'];

                $client_msg = "An employee has been assigned to your equipment: $equipment_name.";
                $client_response = "Employee Name: $employee_name, Phone: $employee_phone assigned.";
                $client_msg_escaped = mysqli_real_escape_string($connect, $client_msg);

                $client_feedback_q = mysqli_query($connect, "SELECT 1 FROM feedback WHERE user_id = $client_id AND message = '$client_msg_escaped' LIMIT 1");
                if (mysqli_num_rows($client_feedback_q) == 0) {
                    mysqli_query($connect, "INSERT INTO feedback (user_id, message, admin_response) VALUES ($client_id, '$client_msg_escaped', '$client_response')");
                }

                $assign_check = mysqli_query($connect, "SELECT 1 FROM assignments WHERE employee_id = $employee_id AND equipment_id = $eq_id AND user_id = $client_id LIMIT 1");
                if (mysqli_num_rows($assign_check) == 0) {
                    mysqli_query($connect, "INSERT INTO assignments (employee_id, equipment_id, user_id) VALUES ($employee_id, $eq_id, $client_id)");
                }
            }

            $assigned = true;
        }

        if ($assigned) {
            $message = "Employee(s) assigned and notified.";
        }
    }

    if (isset($_POST['user_id'], $_POST['base_salary'], $_POST['bonus'], $_POST['deduction'], $_POST['total_salary'], $_POST['salary_month'], $_POST['payment_date'])) {
        $user_id = (int) $_POST['user_id'];
        $base_salary = (float) $_POST['base_salary'];
        $bonus = (float) $_POST['bonus'];
        $deduction = (float) $_POST['deduction'];
        $total_salary = (float) $_POST['total_salary'];
        $salary_month = mysqli_real_escape_string($connect, $_POST['salary_month']);
        $payment_date = mysqli_real_escape_string($connect, $_POST['payment_date']);

        $salary_count_q = mysqli_query($connect, "SELECT COUNT(*) AS total FROM employee_salary WHERE user_id = $user_id AND salary_month = '$salary_month'");
        $salary_count = mysqli_fetch_assoc($salary_count_q)['total'];

        if ($salary_count == 0) {
            $insert_sql = "INSERT INTO employee_salary (user_id, base_salary, bonus, deductions, total_salary, salary_month, payment_date) 
                           VALUES ($user_id, $base_salary, $bonus, $deduction, $total_salary, '$salary_month', '$payment_date')";
            if (mysqli_query($connect, $insert_sql)) {
                $message = "Salary record added successfully.";
            } else {
                $message = "Error adding salary record: " . mysqli_error($connect);
            }
        } else {
            $message = "Salary already exists for this user in $salary_month.";
        }
    }
}

$assigned_employees = [];
$res_assigned = mysqli_query($connect, "SELECT DISTINCT employee_id FROM assignments WHERE equipment_id = $eq_id");
while ($row = mysqli_fetch_assoc($res_assigned)) {
    $assigned_employees[] = (int) $row['employee_id'];
}

$employees = mysqli_query($connect, "SELECT user_id, username, email FROM users WHERE role = 2");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <link rel="stylesheet" href="css/custom.css">
</head>
<body>
<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="navbar-header">
                <a href="#" class="navbar-brand"><strong class="text-primary">Dark</strong><strong>Admin</strong></a>
            </div>
            <div class="right-menu list-inline no-margin-bottom">
                <div class="list-inline-item logout">
                    <a id="logout" href="logout.php" class="nav-link">Logout <i class="icon-logout"></i></a>
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="d-flex align-items-stretch">
    <?php include("common/admin_nav.php"); ?>

    <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <h2 class="h5 no-margin-bottom">Dashboard</h2>
            </div>
        </div>

        <section class="no-padding-top no-padding-bottom">
            <div class="container-fluid mt-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Assign Employee</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                        <form method="post" action="">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($emp = mysqli_fetch_assoc($employees)) {
                                            $disabled = in_array((int) $emp['user_id'], $assigned_employees);
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_employees[]"
                                                           value="<?= $emp['user_id'] ?>" <?= $disabled ? 'disabled' : '' ?>>
                                                </td>
                                                <td><?= htmlspecialchars($emp['username']) ?></td>
                                                <td><?= htmlspecialchars($emp['email']) ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary">Assign Selected Employees</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <div class="card">
            <div class="card-header">
                <h4>Assignment List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Email</th>
                            <th>Client Name</th>
                            <th>Client Email</th>
                            <th>Equipment</th>
                            <th>Salary Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $assignment_q = mysqli_query($connect, "
                            SELECT 
                                emp.user_id AS employee_id,
                                emp.username AS employee_name,
                                emp.email AS employee_email,
                                cli.username AS client_name,
                                cli.email AS client_email,
                                e.name AS equipment_name
                            FROM assignments a
                            JOIN users emp ON a.employee_id = emp.user_id
                            JOIN users cli ON a.user_id = cli.user_id
                            JOIN equipment e ON a.equipment_id = e.equipment_id
                            WHERE a.equipment_id = $eq_id
                        ");
                        while ($row = mysqli_fetch_assoc($assignment_q)) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                                <td><?= htmlspecialchars($row['employee_email']) ?></td>
                                <td><?= htmlspecialchars($row['client_name']) ?></td>
                                <td><?= htmlspecialchars($row['client_email']) ?></td>
                                <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-info"
                                       onclick="showSalaryForm(<?= $row['employee_id'] ?>)">Add Salary</a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="container mt-5" id="salaryFormContainer" style="display: none;">
            <h2>Add Salary Record</h2>
            <form method="post" action="" id="salaryForm">
                <div class="mb-3">
                    <label for="user_id">Select Employee</label>
                    <select id="user_id" name="user_id" class="form-select" required>
                        <option value="" disabled selected>-- Select Employee --</option>
                        <?php
                        mysqli_data_seek($employees, 0);
                        while ($emp = mysqli_fetch_assoc($employees)) { ?>
                            <option value="<?= $emp['user_id'] ?>"><?= htmlspecialchars($emp['username']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="base_salary">Base Salary</label>
                    <input type="number" step="0.01" id="base_salary" name="base_salary" class="form-control" required oninput="calculateTotal()">
                </div>
                <div class="mb-3">
                    <label for="bonus">Bonus</label>
                    <input type="number" step="0.01" id="bonus" name="bonus" class="form-control" value="0" oninput="calculateTotal()">
                </div>
                <div class="mb-3">
                    <label for="deduction">Deduction</label>
                    <input type="number" step="0.01" id="deduction" name="deduction" class="form-control" value="0" oninput="calculateTotal()">
                </div>
                <div class="mb-3">
                    <label for="total_salary">Total Salary</label>
                    <input type="number" step="0.01" id="total_salary" name="total_salary" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="salary_month">Salary Month</label>
                    <input type="month" id="salary_month" name="salary_month" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="payment_date">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Add Salary</button>
            </form>
        </div>
    </div>
</div>

<script>
    function calculateTotal() {
        var base = parseFloat(document.getElementById('base_salary').value) || 0;
        var bonus = parseFloat(document.getElementById('bonus').value) || 0;
        var deduction = parseFloat(document.getElementById('deduction').value) || 0;
        document.getElementById('total_salary').value = (base + bonus - deduction).toFixed(2);
    }

    function showSalaryForm(userId) {
        document.getElementById('salaryFormContainer').style.display = 'block';
        document.getElementById('user_id').value = userId;
        document.getElementById('salaryFormContainer').scrollIntoView({ behavior: 'smooth' });
    }
</script>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
