<?php
header("Content-Type: application/json");
$db = new PDO("sqlite:motivations.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$action = $_REQUEST['action'] ?? '';

if ($action === "list") {
    $stmt = $db->query("SELECT * FROM motivations ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === "add") {
    $text = trim($_POST['text'] ?? '');
    if ($text !== '') {
        $stmt = $db->prepare("INSERT INTO motivations(text) VALUES (?)");
        $stmt->execute([$text]);
    }
    echo json_encode(["success" => true]);
    exit;
}

if ($action === "update") {
    $id = intval($_POST['id']);
    $text = trim($_POST['text']);
    $stmt = $db->prepare("UPDATE motivations SET text=? WHERE id=?");
    $stmt->execute([$text, $id]);
    echo json_encode(["success" => true]);
    exit;
}

if ($action === "delete") {
    $id = intval($_POST['id']);
    $stmt = $db->prepare("DELETE FROM motivations WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "Invalid action"]);
