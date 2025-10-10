<?php
include'dbConfig.php'; // পাথ ঠিক রাখো

$name = $phone = $email = "";
$name_err = $phone_err = $email_err = "";
$id = 0;
$is_edit = false;

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $DB_con->prepare("DELETE FROM delivery_men WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: index.php?page=delivery_men");
    exit;
}

if (isset($_GET['edit_id'])) {
    $is_edit = true;
    $id = (int)$_GET['edit_id'];
    $stmt = $DB_con->prepare("SELECT * FROM delivery_men WHERE id = ?");
    $stmt->execute([$id]);
    $dm = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($dm) {
        $name = $dm['name'];
        $phone = $dm['phone'];
        $email = $dm['email'];
    } else {
        header("Location: index.php?page=delivery_men");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (empty($name)) $name_err = "Name is required.";
    if (empty($phone)) $phone_err = "Phone is required.";
    if (empty($email)) $email_err = "Email is required.";

    if (!$name_err && !$phone_err && !$email_err) {
        if ($id > 0) {
            $stmt = $DB_con->prepare("UPDATE delivery_men SET name = ?, phone = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $email, $id]);
        } else {
            $stmt = $DB_con->prepare("INSERT INTO delivery_men (name, phone, email) VALUES (?, ?, ?)");
            $stmt->execute([$name, $phone, $email]);
        }
        header("Location: index.php?page=delivery_men");
        exit;
    }
}

$stmt = $DB_con->prepare("SELECT * FROM delivery_men ORDER BY id DESC");
$stmt->execute();
$delivery_men = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Delivery Men Management</h2>

<div class="mb-4 border p-3 rounded bg-light">
    <h4><?= $is_edit ? "Edit Delivery Man" : "Add New Delivery Man" ?></h4>
    <form method="POST" action="index.php?page=delivery_men">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>">
            <?php if ($name_err): ?><small class="text-danger"><?= $name_err ?></small><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>">
            <?php if ($phone_err): ?><small class="text-danger"><?= $phone_err ?></small><?php endif; ?>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>">
            <?php if ($email_err): ?><small class="text-danger"><?= $email_err ?></small><?php endif; ?>
        </div>
        <button type="submit" class="btn btn-success"><?= $is_edit ? "Update" : "Add" ?></button>
        <?php if ($is_edit): ?>
            <a href="index.php?page=delivery_men" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
</div>

<h4>Delivery Men List</h4>
<table class="table table-bordered">
    <thead class="thead-dark">
        <tr>
            <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($delivery_men): ?>
            <?php foreach ($delivery_men as $dm): ?>
                <tr>
                    <td><?= $dm['id'] ?></td>
                    <td><?= htmlspecialchars($dm['name']) ?></td>
                    <td><?= htmlspecialchars($dm['phone']) ?></td>
                    <td><?= htmlspecialchars($dm['email']) ?></td>
                    <td>
                        <a href="index.php?page=delivery_men&edit_id=<?= $dm['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="index.php?page=delivery_men&delete_id=<?= $dm['id'] ?>" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">No delivery men found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
