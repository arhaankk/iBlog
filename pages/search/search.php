<?php
require_once('../../util/IB.php');
require_once('../../util/blog-card.php');
$app = IB::app();
$db = $app->getClass('IB\Db');
$pdo = $db->connect();

// Initialize an array to hold the WHERE clause conditions
$whereConditions = [];
$params = [];

// Build the SQL query dynamically based on provided filters
if (isset($_GET['id'])) {
    $whereConditions[] = "b.id = :id";
    $params[':id'] = $_GET['id'];
}

if (isset($_GET['title'])) {
    $whereConditions[] = "LOWER(b.title) LIKE :title";
    $params[':title'] = '%' . strtolower($_GET['title']) . '%';
}

if (isset($_GET['content'])) {
    $whereConditions[] = "LOWER(b.content) LIKE :content";
    $params[':content'] = '%' . strtolower($_GET['content']) . '%';
}

if (isset($_GET['date'])) {
    $whereConditions[] = "DATE(b.created_at) = :date";
    $params[':date'] = $_GET['date'];
}

if (isset($_GET['hasImage'])) {
    $whereConditions[] = "EXISTS (SELECT 1 FROM postImages pi WHERE pi.postId = b.id)";
} else {
    $whereConditions[] = "NOT EXISTS (SELECT 1 FROM postImages pi WHERE pi.postId = b.id)";
}

if (isset($_GET['userId'])) {
    $whereConditions[] = "b.userId = :userId";
    $params[':userId'] = $_GET['userId'];
}

// Construct the base SQL query
$sql = "
    SELECT b.*  , u.username AS authorName
    FROM blog b
    JOIN users u ON b.userId = u.id
";
$page = $app->getClass('IB\Page');
$page->setTitle('Search Posts');
$page->setDescription('Search iBlog for relevant posts');
$page->addCrumb('Search Posts', '{{PAGES}}/search/search.php');
$page->preamble();
?>
<link rel="stylesheet" href="<?php echo $page->data('pages'); ?>style.css">

<main>
    <h1>Search Posts</h1>

    <p>Explore all the posts that iBlog has to offer!</p>

    <hr>
    <h2>Query</h2>
    <div id="search-card" class="card--medium shadow p-4">
        <form id="searchForm">
            <div class="row g-3">
                <!-- Text Search -->
                <div class="col-md-6">
                    <label for="titleSearch" class="form-label">Search in Title</label>
                    <input type="text" class="form-control" id="titleSearch" placeholder="Enter text">
                </div>
                <div class="col-md-6">
                    <label for="contentSearch" class="form-label">Search in Content</label>
                    <input type="text" class="form-control" id="contentSearch" placeholder="Enter text">
                </div>

                <!-- Date Range -->
                <div class="col-md-6">
                    <label for="dateFilter" class="form-label">Filter by Date</label>
                    <input type="date" class="form-control" id="dateFilter">
                </div>

                <!-- Has Image -->
<!--                        todo make tick and text in a row instead of in column-->
                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="hasImage">
                        <label class="form-check-label mb-0" for="hasImage">Has Image</label>
                    </div>
                </div>

                <!-- User ID -->
                <div class="col-md-6">
                    <label for="userIdFilter" class="form-label">Filter by User ID</label>
                    <input type="number" class="form-control" id="userIdFilter" placeholder="Enter User ID">
                </div>

<!--                        todo make it into a row instead of column-->
                <div class="col-12">
                    <button type="submit" class="btn button--active w-100">Apply Filters</button>
                    <button type="reset" class="btn button--active w-100">Reset Filters</button>
                </div>
            </div>
        </form>
    </div>

    <hr>
    <h2>Results</h2>
    <div id="searchResults" class="card--medium p-4">
        <?php
        if (sizeof($_GET) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
            // Execute the query
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Fetch the results
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Output the results
            if (empty($posts)) {
                echo "<p>No posts match the provided criteria.</p>";
            } else {
                foreach ($posts as $post) {
                    echo generatePostHtml($post, $pdo, false);
                    echo "<br>";
                }
            }
        } else {
            echo "<p>No criteria provided.</p>";
        }
        ?>
    </div>
</main>

<!-- Custom JS -->
<script src="script.js"></script>

<?php $page->epilogue(); ?>
