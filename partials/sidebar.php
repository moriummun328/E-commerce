<?php
$catStmt = $DB_con->query("SELECT id, category_name FROM categories ORDER BY category_name");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<aside class="left-sidebar p-3 bg-light border rounded shadow-sm">
    <div class="sticky-sidebar">
        <h5 class="mb-3 font-weight-bold text-dark">
            <i class="bi bi-funnel mr-1"></i> Filter by Category
        </h5>
        <div id="catBox" class="list-group">
            <?php foreach($categories as $c): ?>
                <label class="list-group-item d-flex align-items-center">
                    <input type="checkbox" class="cat-check mr-2" value="<?= (int)$c['id']?>" name="cat_name">
                    <span><?= htmlspecialchars($c['category_name']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <button id="clearFilter" class="btn btn-sm btn-outline-danger mt-3 w-100">
            <i class="bi bi-x-circle mr-1"></i> Clear Filter
        </button>
    </div>
</aside>

<style>
.left-sidebar {
    max-width: 100px;
    height: 50%;
}
.sticky-sidebar {
    position: sticky;
    top: 20px;
}
.list-group-item {
    border: none;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background 0.2s ease;
    font-size: 14px;
}
.list-group-item:hover {
    background: #f8f9fa;
    border-radius: 5px;
}
.cat-check {
    cursor: pointer;
}
</style>
