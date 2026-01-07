<?php
require_once '../config/db.php';
session_start();

if (isset($_POST['query'])) {
    $search = "%" . trim($_POST['query']) . "%";
    $roleCondition = "";
    $params = [':search' => $search];

    if ($_SESSION['role'] !== 'admin') {
        $roleCondition = "AND p.department = :dept";
        $params[':dept'] = $_SESSION['department'];
    }

    $sql = "SELECT p.*, c.name as company_name 
            FROM persons p 
            LEFT JOIN companies c ON p.company_id = c.id 
            WHERE (p.name LIKE :search OR p.surname LIKE :search OR c.name LIKE :search) 
            AND p.is_active = 1 
            $roleCondition 
            LIMIT 20";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    if ($results) {
        foreach ($results as $row) {
            // Admin veya kendi departmanı ise butonları göster
            $buttons = "";
            if($_SESSION['role'] == 'admin' || $row['department'] == $_SESSION['department']){
                $buttons = "<a href='edit_person.php?id={$row['id']}' class='btn btn-warning btn-sm'><i class='fas fa-edit'></i></a> 
                            <a href='delete_person.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Emin misin?\")'><i class='fas fa-trash'></i></a>";
            }

            echo "<tr>
                    <td>" . htmlspecialchars($row['name'] . ' ' . $row['surname']) . "</td>
                    <td>" . htmlspecialchars($row['company_name']) . "</td>
                    <td>" . htmlspecialchars($row['phone']) . "</td>
                    <td><span class='badge bg-info'>" . htmlspecialchars($row['department']) . "</span></td>
                    <td>" . htmlspecialchars($row['full_address']) . "</td>
                    <td>$buttons</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>Kayıt bulunamadı.</td></tr>";
    }
}
?>