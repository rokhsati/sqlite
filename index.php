<?php
// Handle errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to get SQLite files in the current directory
function getSQLiteFiles() {
    $files = glob('*.{sqlite,db,sqlite3}', GLOB_BRACE);
    return array_filter($files, function($file) {
        return is_file($file);
    });
}

// Function to get tables from SQLite database
function getTables($dbFile) {
    try {
        $db = new PDO("sqlite:$dbFile");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        return $tables;
    } catch (PDOException $e) {
        return ["error" => "Error connecting to database: " . $e->getMessage()];
    }
}

// Function to get columns from a table
function getColumns($dbFile, $table) {
    try {
        $db = new PDO("sqlite:$dbFile");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $result = $db->query("PRAGMA table_info('$table')");
        $columns = $result->fetchAll(PDO::FETCH_ASSOC);
        return $columns;
    } catch (PDOException $e) {
        return ["error" => "Error retrieving columns: " . $e->getMessage()];
    }
}

// Function to execute SQL query
function executeQuery($dbFile, $query) {
    try {
        $db = new PDO("sqlite:$dbFile");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if query is SELECT
        $isSelect = preg_match('/^\s*SELECT/i', trim($query));
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        if ($isSelect) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ["type" => "select", "data" => $results];
        } else {
            $affectedRows = $stmt->rowCount();
            return ["type" => "non-select", "message" => "Query executed successfully. Affected rows: $affectedRows"];
        }
    } catch (PDOException $e) {
        return ["type" => "error", "message" => "Query execution error: " . $e->getMessage()];
    }
}

// Handle form submissions
$selectedDb = $_POST['db_file'] ?? '';
$selectedTable = $_POST['table'] ?? '';
$query = $_POST['query'] ?? '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($query) && !empty($selectedDb)) {
    $result = executeQuery($selectedDb, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQLite Database Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
        }
        .container {
            max-width: 1200px;
            margin-top: 20px;
        }
        .card {
            background-color: #252526;
            border: 1px solid #3c3c3c;
        }
        .card-header {
            background-color: #2d2d2d;
            color: #d4d4d4;
            font-weight: 500;
            font-size: 16px;
        }
        .form-control, .form-select {
            background-color: #3c3c3c;
            color: #d4d4d4;
            border: 1px solid #555;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            background-color: #3c3c3c;
            color: #d4d4d4;
            border-color: #0078d4;
            box-shadow: 0 0 0 0.2rem rgba(0, 120, 212, 0.25);
        }
        .btn-primary {
            background-color: #0078d4;
            border-color: #0078d4;
            font-size: 14px;
        }
        .btn-primary:hover {
            background-color: #005ba1;
            border-color: #005ba1;
        }
        .table {
            color: #d4d4d4;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .list-group-item {
            background-color: #3c3c3c;
            color: #d4d4d4;
            border-color: #555;
            font-size: 14px;
        }
        .alert {
            font-size: 14px;
        }
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">SQLite Database Manager</h1>

        <div class="row g-3">
            <!-- Database selection -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Select Database</div>
                    <div class="card-body">
                        <form method="post" id="dbForm">
                            <select name="db_file" class="form-select mb-3" onchange="this.form.submit()">
                                <option value="">Choose a database</option>
                                <?php foreach (getSQLiteFiles() as $file): ?>
                                    <option value="<?= htmlspecialchars($file) ?>" <?= $selectedDb === $file ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($file) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table selection -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Tables</div>
                    <div class="card-body">
                        <?php if ($selectedDb): ?>
                            <form method="post" id="tableForm">
                                <input type="hidden" name="db_file" value="<?= htmlspecialchars($selectedDb) ?>">
                                <select name="table" class="form-select mb-3" onchange="this.form.submit()">
                                    <option value="">Choose a table</option>
                                    <?php
                                    $tables = getTables($selectedDb);
                                    if (isset($tables['error'])) {
                                        echo "<div class='alert alert-danger'>{$tables['error']}</div>";
                                    } else {
                                        foreach ($tables as $table) {
                                            echo "<option value='" . htmlspecialchars($table) . "' " . ($selectedTable === $table ? 'selected' : '') . ">" . htmlspecialchars($table) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </form>
                        <?php else: ?>
                            <p class="text-muted">Select a database first</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columns display -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Columns</div>
                    <div class="card-body">
                        <?php if ($selectedDb && $selectedTable): ?>
                            <?php
                            $columns = getColumns($selectedDb, $selectedTable);
                            if (isset($columns['error'])) {
                                echo "<div class='alert alert-danger'>{$columns['error']}</div>";
                            } else {
                                echo "<ul class='list-group'>";
                                foreach ($columns as $column) {
                                    echo "<li class='list-group-item'>" . htmlspecialchars($column['name']) . " (" . htmlspecialchars($column['type']) . ")</li>";
                                }
                                echo "</ul>";
                            }
                            ?>
                        <?php else: ?>
                            <p class="text-muted">Select a table first</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Query input and execution -->
        <div class="card mt-4">
            <div class="card-header">Execute Query</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="db_file" value="<?= htmlspecialchars($selectedDb) ?>">
                    <input type="hidden" name="table" value="<?= htmlspecialchars($selectedTable) ?>">
                    <div class="mb-3">
                        <textarea name="query" class="form-control" rows="5" placeholder="Write your query here..."><?= htmlspecialchars($query) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Execute Query</button>
                </form>
            </div>
        </div>

        <!-- Query results -->
        <?php if ($result): ?>
            <div class="card mt-4">
                <div class="card-header">Query Results</div>
                <div class="card-body">
                    <?php if ($result['type'] === 'select'): ?>
                        <?php if (count($result['data']) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($result['data'][0]) as $column): ?>
                                                <th><?= htmlspecialchars($column) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['data'] as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $value): ?>
                                                    <td><?= htmlspecialchars($value) ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No records found</p>
                        <?php endif; ?>
                    <?php elseif ($result['type'] === 'non-select'): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($result['message']) ?></div>
                    <?php else: ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($result['message']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
