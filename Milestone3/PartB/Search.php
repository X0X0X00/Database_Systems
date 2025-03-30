<?php
header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = 'localhost';
$username = 'zzh133';
$password = 'rzwzbeFi';
$dbname = 'zzh133_1';    

try {
    $mysqli = new mysqli($servername, $username, $password, $dbname);

    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    $mysqli->set_charset("utf8mb4");

    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = $_GET['search'];
        $searchTermWithWildcards = '%' . $mysqli->real_escape_string($_GET['search']) . '%';
        
        $sql = "SELECT b.*, 
                    TRIM(CONCAT(
                        COALESCE(a.firstname, ''),
                        CASE WHEN a.middlename IS NOT NULL THEN CONCAT(' ', a.middlename) ELSE '' END,
                        ' ',
                        COALESCE(a.lastname, '')
                    )) as author_name,
                    YEAR(a.birth_day) as birth_year,
                    c.C_name as category_name
                FROM Book b
                LEFT JOIN Author a ON CAST(b.author AS UNSIGNED) = a.a_id
                LEFT JOIN Category c ON CAST(b.category_id AS UNSIGNED) = c.C_id
                WHERE b.bno = ? 
                   OR b.b_name LIKE ? 
                   OR b.ISBN LIKE ?
                   OR a.firstname LIKE ?
                   OR a.lastname LIKE ?
                   OR CONCAT(a.firstname, ' ', a.lastname) LIKE ?";
        $params = [$searchTerm, $searchTermWithWildcards, $searchTermWithWildcards, 
                  $searchTermWithWildcards, $searchTermWithWildcards, $searchTermWithWildcards];
        $types = "ssssss";
    } else {
        $sql = "SELECT b.*, 
                    TRIM(CONCAT(
                        COALESCE(a.firstname, ''),
                        CASE WHEN a.middlename IS NOT NULL THEN CONCAT(' ', a.middlename) ELSE '' END,
                        ' ',
                        COALESCE(a.lastname, '')
                    )) as author_name,
                    YEAR(a.birth_day) as birth_year,
                    c.C_name as category_name
                FROM Book b
                LEFT JOIN Author a ON CAST(b.author AS UNSIGNED) = a.a_id
                LEFT JOIN Category c ON CAST(b.category_id AS UNSIGNED) = c.C_id
                LIMIT 10";
        $params = [];
        $types = "";
    }

    if ($stmt = $mysqli->prepare($sql)) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $books = $result->fetch_all(MYSQLI_ASSOC);

            if (count($books) > 0) {
                foreach ($books as $book) {
                    $coverPath = 'pic/' . $book['bno'] . '.jpeg';
                    
                    // 构建作者和分类信息
                    $authorInfo = trim($book['author_name']);
                    if (!empty($book['birth_year'])) {
                        $authorInfo .= ' ' . $book['birth_year'];
                    }
                    if (!empty($book['category_name'])) {
                        $authorInfo .= ' (' . $book['category_name'] . ')';
                    }
                    ?>
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="<?php echo htmlspecialchars($coverPath); ?>" 
                                 alt="Cover for <?php echo htmlspecialchars($book['b_name']); ?>" 
                                 class="book-cover-img"
                                 onerror="this.src='pic/default.jpeg'">
                        </div>
                        <div class="book-details">
                            <h2 class="book-title"><?php echo htmlspecialchars($book['b_name']); ?></h2>
                            
                            <p class="book-author">
                                <?php echo htmlspecialchars($authorInfo); ?>
                            </p>

                            <div class="book-stats">
                                <span class="stat-item">Total Copies: <?php echo htmlspecialchars($book['total']); ?></span>
                                <span class="stat-item">Available: <?php echo htmlspecialchars($book['stock']); ?></span>
                                <?php if (!empty($book['location'])): ?>
                                    <span class="stat-item">Location: <?php echo htmlspecialchars($book['location']); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($book['description'])): ?>
                            <p class="book-description">
                                <?php echo htmlspecialchars($book['description']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="no-results">No books found matching your search criteria.</div>';
            }
        } else {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        $stmt->close();
    } else {
        throw new Exception("Query preparation failed: " . $mysqli->error);
    }

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo '<div class="error-message">An error occurred while accessing the database: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

$mysqli->close();
?>

<style>
/* 样式保持不变 */
.book-card {
    display: flex;
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.book-cover {
    flex: 0 0 100px;
    margin-right: 20px;
}

.book-cover-img {
    width: 100px;
    height: 150px;
    border-radius: 4px;
    box-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    object-fit: cover;
}

.book-details {
    flex: 1;
}

.book-title {
    font-size: 24px;
    margin: 0 0 8px 0;
    color: #333;
}

.book-author {
    font-size: 16px;
    color: #666;
    margin: 0 0 16px 0;
}

.book-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 16px;
}

.stat-item {
    color: #666;
    font-size: 14px;
}

.book-description {
    font-size: 14px;
    line-height: 1.6;
    color: #555;
    margin: 0;
}

.no-results {
    text-align: center;
    padding: 40px;
    color: #666;
}

.error-message {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 12px;
    border-radius: 4px;
    margin: 20px;
}
</style>