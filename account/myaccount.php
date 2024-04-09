<?php
session_start();
require_once '../db_connect.php'; // Database connection

// Check if the user is logged in as a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'teacher') {
    // If the user is not logged in as a teacher, redirect them to the login page
    header("Location: ../index.php");
    exit();
}

// Fetch the teacher ID from the session
$teacher_id = $_SESSION['user_id'];

// Controleer of het verzoek een POST-verzoek is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controleer of alle vereiste velden zijn ingevuld
    if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
        // Haal de ingevoerde waarden op uit het formulier
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Controleer of het nieuwe wachtwoord overeenkomt met het bevestigde wachtwoord
        if ($new_password === $confirm_password) {
            // Haal het opgeslagen wachtwoord op uit de database
            $stmt = $pdo->prepare("SELECT password FROM teachers WHERE id = ?");
            $stmt->execute([$teacher_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Verifieer het huidige wachtwoord
                if ($current_password === $result['password']) {
                    // Wijzig het wachtwoord
                    $update_stmt = $pdo->prepare("UPDATE teachers SET password = ? WHERE id = ?");
                    $update_stmt->execute([$new_password, $teacher_id]);
                    echo "Wachtwoord succesvol gewijzigd.";
                } else {
                    echo "Huidig wachtwoord is onjuist.";
                }
            } else {
                echo "Docent niet gevonden.";
            }
        } else {
            echo "Nieuwe wachtwoorden komen niet overeen.";
        }
    } else {
        echo "Vul alle vereiste velden in.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>My Account</title>
    <link rel="stylesheet" type="text/css" href="myaccount.css">
</head>
<body>
    <div class="container">
        <h1>My Account</h1>
        <form action="myaccount.php" method="POST">
            <div class="form-group">
                <label for="current_password">Huidig wachtwoord:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nieuw wachtwoord:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Bevestig nieuw wachtwoord:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="button">Wijzig Wachtwoord</button>
        </form>
        <!-- Placeholder voor berichten na het wijzigen van het wachtwoord -->
        <div class="message">
            <!-- Berichten na het wijzigen van het wachtwoord zullen hier verschijnen -->
        </div>
    </div>
</body>
</html>
