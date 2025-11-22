<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "connect.php";

$user_id = $_SESSION['user_id'];
$q = $conn->prepare("SELECT username FROM users WHERE id = ?");
$q->bind_param("i", $user_id);
$q->execute();
$res = $q->get_result();
$user = $res->fetch_assoc();
$username = $user['username'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<style>
.post-card-fb {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
}

.profile-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 18px;
}

.post-content {
    font-size: 16px;
    color: #333;
    white-space: pre-wrap;
}

.post-action {
    cursor: pointer;
    font-size: 15px;
}

.post-action:hover {
    text-decoration: underline;
}

</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <a class="navbar-brand" href="#">
        <i class="fa-solid fa-share-nodes"></i> DashBoard
    </a>

    <div class="ms-auto d-flex align-items-center gap-3">

        <div class="nav-user text-white">
            <i class="fa-solid fa-user-circle fa-lg"></i>
            Welcome, <?php echo ucfirst($username); ?>
        </div>

        <a href="logout.php" class="btn btn-light btn-sm">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</nav>


<div class="container mt-4">

    <div class="card p-3 post-box shadow-sm">
        <h5><i class="fa-solid fa-pencil"></i> Create Post</h5>

        <textarea class="form-control mt-2" id="postContent" placeholder="Write a post..." rows="3"></textarea>

        <div class="d-flex gap-3 mt-3">
            <select class="form-select w-25" id="visibility">
                <option value="0">🌍 Public</option>
                <option value="1">🔒 Private</option>
            </select>

            <button class="btn btn-primary" id="postBtn">
                <i class="fa-solid fa-paper-plane"></i> Post
            </button>
        </div>
    </div>

    <hr>

    <h4><i class="fa-solid fa-bars"></i> Recent Posts</h4>
    <div id="postList"></div>

</div>


<script>
$(document).ready(function () {

    loadPosts();

    $("#postBtn").click(function () {
        let content = $("#postContent").val();
        let visibility = $("#visibility").val();

        $.post("post_controller.php", {
            type: "create_post",
            content: content,
            visibility: visibility
        }, function (res) {

            if (res.status === "success") {
                $("#postContent").val("");
                loadPosts();
            } else {
                alert(res.message);
            }

        }, "json");
    });

    function loadPosts() {
        $.post("post_controller.php", { type: "fetch_posts" }, function (res) {

            $("#postList").html("");

            res.posts.forEach(post => {

            $("#postList").append(`
                <div class="post-card-fb mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <div class="profile-icon">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="ms-2">
                                <b>${post.username}</b><br>
                                <small class="text-muted">${timeFormat(post.created_at)}</small>
                            </div>
                        </div>
                    </div>
                    <div class="post-content mb-2">
                        ${post.content}
                    </div>
                    <hr>
                    <div class="d-flex text-muted gap-4">
                        <div class="post-action"><i class="fa-regular fa-thumbs-up"></i> Like</div>
                        <div class="post-action"><i class="fa-regular fa-comment"></i> Comment</div>
                    </div>

                </div>
                `);
            });

        }, "json");
    }
});
function timeFormat(date) {
    let seconds = Math.floor((new Date() - new Date(date)) / 1000);

    if (seconds < 60) return "just now";
    let minutes = Math.floor(seconds / 60);
    if (minutes < 60) return minutes + " minutes ago";
    let hours = Math.floor(minutes / 60);
    if (hours < 24) return hours + " hours ago";
    let days = Math.floor(hours / 24);
    if (days < 7) return days + " days ago";

    let weeks = Math.floor(days / 7);
    return weeks + " weeks ago";
}

</script>

</body>
</html>
